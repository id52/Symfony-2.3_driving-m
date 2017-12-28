<?php

namespace My\AppBundle\Util;

class Time
{
    /**
     * @param $object     \DateInterval
     * @return int
     */
    public static function getAllSeconds($object = null)
    {
        if ($object && $object instanceof \DateInterval) {
            $d = $object->days;
            $h = $object->h;
            $i = $object->i;
            $s = $object->s;

            $allSeconds = $s + $i * 60 + $h * 3600 + $d * 86400;

            return $allSeconds;
        }

        return 0;
    }

    /**
     * Returns the absolute(always positive) difference between two DateTime objects in seconds.
     * @param \DateTime $first The date to compare to.
     * @param \DateTime $second [optional] If not passed, $first compared with current DateTime.
     * @return integer representing time difference in seconds, or 0 seconds if parameters wrong.
     */
    public static function getDiffInSeconds($first = null, $second = null)
    {
        if ((!$first || !($first instanceof \DateTime)) || ($second && !($second instanceof \DateTime))) {
            return 0;
        }

        if ($second) {
            return self::getAllSeconds($first->diff($second, true));
        } else {
            $now = new \DateTime();
            return self::getAllSeconds($first->diff($now, true));
        }
    }
}
