<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CertificateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('title')
            ->add('certificator')
            ->add('about')
            ->add('cv')
            ->add('date', null, array(
                'label' => 'begin',
                'date_format' => 'yyyy-MM-dd',
                'date_widget' => 'single_text',
                'attr' => array('class' => 'published_at_date')
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Certificate'
        ));
    }
}
