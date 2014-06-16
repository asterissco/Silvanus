<?php

namespace Silvanus\FirewallRulesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IpPort
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Silvanus\FirewallRulesBundle\Entity\IpPortRepository")
 */
class IpPort
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
     * @ORM\Column(name="service", type="string", length=255)
     */
    private $service;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\ManyToOne(targetEntity="TransportProtocol")
     * @ORM\JoinColumn(name="protocol_id", referencedColumnName="id")
     */
    private $protocol;

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
     * Set service
     *
     * @param string $service
     * @return IpPort
     */
    public function setService($service)
    {
        $this->service = $service;
    
        return $this;
    }

    /**
     * Get service
     *
     * @return string 
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set number
     *
     * @param integer $number
     * @return IpPort
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return IpPort
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set reference
     *
     * @param string $reference
     * @return IpPort
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    
        return $this;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set protocol
     *
     * @param \Silvanus\FirewallRulesBundle\Entity\TransportProtocol $protocol
     * @return IpPort
     */
    public function setProtocol(\Silvanus\FirewallRulesBundle\Entity\TransportProtocol $protocol = null)
    {
        $this->protocol = $protocol;
    
        return $this;
    }

    /**
     * Get protocol
     *
     * @return \Silvanus\FirewallRulesBundle\Entity\TransportProtocol 
     */
    public function getProtocol()
    {
        return $this->protocol;
    }
}