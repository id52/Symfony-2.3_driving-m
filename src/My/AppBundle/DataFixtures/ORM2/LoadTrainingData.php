<?php

namespace My\AppBundle\DataFixtures\ORM2;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use My\AppBundle\Entity\Question;
use My\AppBundle\Entity\Subject;
use My\AppBundle\Entity\Theme;
use My\AppBundle\Entity\TrainingVersion;

class LoadTrainingData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $categories = $manager->getRepository('AppBundle:Category')->findAll();
        foreach ($categories as $category) {
            $version = new TrainingVersion();
            $version->setCategory($category);
            $version->setStartDate(new \DateTime());
            $manager->persist($version);
        }
        $manager->flush();

        $subject = new Subject();
        $subject->setTitle('Правила Дорожного Движения');
        $subject->setBriefDescription('ПДД');
        $subject->setDescription('ПДД - Описание');

        $versions = $manager->getRepository('AppBundle:TrainingVersion')->findAll();
        foreach ($versions as $version) {
            $subject->addVersion($version);
        }
        $manager->persist($subject);

        $theme = new Theme();
        $theme->setSubject($subject);
        $theme->setTitle('ПДД 1');
        $theme->setText('ПДД 1 - Текст');
        foreach ($versions as $version) {
            $theme->addVersion($version);
        }
        $manager->persist($theme);

        for ($i = 1; $i <= 40; $i++) {
            for ($j = 1; $j <= 20; $j++) {
                $answers = array(
                    array('title' => '1', 'correct' => false),
                    array('title' => '2', 'correct' => false),
                    array('title' => '3', 'correct' => false),
                );
                $answers[mt_rand(0, 2)]['correct'] = true;
                $question = new Question();
                $question->setTheme($theme);
                $question->setText('ПДД '.$i.' - '.$j.' - Текст');
                $question->setDescription('ПДД '.$i.' - '.$j.' - Описание');
                $question->setNum(sprintf('%02d.%02d', $i, $j));
                $question->setIsPdd(true);
                $question->setAnswers($answers);
                foreach ($versions as $version) {
                    $question->addVersion($version);
                }
                $manager->persist($question);
            }
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
