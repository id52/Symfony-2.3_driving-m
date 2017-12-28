<?php

namespace My\AppBundle\Controller;

use Doctrine\ORM\Query\Expr;
use My\AppBundle\Entity\Image;
use My\AppBundle\Entity\Category;
use My\AppBundle\Entity\QuestionI18N;
use My\AppBundle\Entity\Region;
use My\AppBundle\Form\Type\ImageFormType;
use My\AppBundle\Form\Type\QuestionFormType;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityRepository;

class TranslatorController extends Controller
{
    /** @var $em \Doctrine\ORM\EntityManager */
    public $em;
    /** @var $user \My\AppBundle\Entity\User */
    public $user;
    public $settings = array();

    /** @var $locale \My\AppBundle\Entity\Locale */
    public $locale;

    public function init()
    {
        $code   = $this->getRequest()->get('locale');
        $upCode = strtoupper($code);

        if (false === $this->get('security.context')->isGranted('ROLE_TRANSLATOR_'.$upCode)) {
            throw $this->createNotFoundException('no role');
        }

        $locale = $this->em->getRepository('AppBundle:Locale')->findOneBy(['code' => $code]);
        if (!$locale) {
            throw $this->createNotFoundException('no locale');
        }

        $this->locale = $locale;
    }

    public function questionListAction()
    {
        $qb = $this->em->getRepository('AppBundle:Question')->createQueryBuilder('q')
            ->leftJoin('q.theme', 't')
            ->leftJoin('q.questions_i18n', 'i')->addSelect('i')
        ;

        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('question', 'form', array(), array(
            'csrf_protection'    => false,
            'translation_domain' => 'question',
        ))
        ->add('is_pdd', 'checkbox', array('required' => false))
        ->add('subject', 'entity', array(
            'class'       => 'AppBundle:Subject',
            'required'    => false,
            'empty_value' => 'choose_option',
        ))
        ->add('version', 'entity', array(
            'class'         => 'AppBundle:TrainingVersion',
            'required'      => false,
            'empty_value'   => 'choose_option',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('v')
                     ->addOrderBy('v.category')
                     ->addOrderBy('v.start_date')
                ;
            },
        ))
        ->add('theme', 'entity', array(
            'class'       => 'AppBundle:Theme',
            'required'    => false,
            'empty_value' => 'choose_option',
            'property'    => 'title',
        ))
        ;

        $fb->setMethod('get');
        $filter_form = $fb->getForm();
        $filter_form->handleRequest($this->getRequest());

        $data = null;
        if (($data = $filter_form->get('is_pdd')->getData()) && $data) {
            $qb
                ->andWhere('q.is_pdd = :is_pdd')->setParameter(':is_pdd', true)
                ->addOrderBy('q.num')
                ->addOrderBy('t.subject')
                ->addOrderBy('t.position')
            ;
        } else {
            $qb
                ->addOrderBy('t.subject')
                ->addOrderBy('t.position')
                ->addOrderBy('q.num')
            ;
        }
        if ($data = $filter_form->get('subject')->getData()) {
            $qb->andWhere('t.subject = :subject')->setParameter(':subject', $data);

        }
        if ($data = $filter_form->get('version')->getData()) {
            $qb
                ->leftJoin('q.versions', 'v')
                ->andWhere('v.id = :version')->setParameter(':version', $data)
            ;
        }

