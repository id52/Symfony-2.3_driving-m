<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Theme as ThemeModel;

class Theme extends ThemeModel
{
    public function getQuestionsIdsArray(TrainingVersion $version, $without_pdd = false)
    {
        $result = array();
        $questions = $this->getQuestions();
        foreach ($questions as $question) {
            if (in_array($version->getId(), $question->getVersionsIds())
                && (!$without_pdd || !$question->getIsPdd())
            ) {
                $result[] = $question->getId();
            }

        }

        return $result;
    }

    public function getVersionsIds()
    {
        $ids = array();
        $versions = $this->getVersions();
        foreach ($versions as $version) {
            $ids[] = $version->getId();
        }
        return $ids;
    }
}
