<?php

namespace Silvanus\FirewallRulesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransportProtocol
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Silvanus\FirewallRulesBundle\Entity\TransportProtocolRepository")
 */
class TransportProtocol
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return TransportProtocol
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
    public function __toString(){
		
			return $this->name;
	}
    
}
