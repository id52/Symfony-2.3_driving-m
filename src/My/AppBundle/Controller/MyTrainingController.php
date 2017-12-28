<?php

namespace My\AppBundle\Controller;

use Doctrine\ORM\Query;
use My\AppBundle\Entity\ExamLog;
use My\AppBundle\Entity\FinalExamLog;
use My\AppBundle\Entity\SliceLog;
use My\AppBundle\Entity\Subject;
use My\AppBundle\Entity\ThemeTestLog;
use My\AppBundle\Exception\AppResponseException;
use My\AppBundle\Util\Time;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MyTrainingController extends MyAbstract
{
    /** @var $version \My\AppBundle\Entity\TrainingVersion */
    protected $version;

    public function init()
    {
        $cntxt = $this->get('security.context');

        if (!$cntxt->isGranted('ROLE_USER_PAID3')) {
            if ($cntxt->isGranted('ROLE_USER_PAID2')) {
                $limit = clone $this->user->getPayment2Paid();
                $limit->add(new \DateInterval('P'.$this->settings['access_time_after_2_payment'].'D'));
                if ($limit < new \DateTime()) {
                    $view = $this->render('AppBundle:My:training_without_3_payment.html.twig', array(
                        'settings' => $this->settings,
                    ));
                    throw new AppResponseException($view);
                }
            }
        }

        parent::init();

        $this->version = $this->em->getRepository('AppBundle:TrainingVersion')->getVersionByUser($this->user);
        if (!$this->version) {
            throw $this->createNotFoundException('Training version not found.');
        }
    }

    public function trainingsAction()
    {
        $subjects_repository = $this->em->getRepository('AppBundle:Subject');
        $subjects = $subjects_repository->findAllAsArray($this->user, $this->version);

        $is_passed = true;
        foreach ($subjects as $subject) {
            if (!$subject['is_passed']) {
                $is_passed = false;
            }
        }

        $is_passed_full = $this->em->getRepository('AppBundle:FinalExamLog')->isPassed($this->user);

        $message = $this->settings['close_final_exam_text'];

        $placeholders = [];
        $placeholders['{{ last_name }}'] = $this->user->getLastName();
        $placeholders['{{ first_name }}'] = $this->user->getFirstName();
        $placeholders['{{ patronymic }}'] = $this->user->getPatronymic();
        if ($this->user->getSex() == 'male') {
            $placeholders['{{ dear }}'] = 'Уважаемый';
        } else {
            $placeholders['{{ dear }}'] = 'Уважаемая';
        }
        for ($i = 1; $i <= 5; $i ++) {
            $placeholders['{{ sign_'.$i.' }}'] = $this->settings['sign_'.$i];
        }

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $message);

        return $this->render('AppBundle:My:trainings.html.twig', array(
            'subjects'       => $subjects,
            'is_passed'      => $is_passed,
            'is_passed_full' => $is_passed_full,
            'closed_message' => $message,
        ));
    }

    public function trainingAction($id)
    {
        $subject = $this->em->getRepository('AppBundle:Subject')->find($id);
        if (!$subject) {
            throw $this->createNotFoundException('Subject for id "'.$id.'" not found.');
        }

        $message = '';
        if ($this->subjectIsClosed($subject)) {
            $message = $this->settings['closed_subject_text'];
            $placeholders = [];
            $placeholders['{{ last_name }}'] = $this->user->getLastName();
            $placeholders['{{ first_name }}'] = $this->user->getFirstName();
            $placeholders['{{ patronymic }}'] = $this->user->getPatronymic();
            if ($this->user->getSex() == 'male') {
                $placeholders['{{ dear }}'] = 'Уважаемый';
            } else {
                $placeholders['{{ dear }}'] = 'Уважаемая';
            }
            for ($i = 1; $i <= 5; $i ++) {
                $placeholders['{{ sign_'.$i.' }}'] = $this->settings['sign_'.$i];
            }

            $message = str_replace(array_keys($placeholders), array_values($placeholders), $message);
        }

        $slices = $this->em->getRepository('AppBundle:Slice')->createQueryBuilder('s')
            ->leftJoin('s.after_theme', 'at')->addSelect('at')
            ->leftJoin('at.subject', 'ats')->addSelect('ats')
            ->leftJoin('s.logs', 'sl', 'WITH', 'sl.user = :user AND sl.passed = :passed')->addSelect('sl')
            ->setParameters(array(':user' => $this->user, ':passed' => true))
            ->leftJoin('s.versions', 'v')
            ->andWhere('v = :version')->setParameter(':version', $this->version)
            ->getQuery()->execute();

        $slices_grouping = array();
        foreach ($slices as $slice) { /** @var $slice \My\AppBundle\Entity\Slice */
            $subject_id = $slice->getAfterTheme()->getSubject()->getId();
            if ($subject_id == $subject->getId()) {
                $slices_grouping[$slice->getAfterTheme()->getId()] = array(
                    'type'   => 'slice',
                    'object' => $slice,
                    'passed' => (count($slice->getLogs()) > 0),
                );
            }
        }

        $training = array();

        /** @CAUTION NativeQuery */
        $rsm = new Query\ResultSetMappingBuilder($this->em);
        $rsm->addRootEntityFromClassMetadata('AppBundle:Theme', 't');
        $rsm->addScalarResult('reader', 'reader');
        $query = $this->em->createNativeQuery('
            SELECT t.*, r.reader_id reader
            FROM themes t
            LEFT JOIN themes_readers r ON t.id = r.theme_id AND r.reader_id = :user_id
            LEFT JOIN training_versions_themes tvt ON t.id = tvt.theme_id
            WHERE t.subject_id = :subject_id AND tvt.version_id = :version_id
            GROUP BY t.id
            ORDER BY t.position ASC
        ', $rsm);
        $query->setParameters(array(
            ':user_id'    => $this->user->getId(),
            ':subject_id' => $subject->getId(),
            ':version_id' => $this->version->getId(),
        ));
        $themes = $query->getArrayResult();

        $themes_cnt = 0;
        $read_themes_cnt = 0;
        $prev_theme = false;
        $prev_theme_read = false;
        foreach ($themes as $theme) {
            $theme_read = (bool) $theme['reader'];
            $theme = $theme[0];
            $training[] = array(
                'type'             => 'theme',
                'object'           => $theme,
                'reading'          => $theme_read,
                'questions_access' => (!$theme_read && (!$prev_theme || $prev_theme_read)),
            );
            $prev_theme = true;
            $prev_theme_read = $theme_read;
            $themes_cnt ++;
            $read_themes_cnt += ($theme_read ? 1 : 0);
            if (isset($slices_grouping[$theme['id']])) {
                $slice = $slices_grouping[$theme['id']];
                $slice['active'] = $themes_cnt == $read_themes_cnt;
                $training[] = $slice;
                if (!$slice['passed']) {
                    $prev_theme_read = false;
                }
            }
        }

        $slices_cnt = 0;
        $passed_slices_cnt = 0;
        $themes_cnt = 0;
        $read_themes_cnt = 0;
        foreach ($training as $object) {
            switch ($object['type']) {
                case 'theme':
                    $themes_cnt++;
                    $read_themes_cnt += ($object['reading'] ? 1 : 0);
                    break;
                case 'slice':
                    $slices_cnt++;
                    $passed_slices_cnt += ($object['passed'] ? 1 : 0);
                    break;
            }
        }

        $exams_logs_repository = $this->em->getRepository('AppBundle:ExamLog');

        $training[] = array(
            'type'   => 'exam',
            'active' => $slices_cnt == $passed_slices_cnt && $themes_cnt == $read_themes_cnt,
            'passed' => $exams_logs_repository->isPassed($subject, $this->user),
        );

        $exam = array(
            'active' => $slices_cnt == $passed_slices_cnt && $themes_cnt == $read_themes_cnt,
            'passed' => $exams_logs_repository->isPassed($subject, $this->user),
        );

        $notfull = $this->settings['notfull_text'];
        $notfull = str_replace('{{ last_name }}', $this->user->getLastName(), $notfull);
        $notfull = str_replace('{{ first_name }}', $this->user->getFirstName(), $notfull);
        $notfull = str_replace('{{ patronymic }}', $this->user->getPatronymic(), $notfull);

        return $this->render('AppBundle:My:training.html.twig', array(
            'training'          => $training,
            'exam'              => $exam,
            'subject'           => $subject,
            'notfull'           => $notfull,
            'closed_message'    => $message,
            'subject_is_closed' => $this->subjectIsClosed($subject),
        ));
    }

    public function trainingThemeAction($id)
    {
        $themes_repository = $this->em->getRepository('AppBundle:Theme');

        $theme = $themes_repository->find($id);
        if (!$theme) {
            throw $this->createNotFoundException('Theme for id "'.$id.'" not found.');
        }

        $questions_access = false;

        $prev_theme = $themes_repository->createQueryBuilder('t')
            ->andWhere('t.position < :position')->setParameter(':position', $theme->getPosition())
            ->andWhere('t.subject = :subject')->setParameter(':subject', $theme->getSubject())
            ->leftJoin('t.versions', 'v')
            ->andWhere('v = :version')->setParameter(':version', $this->version)
            ->addOrderBy('t.position', 'DESC')
            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
        if (!$themes_repository->isReaderExists($theme, $this->user)
            && (!$prev_theme || $themes_repository->isReaderExists($prev_theme, $this->user))
        ) {
            $questions_access = true;

            $slices_logs_repository = $this->em->getRepository('AppBundle:SliceLog');
            $slices = $this->em->getRepository('AppBundle:Slice')->createQueryBuilder('s')
                ->leftJoin('s.after_theme', 't')
                ->andWhere('t.subject = :subject')->setParameter(':subject', $theme->getSubject())
                ->andWhere('t.position < :position')->setParameter(':position', $theme->getPosition())
                ->leftJoin('s.versions', 'v')
                ->andWhere('v = :version')->setParameter(':version', $this->version)
                ->addOrderBy('t.position')
                ->getQuery()->execute();
            foreach ($slices as $slice) {
                if (!$slices_logs_repository->isPassed($slice, $this->user)) {
                    $questions_access = false;
                }
            }
        }

        $text = $theme->getText();

        $categories = array(
            '1_who'       => 'Кто',
            '2_what'      => 'Что',
            '3_where'     => 'Где',
            '4_action'    => 'Действие',
            '5_condition' => 'Условие',
        );
        $flash_blocks = $this->em->getRepository('AppBundle:FlashBlock')->findAll();
        foreach ($flash_blocks as $block) {
            $qb = $this->em->getRepository('AppBundle:FlashBlockItem')->createQueryBuilder('fbi');
            $qb->andWhere('fbi.block = :block')->setParameter(':block', $block);
            if ($block->getIsSimple()) {
                $qb->orderBy('fbi.position');
                $view = $this->renderView('AppBundle:My:_flash_block_simple.html.twig', array(
                    'block' => $block,
                    'items' => $qb->getQuery()->execute(),
                ));
            } else {
                $qb->orderBy('fbi.category, fbi.lft');
                $items = $qb->getQuery()->execute();

                $items_abc = array();
                foreach ($items as $item) { /** @var $item \My\AppBundle\Entity\FlashBlockItem */
                    $first_char = mb_substr($item->getTitle(), 0, 1, 'utf8');
                    if (!isset($items_abc[$first_char])) {
                        $items_abc[$first_char] = array();
                    }
                    $items_abc[$first_char][] = $item;
                }
                uksort($items_abc, function ($a, $b) {
                    return collator_compare(collator_create('ru_RU'), $a, $b);
                });

                $items_cat = array();
                foreach ($items as $item) { /** @var $item \My\AppBundle\Entity\FlashBlockItem */
                    $cat = $item->getCategory();
                    if (isset($categories[$cat])) {
                        if (!isset($items_cat[$cat])) {
                            $items_cat[$cat] = array();
                        }
                        if ($item->getParent()) {
                            $items_cat[$cat][$item->getParent()->getId()]['children'][] = $item;
                        } else {
                            $items_cat[$cat][$item->getId()] = array(
                                'item'     => $item,
                                'children' => array(),
                            );
                        }
                    }
                }

                $view = $this->renderView('AppBundle:My:_flash_block.html.twig', array(
                    'block'      => $block,
                    'items_abc'  => $items_abc,
                    'items_cat'  => $items_cat,
                    'categories' => $categories,
                ));
            }
            $text = str_replace('{{ flash_block_'.$block->getKey().' }}', $view, $text);
        }

        return $this->render('AppBundle:My:training_theme.html.twig', array(
            'theme'            => $theme,
            'text'             => $text,
            'questions_access' => $questions_access,
        ));
    }

    public function getFlashBlockItemAjaxAction(Request $request)
    {
        $result = array();

        if ($request->isXmlHttpRequest()) {
            $item = $this->em->find('AppBundle:FlashBlockItem', $request->get('id'));
            if ($item) {
                $result['title'] = $item->getTitle();
                $result['text'] = $item->getText();
            }
        }

        return new JsonResponse($result);
    }

    public function trainingThemeTestAction(Request $request, $id)
    {
        if ($return = $this->checkPermissions()) {
            return $return;
        }

        $themes_repository = $this->em->getRepository('AppBundle:Theme');

        $theme = $themes_repository->find($id);
        if (!$theme) {
            throw $this->createNotFoundException('Theme for id "'.$id.'" not found.');
        }

        $questions_access = false;

        $prev_theme = $themes_repository->createQueryBuilder('t')
            ->andWhere('t.position < :position')->setParameter(':position', $theme->getPosition())
            ->andWhere('t.subject = :subject')->setParameter(':subject', $theme->getSubject())
            ->leftJoin('t.versions', 'v')
            ->andWhere('v = :version')->setParameter(':version', $this->version)
            ->addOrderBy('t.position', 'DESC')
            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
        if (!$themes_repository->isReaderExists($theme, $this->user)
            && (!$prev_theme || $themes_repository->isReaderExists($prev_theme, $this->user))
        ) {
            $questions_access = true;

            $slices_logs_repository = $this->em->getRepository('AppBundle:SliceLog');
            $slices = $this->em->getRepository('AppBundle:Slice')->createQueryBuilder('s')
                ->leftJoin('s.after_theme', 'at')
                ->andWhere('at.position < :position')->setParameter(':position', $theme->getPosition())
                ->andWhere('at.subject = :subject')->setParameter(':subject', $theme->getSubject())
                ->leftJoin('s.versions', 'v')
                ->andWhere('v = :version')->setParameter(':version', $this->version)
                ->getQuery()->execute();
            foreach ($slices as $slice) {
                if (!$slices_logs_repository->isPassed($slice, $this->user)) {
                    $questions_access = false;
                }
            }
        }
        if (!$questions_access) {
            throw $this->createNotFoundException('You don\'t have access to the theme with id "'.$id.'".');
        }

        $session = $request->getSession();
        $s_name = 'theme_'.$theme->getId();
        $s_data = $session->get($s_name);
        $log_id = ($s_data && isset($s_data['log_id'])) ? $s_data['log_id'] : 0;

        $log = $this->em->getRepository('AppBundle:ThemeTestLog')->find($log_id);

        if (!$log) {
            $questions = $theme->getQuestionsIdsArray($this->version);
            shuffle($questions);
            $answers = count($questions) ? array_fill(0, count($questions), null) : array();

            $num = 0;

            $time    = new \DateTime();
            $endTime = null;
            if ($this->settings['theme_test_time'] > 0) {
                $endTime = clone $time;
                $endTime->add(new \DateInterval('PT'.$this->settings['theme_test_time'].'M'));
            }

            $log = new ThemeTestLog();
            $log->setStartedAt($time);
            $log->setQuestions($questions);
            $log->setAnswers($answers);
            $log->setUser($this->user);
            $log->setTheme($theme);
            $this->em->persist($log);
            $this->em->flush();

            $s_data = [
                'questions'  => $questions,
                'answers'    => $answers,
                'started_at' => $time,
                'end_time'   => $endTime,
                'log_id'     => $log->getId(),
                'current'    => $num,
                'comments'   => true,
                'l_activity' => $time,
            ];

            $session = $request->getSession();
            $session->set($s_name, $s_data);
        } else {
            $questions = $s_data['questions'];
            $answers = $s_data['answers'];
            $num = $s_data['current'];
        }

        $activity_limit = new \DateTime();
        $activity_limit->sub(new \DateInterval('PT2H'));
        if ($s_data['l_activity'] < $activity_limit) {
            $log->setEndedAt(new \DateTime());
            $this->em->persist($log);
            $this->em->flush();

            $session->remove($s_name);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => 'longtime'));
            } else {
                return $this->render('AppBundle:My:training_theme_test_longtime.html.twig', array('theme' => $theme));
            }
        }
        $s_data['l_activity'] = new \DateTime();
        $session->set($s_name, $s_data);

        if ($s_data['end_time'] && $s_data['end_time'] < new \DateTime()) {
            $log->setEndedAt(new \DateTime());
            $this->em->persist($log);
            $this->em->flush();

            $session->remove($s_name);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => 'timeout'));
            } else {
                return $this->render('AppBundle:My:training_theme_test_timeout.html.twig', array('theme' => $theme));
            }
        }

        if (!isset($questions[$num])) {
            $keys = array_keys($questions);
            if ($num > end($keys) || count($keys) == 0) {
                foreach ($answers as $key => $value) {
                    $count_corrects = 0;
                    foreach ($value as $answer) {
                        if ($answer['correct']) {
                            $count_corrects ++;
                        } else {
                            $count_corrects = 0;
                        }
                    }
                    if ($count_corrects >= $this->settings['theme_test_correct_answers']) {
                        unset($questions[$key]);
                    }
                }

                if ('shuffle' == $this->settings['theme_test_questions_method']) {
                    $old_questions = $questions;
                    $questions = array();
                    $keys = array_keys($old_questions);
                    shuffle($keys);
                    foreach ($keys as $key) {
                        $questions[$key] = $old_questions[$key];
                    }
                }

                $s_data['questions'] = $questions;
                $session->set($s_name, $s_data);

                if (count($questions) > 0) {
                    $keys = array_keys($questions);
                    $num = reset($keys);
                    $s_data['current'] = $num;
                    $session->set($s_name, $s_data);
                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(array('refresh' => true));
                    } else {
                        return $this->redirect($this->generateUrl('my_training_theme_test', array(
                            'id' => $theme->getId(),
                        )));
                    }
                } else {
                    if (!$themes_repository->isReaderExists($theme, $this->user)) {
                        $theme->addReader($this->user);
                        $this->em->persist($theme);
                        $this->em->flush();
                    }

                    $log->setEndedAt(new \DateTime());
                    $log->setPassed(true);
                    $this->em->persist($log);
                    $this->em->flush();

                    $session->remove($s_name);

                    $next_theme = $this->em->getRepository('AppBundle:Theme')->createQueryBuilder('t')
                        ->andWhere('t.position > :position')->setParameter(':position', $theme->getPosition())
                        ->andWhere('t.subject = :subject')->setParameter(':subject', $theme->getSubject())
                        ->leftJoin('t.versions', 'v')
                        ->andWhere('v = :version')->setParameter(':version', $this->version)
                        ->addOrderBy('t.position')
                        ->setMaxResults(1)->getQuery()->getOneOrNullResult();

                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(array('complete' => true));
                    } else {
                        return $this->render('AppBundle:My:training_theme_test_complete.html.twig', array(
                            'theme'      => $theme,
                            'next_theme' => $next_theme,
                        ));
                    }
                }
            } else {
                $message = 'Question for number "'.$num.'"';
                $message .= ' in theme for id "'.$id.'"';
                $message .= ' not found.';
                throw $this->createNotFoundException($message);
            }
        }

        $question = $this->em->getRepository('AppBundle:Question')->find($questions[$num]);
        if (!$question) {
            throw $this->createNotFoundException('Question for id "'.$questions[$num].'" not found.');
        }

        $correct_all = array_fill(1, $this->settings['theme_test_correct_answers'], 0);
        $correct_this = 0;
        $correct_this_in_row = 0;
        foreach ($answers as $key => $answers_q) {
            $correct_q = 0;
            foreach ((array) $answers_q as $answer) {
                if ($answer['correct']) {
                    $correct_q ++;
                    $correct_all[$correct_q] ++;
                    if ($key == $num) {
                        $correct_this ++;
                        $correct_this_in_row ++;
                    }
                } else {
                    if ($correct_q > 0) {
                        for ($i = $correct_q; $i > 0; $i --) {
                            $correct_all[$i] --;
                        }
                        $correct_q = 0;
                    }
                    if ($this->settings['theme_test_correct_answers_in_row']) {
                        if ($key == $num) {
                            $correct_this_in_row = 0;
                        }
                    }
                }
            }
        }

        /** @var $endTime \DateTime */
        $endTime = $s_data['end_time'];

        if ($request->isMethod('post')) {
            $c_answer = $request->get('answer');
            $q_answers = $question->getAnswers();

            if (isset($q_answers[$c_answer])) {
                if (!$s_data['answers'][$num]) {
                    $s_data['answers'][$num] = array($q_answers[$c_answer]);
                } else {
                    $s_data['answers'][$num][] = $q_answers[$c_answer];
                }
            }

            reset($questions);
            while (key($questions) !== $num) {
                next($questions);
            }
            next($questions);
            $new_num = key($questions);
            if (is_null($new_num)) {
                $new_num = count($answers);
            }
            $s_data['current'] = $new_num;
            $session->set($s_name, $s_data);

            $log->setAnswers($s_data['answers']);
            $this->em->persist($log);
            $this->em->flush();

            $is_correct = isset($q_answers[$c_answer]) && $q_answers[$c_answer]['correct'];
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array(
                    'correct'  => $is_correct,
                    'c_answer' => $c_answer,
                    'comment'  => (!$is_correct && $s_data['comments']) ? $question->getDescription() : '',
                ));
            } else {
                $keys = array_keys('shuffle' == $this->settings['theme_test_questions_method'] ? $questions : $answers);
                $params = [
                    'theme'                  => $theme,
                    'num'                    => $num,
                    'answers'                => $answers,
                    'keys'                   => $keys,
                    'seconds_left'           => Time::getDiffInSeconds($endTime),
                    'is_comment'             => $s_data['comments'],
                    'is_shuffle'             => 'shuffle' == $this->settings['theme_test_questions_method'],
                    'correct_answers'        => $this->settings['theme_test_correct_answers'],
                    'correct_answers_in_row' => $this->settings['theme_test_correct_answers_in_row'],
                    'correct_all'            => $correct_all,
                    'correct_this'           => $correct_this,
                    'correct_this_in_row'    => $correct_this_in_row,
                ];
                if ($is_correct) {
                    return $this->render('AppBundle:My:training_theme_question_success.html.twig', $params);
                } else {
                    $params['question'] = $question;

                    return $this->render('AppBundle:My:training_theme_question_error.html.twig', $params);
                }
            }
        }

        $q_answers = $question->getAnswers();
        if ($this->settings['theme_test_shuffle_answers']) {
            $keys = array_keys($q_answers);
            shuffle($keys);
            $new_q_answers = array();
            foreach ($keys as $key) {
                $new_q_answers[$key] = $q_answers[$key];
            }
            $q_answers = $new_q_answers;
        }

        $keys = array_keys('shuffle' == $this->settings['theme_test_questions_method'] ? $questions : $answers);
        $params = [
            'theme'                  => $theme,
            'num'                    => $num,
            'answers'                => $answers,
            'keys'                   => $keys,
            'question'               => $question,
            'seconds_left'           => Time::getDiffInSeconds($endTime),
            'is_comment'             => $s_data['comments'],
            'is_shuffle'             => 'shuffle' == $this->settings['theme_test_questions_method'],
            'correct_answers'        => $this->settings['theme_test_correct_answers'],
            'correct_answers_in_row' => $this->settings['theme_test_correct_answers_in_row'],
            'correct_all'            => $correct_all,
            'correct_this'           => $correct_this,
            'correct_this_in_row'    => $correct_this_in_row,
            'q_answers'              => $q_answers,
        ];
        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView('AppBundle:My:training_theme_question_in.html.twig', $params);

            return new JsonResponse(array('content' => $content));
        } else {
            return $this->render('AppBundle:My:training_theme_question.html.twig', $params);
        }
    }

    public function trainingThemeTestResetAction($id)
    {
        $cntxt = $this->get('security.context');
        if (!$cntxt->isGranted('ROLE_TESTER_TRAINING')) {
            throw $this->createNotFoundException('Access denied.');
        }

        $theme = $this->em->getRepository('AppBundle:Theme')->find($id);
        if (!$theme) {
            throw $this->createNotFoundException('Theme for id "'.$id.'" not found.');
        }

        $this->em->getRepository('AppBundle:ThemeTestLog')->createQueryBuilder('ttl')
            ->delete()
            ->andWhere('ttl.theme = :theme')->setParameter(':theme', $theme)
            ->andWhere('ttl.user = :user')->setParameter(':user', $this->user)
            ->getQuery()->execute();

        $theme->removeReader($this->user);
        $this->em->persist($theme);
        $this->em->flush();

        return $this->redirect($this->generateUrl('my_training', array('id' => $theme->getSubject()->getId())));
    }

    public function trainingThemeTestQuitAction(Request $request, $id)
    {
        $theme = $this->em->getRepository('AppBundle:Theme')->find($id);
        if (!$theme) {
            throw $this->createNotFoundException('Theme for id "'.$id.'" not found.');
        }

        $session = $request->getSession();
        $s_name = 'theme_'.$theme->getId();
        $session->remove($s_name);

        return $this->redirect($this->generateUrl('my_training', array('id' => $theme->getSubject()->getId())));
    }

    public function trainingThemeTestCommentAction(Request $request, $id)
    {
        $theme = $this->em->getRepository('AppBundle:Theme')->find($id);
        if (!$theme) {
            throw $this->createNotFoundException('Theme for id "'.$id.'" not found.');
        }

        $session = $request->getSession();
        $s_name = 'theme_'.$theme->getId();
        $s_data = $session->get($s_name);
        $s_data['comments'] = !$s_data['comments'];
        $session->set($s_name, $s_data);

        return $this->redirect($this->generateUrl('my_training_theme_test', array('id' => $theme->getId())));
    }

    public function trainingThemeTestPassAction($id)
    {
        $cntxt = $this->get('security.context');
        if (!($cntxt->isGranted('ROLE_TESTER_TRAINING')
            or ($cntxt->isGranted('ROLE_USER_PAID2') and $this->container->getParameter('is_test'))
        )) {
            throw $this->createNotFoundException('Access denied.');
        }

        $theme = $this->em->getRepository('AppBundle:Theme')->find($id);
        if (!$theme) {
            throw $this->createNotFoundException('Theme for id "'.$id.'" not found.');
        }

        $theme->removeReader($this->user);
        $theme->addReader($this->user);
        $this->em->persist($theme);
        $this->em->flush();

        return $this->redirect($this->generateUrl('my_training', array('id' => $theme->getSubject()->getId())));
    }

    public function trainingSliceAction(Request $request, $id)
    {
        if ($return = $this->checkPermissions()) {
            return $return;
        }

        $slice = $this->em->getRepository('AppBundle:Slice')->find($id);
        if (!$slice) {
            throw $this->createNotFoundException('Slice for id "'.$id.'" not found.');
        }

        $slice_access = true;

        /** @CAUTION NativeQuery */
        $rsm = new Query\ResultSetMappingBuilder($this->em);
        $rsm->addRootEntityFromClassMetadata('AppBundle:Theme', 't');
        $rsm->addScalarResult('reader', 'reader');
        $query = $this->em->createNativeQuery('
            SELECT t.*, r.reader_id reader
            FROM themes t
            LEFT JOIN themes_readers r ON t.id = r.theme_id AND r.reader_id = :user_id
            LEFT JOIN training_versions_themes tvt ON t.id = tvt.theme_id
            WHERE t.subject_id = :subject_id AND t.position <= :position AND tvt.version_id = :version_id
            GROUP BY t.id
        ', $rsm);
        $query->setParameters(array(
            ':user_id'    => $this->user->getId(),
            ':subject_id' => $slice->getAfterTheme()->getSubject()->getId(),
            ':position'   => $slice->getAfterTheme()->getPosition(),
            ':version_id' => $this->version->getId(),
        ));
        $themes = $query->getResult();

        foreach ($themes as $theme) {
            if (!$theme['reader']) {
                $slice_access = false;
            }
        }
        if ($slice_access) {
            $slices_logs_repository = $this->em->getRepository('AppBundle:SliceLog');
            if ($slices_logs_repository->isPassed($slice, $this->user)) {
                $slice_access = false;
            }
        }
        if (!$slice_access) {
            throw $this->createNotFoundException('Slice for id "'.$id.'" is not available.');
        }

        $session = $request->getSession();
        $s_name = 'slice_'.$slice->getId();
        $s_data = $session->get($s_name);
        $log_id = ($s_data && isset($s_data['log_id'])) ? $s_data['log_id'] : 0;

        $log = $this->em->getRepository('AppBundle:SliceLog')->find($log_id);
        if (!$log) {
            $orig_questions = array();
            foreach ($themes as $theme) {
                /** @var $theme \My\AppBundle\Entity\Theme */
                $theme = $theme[0];
                $orig_questions[] = $theme->getQuestionsIdsArray($this->version);
            }

            $questions_tickets = $this->settings['slice_tickets'];
            $questions_limit = $this->settings['slice_questions_in_ticket'];
            $current_questions = $orig_questions;
            $questions = array();
            for ($i = 0; $i < $questions_tickets; $i ++) {
                $questions[$i] = array();

                $current_questions_count = count($current_questions, COUNT_RECURSIVE) - count($current_questions);

                if ($current_questions_count > 0) {
                    foreach ($current_questions as $j => $q) {
                        $get_questions = floor(count($q) * $questions_limit / $current_questions_count);
                        if ($get_questions > 0) {
                            if ($get_questions > count($q)) {
                                $get_questions = count($q);
                            }
                            $keys = (array) array_rand($q, $get_questions);
                            $questions[$i] = array_merge(
                                $questions[$i],
                                array_intersect_key($q, array_fill_keys($keys, null))
                            );
                            foreach ($keys as $k) {
                                unset($current_questions[$j][$k]);
                            }
                        }
                    }

                    $add_questions = $questions_limit - count($questions[$i]);
                    if ($add_questions > 0) {
                        $current_questions_united = array();
                        foreach ($current_questions as $q) {
                            $current_questions_united = array_merge($current_questions_united, $q);
                        }
                        if ($add_questions > count($current_questions_united)) {
                            $add_questions = count($current_questions_united);
                        }
                        if ($add_questions > 0) {
                            $questions[$i] = array_merge($questions[$i], array_intersect_key(
                                $current_questions_united,
                                array_fill_keys((array) array_rand($current_questions_united, $add_questions), null)
                            ));
                        }
                    }

                    shuffle($questions[$i]);
                }

                if (count($questions[$i]) == 0) {
                    unset($questions[$i]);
                }

                if (!$this->settings['slice_not_repeat_questions_in_tickets']) {
                    $current_questions = $orig_questions;
                }
            }

            $answers = array();
            foreach ($questions as $i => $q) {
                if (count($q) > 0) {
                    $answers[$i] = array_fill(0, count($q), null);
                } else {
                    $answers[$i] = array();
                }
            }

            $num = 0;
            $ticket = 0;

            $time = new \DateTime();
            $ticket_end_time = null;
            if ($this->settings['slice_ticket_time'] > 0) {
                $ticket_end_time = clone $time;
                $ticket_end_time->add(new \DateInterval('PT'.$this->settings['slice_ticket_time'].'M'));
            }

            $log = new SliceLog();
            $log->setStartedAt($time);
            $log->setQuestions($questions);
            $log->setAnswers($answers);
            $log->setUser($this->user);
            $log->setSlice($slice);
            $this->em->persist($log);
            $this->em->flush();

            $s_data = array(
                'questions'       => $questions,
                'answers'         => $answers,
                'started_at'      => $time,
                'ticket_end_time' => $ticket_end_time,
                'log_id'          => $log->getId(),
                'current'         => $num,
                'current_ticket'  => $ticket,
                'l_activity'      => $time,
            );

            $session = $request->getSession();
            $session->set($s_name, $s_data);
        } else {
            $questions = $s_data['questions'];
            $answers = $s_data['answers'];
            $num = $s_data['current'];
            $ticket = $s_data['current_ticket'];
        }

        $activity_limit = new \DateTime();
        $activity_limit->sub(new \DateInterval('PT2H'));
        if ($s_data['l_activity'] < $activity_limit) {
            $this->sliceEnd($s_name, $log);
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => 'longtime'));
            } else {
                return $this->render('AppBundle:My:training_slice_longtime.html.twig', array(
                    'slice' => $slice,
                ));
            }
        }
        $s_data['l_activity'] = new \DateTime();
        $session->set($s_name, $s_data);

        if (!$s_data['ticket_end_time'] && $this->settings['slice_ticket_time'] > 0) {
            $ticket_end_time = new \DateTime();
            $ticket_end_time->add(new \DateInterval('PT'.$this->settings['slice_ticket_time'].'M'));
            $s_data['ticket_end_time'] = $ticket_end_time;
            $session->set($s_name, $s_data);
        }

        if ($s_data['ticket_end_time'] && $s_data['ticket_end_time'] < new \DateTime()) {
            $this->sliceEnd($s_name, $log);
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => 'timeout'));
            } else {
                return $this->render('AppBundle:My:training_slice_timeout.html.twig', array(
                    'slice' => $slice,
                ));
            }
        }

        $errors = 0;
        $max_errors = $this->settings['slice_max_errors_in_ticket'];
        if ($max_errors > 0) {
            foreach ($answers[$ticket] as $q) {
                foreach ((array) $q as $a) {
                    if (!$a['correct']) {
                        $errors ++;
                    }
                }
            }
            if ($errors >= $max_errors) {
                $this->sliceEnd($s_name, $log);
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array('error' => 'max_errors'));
                } else {
                    return $this->render('AppBundle:My:training_slice_max_errors.html.twig', array(
                        'slice' => $slice,
                    ));
                }
            }
        }

        if (!isset($questions[$ticket][$num])) {
            $keys = array_keys($questions[$ticket]);
            if ($num > end($keys)) {
                if ($ticket + 1 < count($answers)) {
                    $this->sliceNextTicket($s_name);
                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(array('refresh' => true));
                    } else {
                        return $this->redirect($this->generateUrl('my_training_slice', array(
                            'id' => $slice->getId(),
                        )));
                    }
                } else {
                    $this->sliceEnd($s_name, $log, true);

                    /** @var $slices_logs_repository \My\AppBundle\Repository\SliceLogRepository */
                    $slices_logs_repository = $this->em->getRepository('AppBundle:SliceLog');

                    $slices = $this->em->getRepository('AppBundle:Slice')->createQueryBuilder('s')
                        ->leftJoin('s.after_theme', 't')
                        ->andWhere('t.subject = :subject')
                        ->setParameter(':subject', $slice->getAfterTheme()->getSubject())
                        ->leftJoin('s.versions', 'v')
                        ->andWhere('v = :version')->setParameter(':version', $this->version)
                        ->getQuery()->execute();
                    $exam_access = true;
                    foreach ($slices as $s) {
                        if (!$slices_logs_repository->isPassed($s, $this->user)) {
                            $exam_access = false;
                        }
                    }
                    if ($exam_access) {
                        $notify = $this->get('app.notify');
                        $notify->sendAfterAllSlices($this->user, $slice->getAfterTheme()->getSubject());
                    }

                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(array('complete' => true));
                    } else {
                        return $this->render('AppBundle:My:training_slice_complete.html.twig', array(
                            'slice' => $slice,
                        ));
                    }
                }
            } else {
                $message = 'Question for number "'.$num.'"';
                $message .= ' in ticket for "'.$ticket.'"';
                $message .= ' in slice for id "'.$id.'"';
                $message .= ' not found.';
                throw $this->createNotFoundException($message);
            }
        }

        $question = $this->em->getRepository('AppBundle:Question')->find($questions[$ticket][$num]);
        if (!$question) {
            throw $this->createNotFoundException('Question for id "'.$questions[$ticket][$num].'" not found.');
        }

        if ($request->isMethod('post')) {
            $c_answer = $request->get('answer');
            $q_answers = $question->getAnswers();

            if (isset($q_answers[$c_answer])) {
                if (!$s_data['answers'][$ticket][$num]) {
                    $s_data['answers'][$ticket][$num] = array($q_answers[$c_answer]);
                } else {
                    $s_data['answers'][$ticket][$num][] = $q_answers[$c_answer];
                }
            }

            reset($questions[$ticket]);
            while (key($questions[$ticket]) !== $num) {
                next($questions[$ticket]);
            }
            next($questions[$ticket]);
            $new_num = key($questions[$ticket]);
            if (is_null($new_num)) {
                $new_num = count($answers[$ticket]);
            }
            $s_data['current'] = $new_num;
            $session->set($s_name, $s_data);

            $log->setAnswers($s_data['answers']);
            $this->em->persist($log);
            $this->em->flush();

            $is_correct = isset($q_answers[$c_answer]) && $q_answers[$c_answer]['correct'];
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array(
                    'correct'  => $is_correct,
                    'c_answer' => $c_answer,
                    'errors'   => $is_correct ? $errors : ($errors + 1),
                ));
            } else {
                $params = [
                    'slice'        => $slice,
                    'num'          => $num,
                    'ticket'       => $ticket,
                    'answers'      => $answers,
                    'seconds_Left' => Time::getDiffInSeconds($s_data['ticket_end_time']),
                ];
                if ($is_correct) {
                    return $this->render('AppBundle:My:training_slice_success.html.twig', $params);
                } else {
                    return $this->render('AppBundle:My:training_slice_error.html.twig', $params);
                }
            }
        }

        $q_answers = $question->getAnswers();
        if ($this->settings['slice_shuffle_answers']) {
            $keys = array_keys($q_answers);
            shuffle($keys);
            $new_q_answers = array();
            foreach ($keys as $key) {
                $new_q_answers[$key] = $q_answers[$key];
            }
            $q_answers = $new_q_answers;
        }

        $params = [
            'slice'        => $slice,
            'num'          => $num,
            'ticket'       => $ticket,
            'answers'      => $answers,
            'question'     => $question,
            'seconds_left' => Time::getDiffInSeconds($s_data['ticket_end_time']),
            'q_answers'    => $q_answers,
            'errors'       => $errors,
            'max_errors'   => $max_errors,
        ];

        if ($request->get('next')) {
            $content = $this->renderView('AppBundle:My:training_slice_in.html.twig', $params);

            return new JsonResponse(array('content' => $content));
        } else {
            return $this->render('AppBundle:My:training_slice.html.twig', $params);
        }
    }

    protected function sliceNextTicket($s_name)
    {
        $session = $this->getRequest()->getSession();
        $s_data = $session->get($s_name);

        $s_data['ticket_end_time'] = null;
        $s_data['current_ticket'] += 1;
        $s_data['current'] = 0;

        $session->set($s_name, $s_data);
    }

    protected function sliceEnd($s_name, SliceLog $log, $is_passed = false)
    {
        $log->setEndedAt(new \DateTime());
        $log->setPassed($is_passed);
        $this->em->persist($log);
        $this->em->flush();

        $session = $this->getRequest()->getSession();
        $session->remove($s_name);
    }

    public function trainingSliceResetAction($id)
    {
        $cntxt = $this->get('security.context');
        if (!$cntxt->isGranted('ROLE_TESTER_TRAINING')) {
            throw $this->createNotFoundException('Access denied.');
        }

        /** @var $slice \My\AppBundle\Entity\Slice */
        $slice = $this->em->getRepository('AppBundle:Slice')->find($id);
        if (!$slice) {
            throw $this->createNotFoundException('Slice for id "'.$id.'" not found.');
        }

        $this->em->getRepository('AppBundle:SliceLog')->createQueryBuilder('sl')
            ->delete()
            ->andWhere('sl.slice = :slice')->setParameter(':slice', $slice)
            ->andWhere('sl.user = :user')->setParameter(':user', $this->user)
            ->getQuery()->execute();

        return $this->redirect($this->generateUrl('my_training', array(
            'id' => $slice->getAfterTheme()->getSubject()->getId(),
        )));
    }

    public function trainingSliceQuitAction(Request $request, $id)
    {
        $slice = $this->em->getRepository('AppBundle:Slice')->find($id);
        if (!$slice) {
            throw $this->createNotFoundException('Slice for id "'.$id.'" not found.');
        }

        $session = $request->getSession();
        $s_name = 'slice_'.$slice->getId();
        $session->remove($s_name);

        return $this->redirect($this->generateUrl('my_training', array(
            'id' => $slice->getAfterTheme()->getSubject()->getId(),
        )));
    }

    public function trainingSlicePassAction($id)
    {
        $cntxt = $this->get('security.context');
        if (!($cntxt->isGranted('ROLE_TESTER_TRAINING')
            or ($cntxt->isGranted('ROLE_USER_PAID2') and $this->container->getParameter('is_test')))
        ) {
            throw $this->createNotFoundException('Access denied.');
        }

        $slice = $this->em->getRepository('AppBundle:Slice')->find($id);
        if (!$slice) {
            throw $this->createNotFoundException('Slice for id "'.$id.'" not found.');
        }

        $time = new \DateTime();

        $log = new SliceLog();
        $log->setUser($this->user);
        $log->setSlice($slice);
        $log->setPassed(true);
        $log->setStartedAt($time);
        $log->setEndedAt($time);
        $this->em->persist($log);
        $this->em->flush();

        return $this->redirect($this->generateUrl('my_training', array(
            'id' => $slice->getAfterTheme()->getSubject()->getId(),
        )));
    }

    public function trainingExamAction(Request $request, $id)
    {
        if ($return = $this->checkPermissions(3)) {
            return $return;
        }

        $subject = $this->em->getRepository('AppBundle:Subject')->find($id);
        if (!$subject) {
            throw $this->createNotFoundException('Subject for id "'.$id.'" not found.');
        }

        if ($this->subjectIsClosed($subject)) {
            throw $this->createNotFoundException('Subject for id "'.$id.'" is closed.');
        }

        $notify = $this->get('app.notify');

        $exam_access = true;

        /** @CAUTION NativeQuery */
        $rsm = new Query\ResultSetMappingBuilder($this->em);
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('reader', 'reader');
        $query = $this->em->createNativeQuery('
            SELECT t.id, r.reader_id reader
            FROM themes t
            LEFT JOIN themes_readers r ON t.id = r.theme_id AND r.reader_id = :user_id
            LEFT JOIN training_versions_themes tvt ON t.id = tvt.theme_id
            WHERE t.subject_id = :subject_id AND tvt.version_id = :version_id
            GROUP BY t.id
        ', $rsm);
        $query->setParameters(array(
            ':user_id'    => $this->user->getId(),
            ':subject_id' => $subject->getId(),
            ':version_id' => $this->version->getId(),
        ));
        $themes = $query->getArrayResult();

        $themes_ids = array();
        foreach ($themes as $theme) {
            if (!$theme['reader']) {
                $exam_access = false;
            }
            $themes_ids[] = $theme['id'];
        }

        if ($exam_access) {
            $slices = $this->em->getRepository('AppBundle:Slice')->createQueryBuilder('s')
                ->leftJoin('s.logs', 'sl', 'WITH', 'sl.user = :user AND sl.passed = :passed')->addSelect('sl')
                ->setParameters(array(':user' => $this->user, ':passed' => true))
                ->leftJoin('s.versions', 'v')
                ->andWhere('v = :version')->setParameter(':version', $this->version)
                ->andWhere('s.after_theme IN (:themes)')->setParameter(':themes', $themes_ids)
                ->getQuery()->execute();
            foreach ($slices as $slice) { /** @var $slice \My\AppBundle\Entity\Slice */
                if (count($slice->getLogs()) == 0) {
                    $exam_access = false;
                }
            }
        }

        if ($exam_access) {
            $exams_logs_repository = $this->em->getRepository('AppBundle:ExamLog');
            if ($exams_logs_repository->isPassed($subject, $this->user)) {
                $exam_access = false;
            }
        }
        if (!$exam_access) {
            throw $this->createNotFoundException('Exam for subject for id "'.$id.'" is not available.');
        }

        $session = $request->getSession();
        $s_name = 'exam_'.$subject->getId();
        $s_data = $session->get($s_name);
        $log_id = ($s_data && isset($s_data['log_id'])) ? $s_data['log_id'] : 0;

        $log = $this->em->getRepository('AppBundle:ExamLog')->find($log_id);
        if (!$log) {
            /** @var $last_log \My\AppBundle\Entity\ExamLog */
            $last_log = $this->em->getRepository('AppBundle:ExamLog')->createQueryBuilder('el')
                ->andWhere('el.user = :user')->setParameter(':user', $this->user)
                ->andWhere('el.subject = :subject')->setParameter(':subject', $subject)
                ->orderBy('el.started_at', 'DESC')
                ->setMaxResults(1)->getQuery()->getOneOrNullResult();
            if ($last_log && $this->settings['exam_retake_time'] > 0) {
                $retake_time = clone $last_log->getStartedAt();
                $retake_time->add(new \DateInterval('PT'.$this->settings['exam_retake_time'].'H'));
                if ($retake_time > new \DateTime()) {
                    return $this->render('AppBundle:My:training_exam_retake.html.twig', array(
                        'subject' => $subject,
                    ));
                }
            }

            $themes = $this->em->getRepository('AppBundle:Theme')->createQueryBuilder('t')
                ->leftJoin('t.subject', 's')->addSelect('s')
                ->leftJoin('t.questions', 'q')->addSelect('q')
                ->leftJoin('q.image', 'i')->addSelect('i')
                ->leftJoin('t.versions', 'v')
                ->andWhere('v = :version')->setParameter(':version', $this->version)
                ->andWhere('s = :subject')->setParameter(':subject', $subject)
                ->getQuery()->execute();

            $orig_questions = array();
            foreach ($themes as $theme) { /** @var $theme \My\AppBundle\Entity\Theme */
                $orig_questions[] = $theme->getQuestionsIdsArray($this->version);
            }

            $questions_tickets = $this->settings['exam_tickets'];
            $questions_limit = $this->settings['exam_questions_in_ticket'];
            $current_questions = $orig_questions;
            $questions = array();

            for ($i = 0; $i < $questions_tickets; $i ++) {
                $questions[$i] = array();

                $current_questions_count = count($current_questions, COUNT_RECURSIVE) - count($current_questions);
                if ($current_questions_count > 0) {
                    foreach ($current_questions as $j => $q) {
                        $get_questions = floor(count($q) * $questions_limit / $current_questions_count);
                        if ($get_questions > 0) {
                            if ($get_questions > count($q)) {
                                $get_questions = count($q);
                            }
                            $keys = (array) array_rand($q, $get_questions);
                            $questions[$i] = array_merge(
                                $questions[$i],
                                array_intersect_key($q, array_fill_keys($keys, null))
                            );
                            foreach ($keys as $k) {
                                unset($current_questions[$j][$k]);
                            }
                        }
                    }

                    $add_questions = $questions_limit - count($questions[$i]);
                    if ($add_questions > 0) {
                        $current_questions_united = array();
                        foreach ($current_questions as $j => $q) {
                            foreach ($q as $k => $v) {
                                $current_questions_united[$j.'_'.$k] = $v;
                            }
                        }
                        if ($add_questions > count($current_questions_united)) {
                            $add_questions = count($current_questions_united);
                        }
                        if ($add_questions > 0) {
                            $keys = (array) array_rand($current_questions_united, $add_questions);
                            $questions[$i] = array_merge(
                                $questions[$i],
                                array_intersect_key($current_questions_united, array_fill_keys($keys, null))
                            );
                            foreach ($keys as $key) {
                                list($j, $k) = explode('_', $key);
                                unset($current_questions[$j][$k]);
                            }
                        }
                    }

                    if ($this->settings['exam_shuffle']) {
                        shuffle($questions[$i]);
                    }
                }

                if (count($questions[$i]) == 0) {
                    unset($questions[$i]);
                }

                if (!$this->settings['exam_not_repeat_questions_in_tickets']) {
                    $current_questions = $orig_questions;
                }
            }

            $answers = array();
            foreach ($questions as $i => $q) {
                if (count($q) > 0) {
                    $answers[$i] = array_fill(0, count($q), null);
                } else {
                    $answers[$i] = array();
                }
            }

            $num = 0;
            $ticket = 0;

            $time = new \DateTime();
            $ticket_end_time = null;
            if ($this->settings['exam_ticket_time'] > 0) {
                $ticket_end_time = clone $time;
                $ticket_end_time->add(new \DateInterval('PT'.$this->settings['exam_ticket_time'].'M'));
            }

            $log = new ExamLog();
            $log->setStartedAt($time);
            $log->setQuestions($questions);
            $log->setAnswers($answers);
            $log->setUser($this->user);
            $log->setSubject($subject);
            $this->em->persist($log);
            $this->em->flush();

            $s_data = array(
                'questions'       => $questions,
                'answers'         => $answers,
                'started_at'      => $time,
                'ticket_end_time' => $ticket_end_time,
                'log_id'          => $log->getId(),
                'current'         => $num,
                'current_ticket'  => $ticket,
                'l_activity'      => $time,
            );

            $session = $request->getSession();
            $session->set($s_name, $s_data);
        } else {
            $questions = $s_data['questions'];
            $answers = $s_data['answers'];
            $num = $s_data['current'];
            $ticket = $s_data['current_ticket'];
        }

        $activity_limit = new \DateTime();
        $activity_limit->sub(new \DateInterval('PT2H'));
        if ($s_data['l_activity'] < $activity_limit) {
            $this->examEnd($s_name, $log);
            $notify->sendAfterFailExam($this->user, $subject);
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => 'longtime'));
            } else {
                return $this->render('AppBundle:My:training_exam_longtime.html.twig', array(
                    'subject' => $subject,
                ));
            }
        }
        $s_data['l_activity'] = new \DateTime();
        $session->set($s_name, $s_data);

        if (!$s_data['ticket_end_time'] && $this->settings['exam_ticket_time'] > 0) {
            $ticket_end_time = new \DateTime();
            $ticket_end_time->add(new \DateInterval('PT'.$this->settings['exam_ticket_time'].'M'));
            $s_data['ticket_end_time'] = $ticket_end_time;
            $session->set($s_name, $s_data);
        }

        if ($s_data['ticket_end_time'] && $s_data['ticket_end_time'] < new \DateTime()) {
            $this->examEnd($s_name, $log);
            $notify->sendAfterFailExam($this->user, $subject);
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => 'timeout'));
            } else {
                return $this->render('AppBundle:My:training_exam_timeout.html.twig', array(
                    'subject' => $subject,
                ));
            }
        }

        $errors = 0;
        $max_errors = $this->settings['exam_max_errors_in_ticket'];
        if ($max_errors > 0) {
            foreach ($answers[$ticket] as $q) {
                foreach ((array) $q as $a) {
                    if (!$a['correct']) {
                        $errors ++;
                    }
                }
            }
            if ($errors >= $max_errors) {
                $this->examEnd($s_name, $log);
                $notify->sendAfterFailExam($this->user, $subject);
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array('error' => 'max_errors'));
                } else {
                    return $this->render('AppBundle:My:training_exam_max_errors.html.twig', array(
                        'subject' => $subject,
                    ));
                }
            }
        }

        if (!isset($questions[$ticket][$num])) {
            $keys = array_keys($questions[$ticket]);
            if ($num > end($keys)) {
                if ($ticket + 1 < count($answers)) {
                    $this->examNextTicket($s_name);
                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(array('refresh' => true));
                    } else {
                        return $this->redirect($this->generateUrl('my_training_exam', array(
                            'id' => $subject->getId(),
                        )));
                    }
                } else {
                    $this->examEnd($s_name, $log, true);

                    $notify->sendAfterExam($this->user, $subject);

                    $subjects_repository = $this->em->getRepository('AppBundle:Subject');
                    $subjects = $subjects_repository->findAllAsArray($this->user, $this->version);
                    $is_passed = true;
                    foreach ($subjects as $s) {
                        if (!$s['is_passed']) {
                            $is_passed = false;
                        }
                    }
                    if ($is_passed) {
                        sleep(1);
                        $notify->sendAfterAllExams($this->user);
                    }

                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(array('complete' => true));
                    } else {
                        return $this->render('AppBundle:My:training_exam_complete.html.twig', array(
                            'subject' => $subject,
                        ));
                    }
                }
            } else {
                $message = 'Question for number "'.$num.'"';
                $message .= ' in ticket for "'.$ticket.'"';
                $message .= ' in exam subject for id "'.$id.'"';
                $message .= ' not found.';
                throw $this->createNotFoundException($message);
            }
        }

        $question = $this->em->getRepository('AppBundle:Question')->find($questions[$ticket][$num]);
        if (!$question) {
            throw $this->createNotFoundException('Question for id "'.$questions[$ticket][$num].'" not found.');
        }

        if ($request->isMethod('post')) {
            $c_answer = $request->get('answer');
            $q_answers = $question->getAnswers();

            if (isset($q_answers[$c_answer])) {
                if (!$s_data['answers'][$ticket][$num]) {
                    $s_data['answers'][$ticket][$num] = array($q_answers[$c_answer]);
                } else {
                    $s_data['answers'][$ticket][$num][] = $q_answers[$c_answer];
                }
            }

            reset($questions[$ticket]);
            while (key($questions[$ticket]) !== $num) {
                next($questions[$ticket]);
            }
            next($questions[$ticket]);
            $new_num = key($questions[$ticket]);
            if (is_null($new_num)) {
                $new_num = count($answers[$ticket]);
            }
            $s_data['current'] = $new_num;
            $session->set($s_name, $s_data);

            $log->setAnswers($s_data['answers']);
            $this->em->persist($log);
            $this->em->flush();

            $is_correct = isset($q_answers[$c_answer]) && $q_answers[$c_answer]['correct'];
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array(
                    'correct'  => $is_correct,
                    'c_answer' => $c_answer,
                    'errors'   => $is_correct ? $errors : ($errors + 1),
                ));
            } else {
                $params = [
                    'subject'      => $subject,
                    'num'          => $num,
                    'ticket'       => $ticket,
                    'answers'      => $answers,
                    'question'     => $question,
                    'seconds_left' => Time::getDiffInSeconds($s_data['ticket_end_time']),
                ];
                if ($is_correct) {
                    return $this->render('AppBundle:My:training_exam_success.html.twig', $params);
                } else {
                    return $this->render('AppBundle:My:training_exam_error.html.twig', $params);
                }
            }
        }

        $q_answers = $question->getAnswers();
        if ($this->settings['exam_shuffle_answers']) {
            $keys = array_keys($q_answers);
            shuffle($keys);
            $new_q_answers = array();
            foreach ($keys as $key) {
                $new_q_answers[$key] = $q_answers[$key];
            }
            $q_answers = $new_q_answers;
        }

        $params = [
            'subject'      => $subject,
            'num'          => $num,
            'ticket'       => $ticket,
            'answers'      => $answers,
            'question'     => $question,
            'seconds_left' => Time::getDiffInSeconds($s_data['ticket_end_time']),
            'q_answers'    => $q_answers,
            'errors'       => $errors,
            'max_errors'   => $max_errors,
        ];
        if ($request->get('next')) {
            $content = $this->renderView('AppBundle:My:training_exam_in.html.twig', $params);

            return new JsonResponse(array('content' => $content));
        } else {
            return $this->render('AppBundle:My:training_exam.html.twig', $params);
        }
    }

    protected function examNextTicket($s_name)
    {
        $session = $this->getRequest()->getSession();
        $s_data = $session->get($s_name);

        $s_data['ticket_end_time'] = null;
        $s_data['current_ticket'] += 1;
        $s_data['current'] = 0;

        $session->set($s_name, $s_data);
    }

    protected function examEnd($s_name, ExamLog $log, $is_passed = false)
    {
        $log->setEndedAt(new \DateTime());
        $log->setPassed($is_passed);
        $this->em->persist($log);
        $this->em->flush();

        $session = $this->getRequest()->getSession();
        $session->remove($s_name);
    }

    public function trainingExamResetAction($id)
    {
        $cntxt = $this->get('security.context');
        if (!$cntxt->isGranted('ROLE_TESTER_TRAINING')) {
            throw $this->createNotFoundException('Access denied.');
        }

        $subject = $this->em->getRepository('AppBundle:Subject')->find($id);
        if (!$subject) {
            throw $this->createNotFoundException('Subject for id "'.$id.'" not found.');
        }

        $this->em->getRepository('AppBundle:ExamLog')->createQueryBuilder('el')
            ->delete()
            ->andWhere('el.subject = :subject')->setParameter(':subject', $subject)
            ->andWhere('el.user = :user')->setParameter(':user', $this->user)
            ->getQuery()->execute();

        return $this->redirect($this->generateUrl('my_training', array('id' => $subject->getId())));
    }

    public function trainingExamQuitAction(Request $request, $id)
    {
        $subject = $this->em->getRepository('AppBundle:Subject')->find($id);
        if (!$subject) {
            throw $this->createNotFoundException('Subject for id "'.$id.'" not found.');
        }

        $session = $request->getSession();
        $s_name = 'exam_'.$subject->getId();
        $session->remove($s_name);

        return $this->redirect($this->generateUrl('my_training', array('id' => $subject->getId())));
    }

    public function trainingExamPassAction($id)
    {
        $cntxt = $this->get('security.context');
        if (!($cntxt->isGranted('ROLE_TESTER_TRAINING')
            or ($cntxt->isGranted('ROLE_USER_PAID2') and $this->container->getParameter('is_test'))
        )) {
            throw $this->createNotFoundException('Access denied.');
        }

        $subject = $this->em->getRepository('AppBundle:Subject')->find($id);
        if (!$subject) {
            throw $this->createNotFoundException('Subject for id "'.$id.'" not found.');
        }

        $time = new \DateTime();

        $log = new ExamLog();
        $log->setUser($this->user);
        $log->setSubject($subject);
        $log->setPassed(true);
        $log->setStartedAt($time);
        $log->setEndedAt($time);
        $this->em->persist($log);
        $this->em->flush();

        return $this->redirect($this->generateUrl('my_training', array('id' => $subject->getId())));
    }

    public function trainingSubjectPassAction($id)
    {
        $cntxt = $this->get('security.context');
        if (!$cntxt->isGranted('ROLE_TESTER_TRAINING')) {
            throw $this->createNotFoundException('Access denied.');
        }

        $subject = $this->em->find('AppBundle:Subject', intval($id));
        if (!$subject) {
            throw $this->createNotFoundException();
        }

        $themesIds = [];
        $themes    = $this->em->getRepository('AppBundle:Theme')->createQueryBuilder('t')
            ->andWhere('t.subject = :subject')->setParameter('subject', $subject)
            ->getQuery()->execute();
        foreach ($themes as $theme) { /** @var $theme \My\AppBundle\Entity\Theme */
            $this->trainingThemeTestPassAction($theme->getId());
            $themesIds[] = $theme->getId();
        }

        $slices = $this->em->getRepository('AppBundle:Slice')->createQueryBuilder('s')
            ->andWhere('s.after_theme IN (:themes)')->setParameter('themes', $themesIds)
            ->getQuery()->execute();
        foreach ($slices as $slice) { /** @var $slice \My\AppBundle\Entity\Slice */
            $this->trainingSlicePassAction($slice->getId());
        }

        $this->trainingExamPassAction($subject->getId());

        return $this->redirect($this->generateUrl('my_trainings'));
    }

    public function trainingSubjectResetAction($id)
    {
        $cntxt = $this->get('security.context');
        if (!$cntxt->isGranted('ROLE_TESTER_TRAINING')) {
            throw $this->createNotFoundException('Access denied.');
        }

        $subject = $this->em->find('AppBundle:Subject', intval($id));
        if (!$subject) {
            throw $this->createNotFoundException();
        }

        $themesIds = [];
        $themes    = $this->em->getRepository('AppBundle:Theme')->createQueryBuilder('t')
            ->andWhere('t.subject = :subject')->setParameter('subject', $subject)
            ->getQuery()->execute();
        foreach ($themes as $theme) { /** @var $theme \My\AppBundle\Entity\Theme */
            $this->trainingThemeTestResetAction($theme->getId());
            $themesIds[] = $theme->getId();
        }

        $slices = $this->em->getRepository('AppBundle:Slice')->createQueryBuilder('s')
            ->andWhere('s.after_theme IN (:themes)')->setParameter('themes', $themesIds)
            ->getQuery()->execute();
        foreach ($slices as $slice) { /** @var $slice \My\AppBundle\Entity\Slice */
            $this->trainingSliceResetAction($slice->getId());
        }

        $this->trainingExamResetAction($subject->getId());

        return $this->redirect($this->generateUrl('my_trainings'));
    }

    public function trainingFinalExamAction(Request $request)
    {
        if ($return = $this->checkPermissions(3)) {
            return $return;
        }

        if ($this->user->getCloseFinalExam()) {
            throw $this->createNotFoundException();
        }

        $subjects_repository = $this->em->getRepository('AppBundle:Subject');
        $subjects = $subjects_repository->findAllAsArray($this->user, $this->version);

        $is_passed = true;
        foreach ($subjects as $subject) {
            if (!$subject['is_passed']) {
                $is_passed = false;
            }
        }

        if (!$is_passed) {
            return $this->render('AppBundle:My:training_final_exam_denied.html.twig');
        }

        $final_exams_logs_repository = $this->em->getRepository('AppBundle:FinalExamLog');
        if ($final_exams_logs_repository->isPassed($this->user)) {
            return $this->render('AppBundle:My:training_final_exam_passed.html.twig');
        }

        $session = $request->getSession();
        $s_name = 'final_exam';
        $s_data = $session->get($s_name);
        $log_id = ($s_data && isset($s_data['log_id'])) ? $s_data['log_id'] : 0;

        /** @var $log \My\AppBundle\Entity\FinalExamLog */
        $log = $this->em->getRepository('AppBundle:FinalExamLog')->find($log_id);

        if (!$log) {
            $last_logs = $this->em->getRepository('AppBundle:FinalExamLog')->createQueryBuilder('fel')
                ->andWhere('fel.user = :user')->setParameter(':user', $this->user)
                ->orderBy('fel.started_at', 'DESC')
                ->getQuery()->execute();
            if ($last_logs) {
                $count_last_logs = count($last_logs);
                if ($count_last_logs % 2 == 0) {
                    /** @var $last_log \My\AppBundle\Entity\FinalExamLog */
                    $last_log = $last_logs[0];
                    $retake_time = clone $last_log->getStartedAt();
                    if ($count_last_logs == 2) {
                        $retake_time->add(new \DateInterval('PT2H'));
                    } else {
                        $retake_time->add(new \DateInterval('PT24H'));
                    }
                    if ($retake_time > new \DateTime()) {
                        return $this->render('AppBundle:My:training_final_exam_retake.html.twig');
                    }
                }
            }

            $all_questions = array();

            $questions = $this->em->getRepository('AppBundle:Question')->createQueryBuilder('q')
                ->leftJoin('q.image', 'i')->addSelect('i')
                ->leftJoin('q.theme', 't')->addSelect('t')
                ->leftJoin('t.subject', 's')->addSelect('s')
                ->leftJoin('q.versions', 'v')
                ->andWhere('v = :version')->setParameter(':version', $this->version)
                ->andWhere('q.num IS NOT NULL')
                ->andWhere('q.is_pdd = :is_pdd')->setParameter(':is_pdd', true)
                ->orderBy('q.num')
                ->getQuery()->execute();
            $orig_questions = array();
            foreach ($questions as $question) { /** @var $question \My\AppBundle\Entity\Question */
                $q_num = $question->getTicketNum();
                if (!isset($orig_questions[$q_num])) {
                    $orig_questions[$q_num] = array();
                }
                $orig_questions[$q_num][] = $question->getId();
            }

            $questions_tickets = $this->settings['final_exam_1_tickets'];
            $questions_tickets = min(count($orig_questions), $questions_tickets);
            $tickets_keys = (array) array_rand($orig_questions, $questions_tickets);
            shuffle($tickets_keys);

            $all_questions[1] = array();
            foreach ($tickets_keys as $key) {
                $ticket = $orig_questions[$key];
                if ($this->settings['final_exam_1_shuffle']) {
                    shuffle($ticket);
                }
                $all_questions[1][] = $ticket;
            }

            $themes = $this->em->getRepository('AppBundle:Theme')->createQueryBuilder('t')
                ->leftJoin('t.subject', 's')->addSelect('s')
                ->leftJoin('t.questions', 'q')->addSelect('q')
                ->leftJoin('q.image', 'i')->addSelect('i')
                ->leftJoin('t.versions', 'v')
                ->andWhere('v = :version')->setParameter(':version', $this->version)
                ->getQuery()->execute();

            $orig_questions = array();
            foreach ($themes as $theme) { /** @var $theme \My\AppBundle\Entity\Theme */
                $orig_questions[] = $theme->getQuestionsIdsArray($this->version, true);
            }

            $questions_tickets = $this->settings['final_exam_2_tickets'];
            $questions_limit = $this->settings['final_exam_2_questions_in_ticket'];
            $current_questions = $orig_questions;
            $all_questions[2] = array();

            for ($i = 0; $i < $questions_tickets; $i ++) {
                $all_questions[2][$i] = array();

                $current_questions_count = count($current_questions, COUNT_RECURSIVE) - count($current_questions);
                if ($current_questions_count > 0) {
                    foreach ($current_questions as $j => $q) {
                        $get_questions = floor(count($q) * $questions_limit / $current_questions_count);
                        if ($get_questions > 0) {
                            if ($get_questions > count($q)) {
                                $get_questions = count($q);
                            }
                            $keys = (array) array_rand($q, $get_questions);
                            $all_questions[2][$i] = array_merge(
                                $all_questions[2][$i],
                                array_intersect_key($q, array_fill_keys($keys, null))
                            );
                            foreach ($keys as $k) {
                                unset($current_questions[$j][$k]);
                            }
                        }
                    }

                    $add_questions = $questions_limit - count($all_questions[2][$i]);
                    if ($add_questions > 0) {
                        $current_questions_united = array();
                        foreach ($current_questions as $j => $q) {
                            foreach ($q as $k => $v) {
                                $current_questions_united[$j.'_'.$k] = $v;
                            }
                        }
                        if ($add_questions > count($current_questions_united)) {
                            $add_questions = count($current_questions_united);
                        }
                        if ($add_questions > 0) {
                            $keys = (array) array_rand($current_questions_united, $add_questions);
                            $all_questions[2][$i] = array_merge(
                                $all_questions[2][$i],
                                array_intersect_key($current_questions_united, array_fill_keys($keys, null))
                            );
                            foreach ($keys as $key) {
                                list($j, $k) = explode('_', $key);
                                unset($current_questions[$j][$k]);
                            }
                        }
                    }

                    shuffle($all_questions[2][$i]);
                }

                if (count($all_questions[2][$i]) == 0) {
                    unset($all_questions[2][$i]);
                }

                if (!$this->settings['final_exam_2_not_repeat_questions_in_tickets']) {
                    $current_questions = $orig_questions;
                }
            }

            $questions = $all_questions;

            $answers = array();
            foreach ($questions as $p => $pq) {
                $answers[$p] = array();
                foreach ($pq as $i => $q) {
                    if (count($q) > 0) {
                        $answers[$p][$i] = array_fill(0, count($q), null);
                    } else {
                        $answers[$p][$i] = array();
                    }
                }
            }

            $num = 0;
            $ticket = 0;
            $part = 1;

            $time = new \DateTime();
            $ticket_end_time = null;
            if ($this->settings['final_exam_1_ticket_time'] > 0) {
                $ticket_end_time = clone $time;
                $ticket_end_time->add(new \DateInterval('PT'.$this->settings['final_exam_1_ticket_time'].'M'));
            }

            $log = new FinalExamLog();
            $log->setStartedAt($time);
            $log->setQuestions($questions);
            $log->setAnswers($answers);
            $log->setUser($this->user);
            $this->em->persist($log);
            $this->em->flush();

            $s_data = array(
                'questions'                         => $questions,
                'answers'                           => $answers,
                'extra'                             => array(),
                'started_at'                        => $time,
                'ticket_end_time'                   => $ticket_end_time,
                'log_id'                            => $log->getId(),
                'current'                           => $num,
                'current_ticket'                    => $ticket,
                'current_part'                      => $part,
                'l_activity'                        => $time,
                'amount_of_questions_with_no_extra' => count($questions[$part][$ticket]),
            );

            $session = $request->getSession();
            $session->set($s_name, $s_data);
        } else {
            $questions = $s_data['questions'];
            $answers = $s_data['answers'];
            $num = $s_data['current'];
            $ticket = $s_data['current_ticket'];
            $part = $s_data['current_part'];

            if (!$this->settings['ticket_test_old_style'] && $request->get('question') && $part == 1) {
                $s_data['current'] = array_search($request->get('question'), $questions[$part][$ticket]);
            }

            $num = $s_data['current'];
        }

        $activity_limit = new \DateTime();
        $activity_limit->sub(new \DateInterval('PT2H'));
        if ($s_data['l_activity'] < $activity_limit) {
            $this->finalExamEnd($s_name, $log);
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => 'longtime'));
            } else {
                return $this->render('AppBundle:My:training_final_exam_longtime.html.twig');
            }
        }
        $s_data['l_activity'] = new \DateTime();

        if (!$s_data['ticket_end_time'] && $this->settings['final_exam_'.$part.'_ticket_time'] > 0) {
            $ticket_end_time = new \DateTime();
            $ticket_end_time->add(new \DateInterval('PT'.$this->settings['final_exam_'.$part.'_ticket_time'].'M'));
            $s_data['ticket_end_time'] = $ticket_end_time;
        }

        $session->set($s_name, $s_data);
        if ($s_data['ticket_end_time'] && $s_data['ticket_end_time'] < new \DateTime()) {
            $this->finalExamEnd($s_name, $log);
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => 'timeout'));
            } else {
                return $this->render('AppBundle:My:training_final_exam_timeout.html.twig');
            }
        }

        $popup_error_window = '';
        $amountAnswers      = 0;

        if ($part == 1) {
            $max_errors = false;
            $errors = 0;
            $errors_blocks = array();
            foreach ($answers[$part][$ticket] as $q) {
                foreach ((array)$q as $a) {
                    if ($a && !$a['correct']) {
                        if ($a['extra']) {
                            $max_errors = true;
                            $popup_error_window = 'max_errors_additional_questions';
                            break;
                        }

                        if (!isset($errors_blocks[$a['block']])) {
                            $errors_blocks[$a['block']] = 0;
                        }
                        $errors_blocks[$a['block']] ++;
                        if ($errors_blocks[$a['block']] > 1) {
                            $max_errors = true;
                            $popup_error_window = 'max_errors_questions_block';
                            break;
                        }

                        $errors ++;
                        if ($errors > 2) {
                            $max_errors = true;
                            $popup_error_window = 'max_errors_questions';
                            break;
                        }
                    }
                }

                if (isset($q)) {
                    ++ $amountAnswers;
                }
            }

            if ($max_errors) {
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'error'              => 'error_popup',
                        'popup_error_window' => $popup_error_window,
                    ));
                } else {
                    $this->finalExamEnd($s_name, $log);
                    return $this->render('AppBundle:My:training_final_exam_max_errors.html.twig', array(
                        'popup_error_window' => $popup_error_window,
                    ));
                }
            }
        } else {
            $errors = 0;
            $max_errors = $this->settings['final_exam_'.$part.'_max_errors_in_ticket'];
            if ($max_errors > 0) {
                foreach ($answers[$part][$ticket] as $q) {
                    foreach ((array)$q as $a) {
                        if (!$a['correct']) {
                            $errors ++;
                        }
                    }
                }
                if ($errors >= $max_errors) {
                    $popup_error_window = 'max_errors_ticket';
                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(array(
                            'error'              => 'error_popup',
                            'popup_error_window' => $popup_error_window,

                        ));
                    } else {
                        $this->finalExamEnd($s_name, $log);
                        return $this->render('AppBundle:My:training_final_exam_max_errors.html.twig', array(
                            'popup_error_window' => $popup_error_window,
                        ));
                    }
                }
            }
        }

        $isComplete = array_reduce($answers[$part][$ticket], function ($carry, $item) {
            $carry = $carry && ($item[0] !== null);
            return $carry;
        }, true);

        if (!$this->settings['ticket_test_old_style'] && $part == 1 && $isComplete) {
            if ($ticket + 1 < count($answers[$part])) {
                $s_data['ticket_end_time'] = null;
                $s_data['current_ticket'] += 1;
            } else {
                $s_data['ticket_end_time'] = null;
                $s_data['current']         = 0;
                $s_data['current_ticket']  = 0;
                $s_data['current_part']    = 2;
            }

            $session->set($s_name, $s_data);
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['refresh' => true]);
            } else {
                return $this->redirect($this->generateUrl('my_training_final_exam'));
            }
        }

        if (!isset($questions[$part][$ticket][$num])) {
            $keys = array_keys($questions[$part][$ticket]);
            if ($num > end($keys)) {
                if ($ticket + 1 < count($answers[$part])) {
                    $s_data['ticket_end_time'] = null;
                    $s_data['current'] = 0;
                    $s_data['current_ticket'] += 1;
                    $session->set($s_name, $s_data);
                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(array('refresh' => true));
                    } else {
                        return $this->redirect($this->generateUrl('my_training_final_exam'));
                    }
                } else {
                    if ($part == 1) {
                        $s_data['ticket_end_time'] = null;
                        $s_data['current'] = 0;
                        $s_data['current_ticket'] = 0;
                        $s_data['current_part'] = 2;
                        $session->set($s_name, $s_data);
                        if ($request->isXmlHttpRequest()) {
                            return new JsonResponse(array('refresh' => true));
                        } else {
                            return $this->redirect($this->generateUrl('my_training_final_exam'));
                        }
                    } else {
                        $this->finalExamEnd($s_name, $log, true);

                        $notify = $this->get('app.notify');
                        $notify->sendAfterFinalExam($this->user);

                        if ($request->isXmlHttpRequest()) {
                            return new JsonResponse(array('complete' => true));
                        } else {
                            return $this->render('AppBundle:My:training_final_exam_complete.html.twig');
                        }
                    }
                }
            } else {
                $message = 'Question for number "'.$num.'"';
                $message .= ' in ticket for "'.$ticket.'"';
                $message .= ' in final exam for part "'.$part.'"';
                $message .= ' not found.';
                throw $this->createNotFoundException($message);
            }
        }

        $question = $this->em->getRepository('AppBundle:Question')->find($questions[$part][$ticket][$num]);
        if (!$question) {
            throw $this->createNotFoundException('Question for id "'.$questions[$part][$ticket][$num].'" not found.');
        }

        if ($request->isMethod('post')) {
            $end_time = $s_data['ticket_end_time'];
            $c_answer = $request->get('answer');
            $q_answers = $question->getAnswers();
            $is_correct = false;

            if (isset($q_answers[$c_answer])) {
                if (!$answers[$part][$ticket][$num]) {
                    $answers[$part][$ticket][$num] = array($q_answers[$c_answer]);
                } else {
                    $answers[$part][$ticket][$num][] = $q_answers[$c_answer];
                }
                $is_correct = $q_answers[$c_answer]['correct'];

                if (!$is_correct) {
                    $is_extra = in_array($question->getId(), $s_data['extra']);

                    $index = count($answers[$part][$ticket][$num]) - 1;
                    $answers[$part][$ticket][$num][$index]['block'] = $question->getBlockNum();
                    $answers[$part][$ticket][$num][$index]['extra'] = $is_extra;

                    if (!$is_extra) {
                        $sql = 'SELECT q.id,
                                SUBSTRING_INDEX(q.num, ".", 1) AS ticket,
                                CEIL(SUBSTRING_INDEX(q.num, ".", -1) / 5) AS block
                            FROM questions AS q
                            LEFT JOIN training_versions_questions AS tvq ON q.id = tvq.question_id
                            WHERE q.is_pdd = 1 AND tvq.version_id = :version
                            HAVING block = :block AND ticket <> :ticket
                            ORDER BY RAND()
                            LIMIT 5';
                        $rsm = new Query\ResultSetMapping($this->em);
                        $rsm->addScalarResult('id', 'id', 'integer');
                        $query = $this->em->createNativeQuery($sql, $rsm);
                        $query->setParameters(array(
                            'block'   => $question->getBlockNum(),
                            'ticket'  => $question->getTicketNum(),
                            'version' => $this->version,
                        ));
                        $extra = $query->getArrayResult();
                        foreach ($extra as $row) {
                            $s_data['extra'][] = $row['id'];

                            if ($amountAnswers > $s_data['amount_of_questions_with_no_extra'] - 1) {
                                $questions[$part][$ticket][] = $extra;
                                $answers[$part][$ticket][]   = null;
                            }
                        }

                        if ($end_time) {
                            $end_time->add(new \DateInterval('PT'.$this->settings['final_exam_1_extra_time'].'M'));
                        }
                    }
                }

                if ($amountAnswers == $s_data['amount_of_questions_with_no_extra'] - 1) {
                    foreach ($s_data['extra'] as $extra) {
                        $questions[$part][$ticket][] = $extra;
                        $answers[$part][$ticket][]   = null;
                    }
                }
            }

            if ($this->settings['ticket_test_old_style'] || $part == 2) {
                reset($questions[$part][$ticket]);
                while (key($questions[$part][$ticket]) !== $num) {
                    next($questions[$part][$ticket]);
                }
                next($questions[$part][$ticket]);
                $new_num = key($questions[$part][$ticket]);
                if (is_null($new_num)) {
                    $new_num = count($answers[$part][$ticket]);
                }
                $s_data['current'] = $new_num;
            }

            $log->setAnswers($s_data['answers']);
            $this->em->persist($log);
            $this->em->flush();

            $s_data['answers'] = $answers;
            $s_data['questions'] = $questions;

            $session->set($s_name, $s_data);
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array(
                    'correct'  => $is_correct,
                    'c_answer' => $c_answer,
                    'errors'   => $is_correct ? $errors : ($errors + 1),
                ));
            } else {
                $params = [
                    'num'          => $num,
                    'answers'      => $answers[$part][$ticket],
                    'question'     => $question,
                    'end_time'     => $end_time,
                    'seconds_left' => Time::getDiffInSeconds($s_data['ticket_end_time']),
                    'ticket_num'   => $part == 1 ? $question->getTicketNum() : $ticket + 1,
                    'help_btn'     => $this->settings['question_help_btn'],
                ];

                $params['seconds_left'] = Time::getAllSeconds($params['rem_time']);

                if ($is_correct) {
                    return $this->render('AppBundle:My:training_final_exam_success.html.twig', $params);
                } else {
                    return $this->render('AppBundle:My:training_final_exam_error.html.twig', $params);
                }
            }
        }

        $q_answers = $question->getAnswers();
        if ($this->settings['final_exam_'.$part.'_shuffle_answers']) {
            $keys = array_keys($q_answers);
            shuffle($keys);
            $new_q_answers = array();
            foreach ($keys as $key) {
                $new_q_answers[$key] = $q_answers[$key];
            }
            $q_answers = $new_q_answers;
        }

        $params = [
            'part'         => $part,
            'num'          => $num,
            'answers'      => $answers[$part][$ticket],
            'question'     => $question,
            'end_time'     => $s_data['ticket_end_time'],
            'rem_time'     => $s_data['ticket_end_time'] ?
                $s_data['ticket_end_time']->diff(new \DateTime('now')) : null,
            'seconds_left' => Time::getDiffInSeconds($s_data['ticket_end_time']),
            'q_answers'    => $q_answers,
            'ticket_num'   => $part == 1 ? $question->getTicketNum() : ($ticket + 1),
            'help_btn'     => $this->settings['question_help_btn'],
            'errors'       => $errors,
            'max_errors'   => $max_errors,
        ];

        $params['seconds_left'] = Time::getAllSeconds($params['rem_time']);

        if ($this->settings['ticket_test_old_style'] || $part == 2) {
            if ($request->get('next')) {
                $content = $this->renderView('AppBundle:My:training_final_exam_in.html.twig', $params);
                return new JsonResponse(['content' => $content]);
            } else {
                return $this->render('AppBundle:My:training_final_exam.html.twig', $params);
            }
        }

        $questionsEntitiesSource = $this->em->getRepository('AppBundle:Question')->createQueryBuilder('q')
            ->leftJoin('q.image', 'i')
            ->addSelect('i')
            ->andWhere('q.id IN (:questions)')->setParameter('questions', $questions[$part][$ticket])
            ->getQuery()->getResult();

        $questionsEntities = array_fill(0, count($questions[$part][$ticket]), null);
        foreach ($questionsEntitiesSource as $quest) { /** @var $quest Question */
            $questionsEntities[array_search($quest->getId(), $questions[$part][$ticket])] = $quest;
        }

        $questAnswers = [];
        for ($i = 0; $i < count($questions[$part][$ticket]); $i++) {
            if (isset($answers[$part][$ticket][$i][0]['correct'])) {
                $questAnswers[$questions[$part][$ticket][$i]] = $answers[$part][$ticket][$i][0]['correct'];
            } else {
                $questAnswers[$questions[$part][$ticket][$i]] = null;
            }
        }

        $params['all_questions'] = $questionsEntities;
        $params['quest_answers'] = $questAnswers;

        if ($request->get('question')) {
            $params['question'] = $this->em->getRepository('AppBundle:Question')->find($request->get('question'));
            $content = $this->renderView('AppBundle:My:training_final_exam_in.html.twig', $params);
            return new JsonResponse(['content' => $content]);
        } elseif ($request->get('next')) {
            $content = $this->renderView('AppBundle:My:training_final_exam_with_tiles.html.twig', $params);
            return new JsonResponse(['content' => $content]);
        } else {
            return $this->render('AppBundle:My:training_final_exam.html.twig', $params);
        }
    }

    protected function finalExamEnd($s_name, FinalExamLog $log, $is_passed = false)
    {
        $log->setEndedAt(new \DateTime());
        $log->setPassed($is_passed);
        $this->em->persist($log);

        $this->user->setCertificate('_');
        $this->em->persist($this->user);

        $this->em->flush();

        $session = $this->getRequest()->getSession();
        $session->remove($s_name);
    }

    public function trainingFinalExamResetAction()
    {
        $cntxt = $this->get('security.context');
        if (!$cntxt->isGranted('ROLE_TESTER_TRAINING')) {
            throw $this->createNotFoundException('Access denied.');
        }

        $this->user->setCertificate(null);
        $this->em->persist($this->user);
        $this->em->flush();

        $this->em->getRepository('AppBundle:FinalExamLog')->createQueryBuilder('fel')
            ->delete()
            ->andWhere('fel.user = :user')->setParameter(':user', $this->user)
            ->getQuery()->execute();

        return $this->redirect($this->generateUrl('my_trainings'));
    }

    public function trainingFinalExamQuitAction(Request $request)
    {
        $session = $request->getSession();
        $s_name = 'final_exam';
        $session->remove($s_name);

        return $this->redirect($this->generateUrl('my_trainings'));
    }

    public function trainingFinalExamPassAction()
    {
        $cntxt = $this->get('security.context');
        if (!$cntxt->isGranted('ROLE_TESTER_TRAINING')) {
            throw $this->createNotFoundException('Access denied.');
        }

        $log = new FinalExamLog();
        $log->setUser($this->user);
        $log->setStartedAt(new \DateTime());
        $this->em->persist($log);
        $this->em->flush();

        $this->finalExamEnd('final_exam', $log, true);

        return $this->redirect($this->generateUrl('my_trainings'));
    }

    protected function checkPermissions($pay = 2)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER_PAID'.$pay)) {
            return $this->render('AppBundle:My:training_without_'.$pay.'_payment.html.twig');
        }

        return false;
    }

    protected function subjectIsClosed(Subject $subject)
    {
        $sId = $this->em->getRepository('AppBundle:Subject')->createQueryBuilder('s')
            ->select('s.id')
            ->leftJoin('s.closed_users', 'cu')
            ->andWhere('s = :subject')->setParameter('subject', $subject)
            ->andWhere('cu = :user')->setParameter('user', $this->getUser())
            ->getQuery()->getArrayResult();

        return (bool)$sId;
    }
}