        if ($data = $filter_form->get('theme')->getData()) {
            $qb->andWhere('q.theme = :theme')->setParameter(':theme', $data)
            ;
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setMaxPerPage(20);
        $pagerfanta->setCurrentPage($this->getRequest()->get('page', 1));

        $questionIds = [];
        foreach ($pagerfanta->getCurrentPageResults() as $question) { /** @var $question \My\AppBundle\Entity\Question*/
            $questionIds[$question->getId()] = $question->getId();
        };

        $questionsI18N = $this->em->getRepository('AppBundle:QuestionI18N')->createQueryBuilder('qi')
            ->andWhere('qi.locale = :locale') ->setParameter('locale', $this->locale)
            ->andWhere('qi.question IN (:question_ids)')->setParameter('question_ids', $questionIds)
            ->andWhere('qi.description IS NOT NULL AND qi.text IS NOT NULL')
            ->getQuery()->getResult();

        foreach ($questionsI18N as $questionI18N) { /** @var $questionI18N \My\AppBundle\Entity\QuestionI18N */
            unset($questionIds[$questionI18N->getQuestion()->getId()]);
        };

        return $this->render('AppBundle:Translator:list.html.twig', array(
            'pagerfanta'     => $pagerfanta,
            'locale'         => $this->locale->getCode(),
            'emptyQuestions' => $questionIds,
            'filter_form'    => $filter_form->createView(),
            'query_string'   => $this->getRequest()->getQueryString(),
        ));
    }

    public function questionItemAction(Request $request)
    {
        $id   = null;
        $data = null;

        /** @var $question \My\AppBundle\Entity\Question */
        if ($id = $request->get('id')) {
            $question = $this->em->getRepository('AppBundle:Question')->createQueryBuilder('q')
                ->leftJoin('q.image', 'i')->addSelect('i')
                ->andWhere('q.id = :id')->setParameter('id', $id)
                ->getQuery()->getOneOrNullResult();

            if (!$question) {
                throw $this->createNotFoundException('no question entity');
            }

            $questionI18N = $this->em->getRepository('AppBundle:QuestionI18N')->createQueryBuilder('qi')
                ->andWhere('qi.locale = :locale') ->setParameter('locale', $this->locale)
                ->andWhere('qi.question = :question')->setParameter('question', $question)
                ->getQuery()->getOneOrNullResult();

            if ($questionI18N) {
                $data['text']        = $questionI18N->getText();
                $data['description'] = $questionI18N->getDescription();
            } else {
                $questionI18N = new QuestionI18N();
                $questionI18N->setLocale($this->locale);
                $questionI18N->setQuestion($question);
                $this->em->persist($questionI18N);
                $question->addQuestionsI18n($questionI18N);
                $this->em->persist($question);
            }
        } else {
            throw $this->createNotFoundException('no id');
        }

        $answers       = $question->getAnswers();
        $answersI18N   = $questionI18N->getAnswers();
        $answersMerged = [];

        foreach ($answers as $key => $answer) {
            $answersMerged[] = [
                'title'      => $answer['title'],
                'correct'    => $answer['correct'],
                'title_i18n' => isset($answersI18N[$key]['title']) ? $answersI18N[$key]['title'] : '' ,
            ];
        }

        $fb = $this->createFormBuilder($data, ['translation_domain' => 'question']);
        $fb->add('text', 'textarea', ['attr' => ['style' => 'height:100px']]);
        $fb->add('description', 'textarea', ['attr' => ['style' => 'height:100px']]);

        $form = $fb->getForm();

        if ($request->isMethod('post')) {
            if ($answers_req = $request->get('answers')) {
                foreach ($answers_req as $key => $answer) {
                    $answersI18N[$key] = [
                        'title'   => $answer,
                        'correct' => $answers[$key]['correct'],
                    ];
                }
            }

            $form->handleRequest($request);

            if ($form->isValid()) {
                $questionI18N->setAnswers($answersI18N);
                $questionI18N->setText($form->get('text')->getData());
                $questionI18N->setDescription($form->get('description')->getData());

                $this->em->persist($questionI18N);
                $this->em->flush();

                return $this->redirect($this->generateUrl('admin_question_translations_'. $this->locale->getCode())
                    .'?'.$this->getRequest()->get('query_string'));
            }
        }

        return $this->render('AppBundle:Translator:item.html.twig', array(
            'form'         => $form->createView(),
            'question'     => $question,
            'answers'      => $answersMerged,
            'locale'       => $this->locale->getCode(),
            'query_string' => $this->getRequest()->get('query_string'),
        ));
    }
}
