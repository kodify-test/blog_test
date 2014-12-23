<?php
namespace Kodify\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('save', 'submit', ['attr' => ['class' => 'btn btn-success']]);
    }

    public function getName()
    {
        return 'author';
    }
}