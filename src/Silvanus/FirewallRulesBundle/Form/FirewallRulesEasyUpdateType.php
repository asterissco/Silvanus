<?php

namespace Silvanus\FirewallRulesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class FirewallRulesEasyUpdateType extends AbstractType
{

    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('rule')
            ->add('source','text',array(
				'label'=>'Source (IP/NETWORK)',
				'mapped'=>false,
				'required'=>false,
            ))
            ->add('destination','text',array(
				'label'=>'Destination (IP/NETWORK)',
				'mapped'=>false,
				'required'=>true,
            ))
			->add('protocol', 'entity', array(
				'class' => 'SilvanusFirewallRulesBundle:TransportProtocol',
				'mapped' => false,
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('t')
						->orderBy('t.name', 'ASC');
					},
				//'data' => $this->defaults['protocol'],
			))
            ->add('source_port','text',array(
				'label'=>'Source port (type number or find service)',
				'mapped'=>false,
				'required'=>false,
            ))
            ->add('destination_port','text',array(
				'label'=>'Destination port (type number or find service)',
				'mapped'=>false,
				'required'=>false,
            ))
            ->add('interface_input','text',array(
				'label'=>'Input Network Interface',
				'mapped'=>false,
				'required'=>false,
            ))
            ->add('interface_output','text',array(
				'label'=>'Output Network Interface',
				'mapped'=>false,
				'required'=>false,
            ))
            ->add('action','choice',array(
				'label'=>'Action',
				'mapped'=>false,
				'required'=>true,
				'choices'=>array(
					'ACCEPT' => 'ACCEPT',
					'DROP' => 'DROP',
					'REJECT' => 'REJECT',
				)
            ))
			->add('priority')
			//~ ->add('append','checkbox',array(
				//~ 'label'=>'Append (set in the last)',
				//~ 'mapped'=>false,
				//~ 'required'=>false,
				//~ )
			//~ )
			->add('force','checkbox',array(
				'label'=>'Force priority',
				'mapped'=>false,
				'required'=>false,
				)
			)
		;

		//~ ->add('protocol','choice',array(
			//~ 'label'=>'Protocol',
			//~ 'mapped'=>false,
			//~ 'required'=>false,
			//~ 'choices'=>array(
				//~ 'tcp'=> 'TCP',
				//~ 'udp'=> 'UDP',
				//~ 'udplite'=> 'UDPLITE',
				//~ 'icmp'=> 'ICMP',
				//~ 'esp'=> 'ESP',
				//~ 'ah'=> 'AH',
				//~ 'sctp'=> 'SCTP',
				//~ 'all'=> 'ALL',
			//~ )
		//~ ))


    }

	// -s ip -d ip -sport -dport 

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Silvanus\FirewallRulesBundle\Entity\FirewallRules'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'silvanus_firewallrulesbundle_firewallrulestype';
    }
}
