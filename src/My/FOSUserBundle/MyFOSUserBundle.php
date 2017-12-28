<?php

namespace My\FOSUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MyFOSUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
