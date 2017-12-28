<?php

namespace My\AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PassportNumberTransformer implements DataTransformerInterface
{
    public function transform($passportNumber)
    {
        $value = array('series' => '', 'number' => '');
        if ($passportNumber) {
            $a = explode(' ', $passportNumber);
            if (count($a) == 2) {
                $value = array('series' => $a[0], 'number' => $a[1]);
            }
        }
        return $value;
    }

    public function reverseTransform($value)
    {
        $passportNumber = null;
        if (is_array($value) && !empty($value['series']) && !empty($value['number'])) {
            if (!ctype_digit($value['series']) || strlen($value['series']) != 4) {
                throw new TransformationFailedException('series_invalid');
            }
            if (!ctype_digit($value['number']) || strlen($value['number']) != 6) {
                throw new TransformationFailedException('number_invalid');
            }
            $passportNumber = $value['series'].' '.$value['number'];
        }
        return $passportNumber;
    }
}
