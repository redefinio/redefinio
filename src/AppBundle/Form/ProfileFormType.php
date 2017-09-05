<?php
/**
 * Created by PhpStorm.
 * User: svidleo
 * Date: 05/09/2017
 * Time: 14:10
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('username');
        $builder->remove('email');

        $builder->add('plainPassword',RepeatedType::class, array(
        'type' => PasswordType::class,
        'options' => array('translation_domain' => 'FOSUserBundle'),
        'first_options' => array('label' => 'form.password'),
        'second_options' => array('label' => 'form.password_confirmation'),
        'invalid_message' => 'fos_user.password.mismatch',
    ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_profile';
    }

}