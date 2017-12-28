<?php

namespace My\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Entity\Setting;

class SettingRepository extends EntityRepository
{
    protected $settings = array();

    public function clearAllData()
    {
        $table_name = $this->getClassMetadata()->getTableName();
        $connection = $this->_em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL($table_name, true));
        $this->settings = array();
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        foreach ($data as $key => $value) {
            if ($key && !is_null($value)) {
                $type = gettype($value);
                if ('object' == $type) {
                    if ($value instanceof \DateTime) {
                        $value = $value->format('d-m-Y');
                        $type  = 'date';
                    } else {
                        $value = serialize($value);
                        $type  = 'object';
                    }
                }

                $setting = $this->findOneBy(array('_key' => $key));
                if (!$setting) {
                    $setting = new Setting();
                }
                $setting->setKey($key);
                $setting->setValue($value);
                $setting->setType($type);
                $this->_em->persist($setting);
            }
        }
        $this->_em->flush();
    }

    /**
     * @param array $data
     */
    public function setAllData($data)
    {
        $this->clearAllData();

        foreach ($data as $key => $value) {
            if ($key && !is_null($value)) {
                $type = gettype($value);
                if ('object' == $type) {
                    if ($value instanceof \DateTime) {
                        $value = $value->format('d-m-Y');
                        $type  = 'date';
                    } else {
                        $value = serialize($value);
                        $type  = 'object';
                    }
                }

                $setting = new Setting();
                $setting->setKey($key);
                $setting->setValue($value);
                $setting->setType($type);
                $this->_em->persist($setting);
            }
        }
        $this->_em->flush();
    }

    /**
     * @return array
     */
    public function getAllData()
    {
        if (count($this->settings) == 0) {
            $settings = $this->findAll();
            foreach ($settings as $setting) {
                /** @var $setting \My\AppBundle\Entity\Setting */
                $value = $setting->getValue();
                $type = $setting->getType();
                switch ($type) {
                    case 'date':
                        $value = new \DateTime($value);
                        break;
                    case 'object':
                        $value = unserialize($value);
                        break;
                    default:
                        if (is_null($type)) {
                            $type = 'string';
                        }
                        settype($value, $type);
                }
                $this->settings[$setting->getKey()] = $value;
            }
        }

        return $this->settings;
    }

    /**
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        $settings = $this->getAllData();
        return isset($settings[$key]) ? $settings[$key] : '';
    }
}
