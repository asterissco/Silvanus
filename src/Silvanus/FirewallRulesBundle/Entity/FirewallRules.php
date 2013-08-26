<?php

namespace Silvanus\FirewallRulesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * FirewallRules
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Silvanus\FirewallRulesBundle\Entity\FirewallRulesRepository")
 * @UniqueEntity("priority")
 * @UniqueEntity("rule")
 */
class FirewallRules
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
     * @ORM\Column(name="rule", type="string", length=255)
     */
    private $rule;

    /**
     * @var string
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    /**
     * @var int
     *
     * @ORM\Column(name="chain_id", type="integer")
     * @ORM\OneToOne(targetEntity="Chain", cascade={"persist", "remove"}))
     * @ORM\JoinColumn(name="chain_id", referencedColumnName="id")
     */
    private $chain_id;


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
     * Set rule
     *
     * @param string $rule
     * @return FirewallRules
     */
    public function setRule($rule)
    {
        $this->rule = $rule;
    
        return $this;
    }

    /**
     * Get rule
     *
     * @return string 
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return FirewallRules
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    
        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }



    /**
     * Set chain_id
     *
     * @param integer $chainId
     * @return FirewallRules
     */
    public function setChainId($chainId)
    {
        $this->chain_id = $chainId;
    
        return $this;
    }

    /**
     * Get chain_id
     *
     * @return integer 
     */
    public function getChainId()
    {
        return $this->chain_id;
    }
}
