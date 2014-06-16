<?php

namespace Silvanus\FirewallRulesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IpPortType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('service')
            ->add('number')
            ->add('description')
            ->add('reference')
            ->add('protocol')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Silvanus\FirewallRulesBundle\Entity\IpPort'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'silvanus_firewallrulesbundle_ipport';
    }
}
