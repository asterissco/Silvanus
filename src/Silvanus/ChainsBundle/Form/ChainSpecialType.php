<?php

namespace Silvanus\ChainsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

//~ use Symfony\Component\Form\CallbackValidator;
//~ use Symfony\Component\Form\FormError;
//~ use Symfony\Component\Form\FormInterface;



class ChainSpecialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name');
            //~ ->add('active')
            //~ ->add('host')
            //~ ->add('trusted')
            //~ ->add('type','choice',array(
				//~ 'label' => 'Chain type',
				//~ 'required'=>true,
				//~ 'choices'=>array(
					//~ 'normal' => 'Normal',
					//~ 'special' => 'Special',
				//~ )				
            //~ ));

    
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Silvanus\ChainsBundle\Entity\Chain'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'silvanus_chainsbundle_chaintype';
    }
}
