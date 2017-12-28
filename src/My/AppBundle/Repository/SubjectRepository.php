<?php

namespace My\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use My\AppBundle\Entity\TrainingVersion;
use My\AppBundle\Entity\User;

class SubjectRepository extends EntityRepository
{
    public function findAllAsArray(User $user, TrainingVersion $version)
    {
        $result = array();
        $subjects = $this->createQueryBuilder('s')
            ->leftJoin('s.image', 'i')->addSelect('i')
            ->leftJoin('s.exams_logs', 'el', 'WITH', 'el.user = :user AND el.passed = :passed')->addSelect('el')
            ->setParameters(array(':user' => $user, ':passed' => true))
            ->leftJoin('s.versions', 'v')
            ->andWhere('v = :version')->setParameter(':version', $version)
            ->getQuery()->execute();
        foreach ($subjects as $subject) { /** @var $subject \My\AppBundle\Entity\Subject */
            $passed_date = null;
            if (count($subject->getExamsLogs()) > 0) {
                $exam_logs = $subject->getExamsLogs();
                /** @var $exam_log \My\AppBundle\Entity\ExamLog */
                $exam_log = $exam_logs[0];
                $passed_date = $exam_log->getEndedAt();
            }

            $themes_cnt = 0;
            $read_themes_cnt = 0;

            /** @CAUTION NativeQuery */
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('reader', 'reader');
            $query = $this->_em->createNativeQuery('
                SELECT t.id, r.reader_id reader
                FROM themes t
                LEFT JOIN themes_readers r ON t.id = r.theme_id AND r.reader_id = :user_id
                LEFT JOIN training_versions_themes tvt ON t.id = tvt.theme_id
                WHERE t.subject_id = :subject_id AND tvt.version_id = :version_id
                GROUP BY t.id
            ', $rsm);
            $query->setParameters(array(
                ':user_id'    => $user->getId(),
                ':subject_id' => $subject->getId(),
                ':version_id' => $version->getId(),
            ));
            $themes = $query->getArrayResult();

            foreach ($themes as $theme) {
                $theme_read = (bool)$theme['reader'];
                $themes_cnt ++;
                $read_themes_cnt += ($theme_read ? 1 : 0);
            }

            $result[$subject->getId()] = array(
                'object'          => $subject,
                'is_passed'       => (bool)$passed_date,
                'passed_date'     => $passed_date,
                'themes_cnt'      => $themes_cnt,
                'read_themes_cnt' => $read_themes_cnt,
            );
        }
        return $result;
    }
}
