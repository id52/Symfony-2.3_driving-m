<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('url', 'text', array('help' => 'article_url_help'))
            ->add('keywords', null, array('required' => false))
            ->add('description', null, array('required' => false))
            ->add('text', 'textarea', array('attr' => array('class' => 'ckeditor'), 'help' => 'article_placeholders'))
        ;
    }

    public function getName()
    {
        return 'article';
    }
}
