<?php

namespace My\AppBundle\Entity;

use Imagine\Imagick\Imagine;
use Imagine\Image\Box;
use My\AppBundle\Model\Image as ImageModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Yaml\Yaml;

class Image extends ImageModel
{
    /**
     * @var $uploadFile \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    protected $uploadFile;
    protected $uploadDir = '/uploads/images';

    public function getUploadFile()
    {
        return $this->uploadFile;
    }

    public function setUploadFile(UploadedFile $file)
    {
        $this->uploadFile = $file;
    }

    public function preUpload()
    {
        if ($this->uploadFile) {
            if ($this->file) {
                $this->removeUpload();
                $this->removeUploadCache();
            }
            $this->file = sha1(uniqid()).'.'.$this->uploadFile->guessExtension();
        }
    }

    public function upload()
    {
        if ($this->uploadFile) {
            $this->uploadFile->move($this->getUploadRootDir(), $this->file);
            unset($this->uploadFile);

            $image = new Imagine();
            $image
                ->open($this->getAbsolutePath())
                ->thumbnail(new Box(1200, 1200))
                ->save($this->getAbsolutePath())
            ;
        }
    }

    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function removeUploadCache()
    {
        if ($web_path = $this->getWebPath()) {
            $config = Yaml::parse(__DIR__.'/../../../../app/config/config.yml');
            $filter_sets = array_keys($config['liip_imagine']['filter_sets']);
            $imagine_cache_dir = __DIR__.'/../../../../web'.$config['liip_imagine']['cache_prefix'];
            foreach ($filter_sets as $filter) {
                $file = $imagine_cache_dir.'/'.$filter.'/'.$web_path;
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }

    public function getAbsolutePath()
    {
        return empty($this->file) ? null : $this->getUploadRootDir().'/'.$this->file;
    }

    public function getWebPath()
    {
        return empty($this->file) ? null : $this->getUploadDir().'/'.$this->file;
    }

    public function getUploadRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getUploadDir();
    }

    public function getUploadDir()
    {
        return $this->uploadDir;
    }
}
