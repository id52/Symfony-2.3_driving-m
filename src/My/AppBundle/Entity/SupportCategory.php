<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\SupportCategory as SupportCategoryModel;

class SupportCategory extends SupportCategoryModel
{
    public function __toString()
    {
        if ($parent = $this->getParent()) {
            $parentName = $parent->getName();
        }
        if (empty($parentName)) {
            if ($this->getType() === 'teacher') {
                $user = $this->getUser();
                $name = $user->getFirstName().' '.$user->getLastName();
            } else {
                $name = $this->getName();
            }
            return $name;
        } else {
            return $parentName.': '.$this->getName();
        }
    }

    public function setTVersions($tVersions)
    {
        if ($this->getType() === 'teacher' && is_array($tVersions)) {
            if (count($tVersions) > 0) {
                $versions = array();
                foreach ($tVersions as $t_id => $t_v) {
                    foreach ($t_v as $v_id) {
                        $versions[] = $t_id.'_'.$v_id;
                    }
                }
                if (count($versions) > 0) {
                    $tVersions = '|'.implode('|', $versions).'|';
                } else {
                    $tVersions = null;
                }
            } else {
                $tVersions = null;
            }
        }

        $this->t_versions = $tVersions;

        return $this;
    }

    public function getTVersions()
    {
        if ($this->getType() === 'teacher') {
            $tVersions = array();
            if ($this->t_versions) {
                $versions = explode('|', trim($this->t_versions, '|'));
                foreach ($versions as $v) {
                    list($t_id, $v_id) = explode('_', $v);
                    $t_id = intval($t_id);
                    $v_id = intval($v_id);
                    if (!isset($tVersions[$t_id])) {
                        $tVersions[$t_id] = array();
                    }
                    $tVersions[$t_id][] = $v_id;
                }
            }
            return $tVersions;
        } else {
            return $this->t_versions;
        }
    }
}
