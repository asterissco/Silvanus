<?php

namespace Silvanus\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserCreateType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('username')
            ->add('email')
            ->add('password','repeated',array(
				'type' => 'password',
				'invalid_message' => 'The password fields must match.',
				'options' => array('attr' => array('class' => 'password-field')),
				'required' => true,
				'first_options'  => array('label' => 'Password'),
				'second_options' => array('label' => 'Repeat Password'),				
            ))
            ->add('roles')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Silvanus\SecurityBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'silvanus_securitybundle_user';
    }
}
