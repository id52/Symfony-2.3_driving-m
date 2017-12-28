<?php

namespace My\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use My\AppBundle\Entity\Question;
use My\AppBundle\Entity\Slice;
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

        $versions = $manager->getRepository('AppBundle:TrainingVersion')->findAll();

        $subject = new Subject();
        $subject->setTitle('Правила Дорожного Движения');
        $subject->setBriefDescription('ПДД');
        $subject->setDescription('ПДД - Описание');
        foreach ($versions as $version) {
            $subject->addVersion($version);
        }
        $manager->persist($subject);

        $theme = new Theme();
        $theme->setPosition(0);
        $theme->setSubject($subject);
        $theme->setTitle('ПДД 1');
        $theme->setText('ПДД 1 - Текст');
        foreach ($versions as $version) {
            $theme->addVersion($version);
        }
        $manager->persist($theme);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('ПДД 1 - 1 - Текст');
        $question->setDescription('ПДД 1 - 1 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('ПДД 1 - 2 - Текст');
        $question->setDescription('ПДД 1 - 2 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('ПДД 1 - 3 - Текст');
        $question->setDescription('ПДД 1 - 3 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $theme = new Theme();
        $theme->setPosition(1);
        $theme->setSubject($subject);
        $theme->setTitle('ПДД 2');
        $theme->setText('ПДД 2 - Текст');
        foreach ($versions as $version) {
            $theme->addVersion($version);
        }
        $manager->persist($theme);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('ПДД 2 - 1 - Текст');
        $question->setDescription('ПДД 2 - 1 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $question->setIsPdd(true);
        $question->setNum('01.01');
        $manager->persist($question);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('ПДД 2 - 2 - Текст');
        $question->setDescription('ПДД 2 - 2 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('ПДД 2 - 3 - Текст');
        $question->setDescription('ПДД 2 - 3 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $slice = new Slice();
        $slice->setAfterTheme($theme);
        foreach ($versions as $version) {
            $slice->addVersion($version);
        }
        $manager->persist($slice);

        $theme = new Theme();
        $theme->setPosition(2);
        $theme->setSubject($subject);
        $theme->setTitle('ПДД 3');
        $theme->setText('ПДД 3 - Текст');
        foreach ($versions as $version) {
            $theme->addVersion($version);
        }
        $manager->persist($theme);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('ПДД 3 - 1 - Текст');
        $question->setDescription('ПДД 3 - 1 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('ПДД 3 - 2 - Текст');
        $question->setDescription('ПДД 3 - 2 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('ПДД 3 - 3 - Текст');
        $question->setDescription('ПДД 3 - 3 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $subject = new Subject();
        $subject->setTitle('Медицина');
        $subject->setBriefDescription('МЕД');
        $subject->setDescription('МЕД - Описание');
        foreach ($versions as $version) {
            $subject->addVersion($version);
        }
        $manager->persist($subject);

        $theme = new Theme();
        $theme->setPosition(0);
        $theme->setSubject($subject);
        $theme->setTitle('МЕД 1');
        $theme->setText('МЕД 1 - Текст');
        foreach ($versions as $version) {
            $theme->addVersion($version);
        }
        $manager->persist($theme);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('МЕД 1 - 1 - Текст');
        $question->setDescription('МЕД 1 - 1 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $question->setIsPdd(true);
        $question->setNum('01.02');
        $manager->persist($question);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('МЕД 1 - 2 - Текст');
        $question->setDescription('МЕД 1 - 2 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('МЕД 1 - 3 - Текст');
        $question->setDescription('МЕД 1 - 3 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $theme = new Theme();
        $theme->setPosition(1);
        $theme->setSubject($subject);
        $theme->setTitle('МЕД 2');
        $theme->setText('МЕД 2 - Текст');
        foreach ($versions as $version) {
            $theme->addVersion($version);
        }
        $manager->persist($theme);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('МЕД 2 - 1 - Текст');
        $question->setDescription('МЕД 2 - 1 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('МЕД 2 - 2 - Текст');
        $question->setDescription('МЕД 2 - 2 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $question->setIsPdd(true);
        $question->setNum('01.03');
        $manager->persist($question);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('МЕД 2 - 3 - Текст');
        $question->setDescription('МЕД 2 - 3 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $slice = new Slice();
        $slice->setAfterTheme($theme);
        foreach ($versions as $version) {
            $slice->addVersion($version);
        }
        $manager->persist($slice);

        $theme = new Theme();
        $theme->setPosition(2);
        $theme->setSubject($subject);
        $theme->setTitle('МЕД 3');
        $theme->setText('МЕД 3 - Текст');
        foreach ($versions as $version) {
            $theme->addVersion($version);
        }
        $manager->persist($theme);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('МЕД 3 - 1 - Текст');
        $question->setDescription('МЕД 3 - 1 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('МЕД 3 - 2 - Текст');
        $question->setDescription('МЕД 3 - 2 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $manager->persist($question);

        $question = new Question();
        $question->setTheme($theme);
        $question->setText('МЕД 3 - 3 - Текст');
        $question->setDescription('МЕД 3 - 3 - Описание');
        $question->setAnswers(array(
            array('title' => '1', 'correct' => false),
            array('title' => '2', 'correct' => true),
            array('title' => '3', 'correct' => false),
        ));
        foreach ($versions as $version) {
            $question->addVersion($version);
        }
        $question->setIsPdd(true);
        $question->setNum('01.04');
        $manager->persist($question);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
