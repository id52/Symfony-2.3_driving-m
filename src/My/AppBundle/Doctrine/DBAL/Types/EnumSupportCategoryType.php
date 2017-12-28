<?php

namespace My\AppBundle\Doctrine\DBAL\Types;

class EnumSupportCategoryType extends EnumType
{
    protected $name = 'enumsupportcategory';
    protected $values = array('category', 'teacher');
}
