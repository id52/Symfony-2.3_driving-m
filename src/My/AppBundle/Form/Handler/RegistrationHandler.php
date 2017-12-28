<?php

namespace My\AppBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseRegHandler;
use Symfony\Component\Form\FormError;

class RegistrationHandler extends BaseRegHandler
{
    public function process($confirmation = false)
    {
        $user = $this->createUser();
        $this->form->setData($user);

        $this->form->handleRequest($this->request);

        if ($this->form->isValid()) {
            $region = $this->form->get('region')->getData();
            $category = $this->form->get('category')->getData();
            if ($category) {
                $prices = $category->getRegionsPrices();
                foreach ($prices as $price) {
                    /** @var $price \My\AppBundle\Entity\CategoryPrice */

                    if ($price->getActive() && $price->getRegion() == $region) {
                        $this->onSuccess($user, $confirmation);
                        return true;
                    }
                }
                $this->form->get('category')->addError(new FormError('Такой категории нет в выбранном регионе.'));
            } else {
                $this->form->get('category')->addError(new FormError('Значение не должно быть пустым.'));
            }
        }
        return false;
    }
}
