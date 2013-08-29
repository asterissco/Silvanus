<?php

namespace Silvanus\ChainsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trusted
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Silvanus\ChainsBundle\Entity\TrustedRepository")
 */
class Trusted
{

    public function __construct(){
        
        $this->chains = new \Doctrine\Common\Collections\ArrayCollection();

    }
	
	
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
     *
     * @ORM\ManyToMany(targetEntity="Chain", mappedBy="trusted")
     * 
     */
	private $chains;

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
     * @return Trusted
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



    /**
     * Set chains
     *
     * @param string $chains
     * @return Trusted
     */
    public function setChains($chains)
    {
        $this->chains = $chains;
    
        return $this;
    }

    /**
     * Get chains
     *
     * @return string 
     */
    public function getChains()
    {
        return $this->chains;
    }
}
