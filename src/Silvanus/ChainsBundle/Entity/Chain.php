<?php

namespace Silvanus\ChainsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Chain
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Silvanus\ChainsBundle\Entity\ChainRepository")
 * @UniqueEntity("name")
 * @Assert\Callback(methods={"isNameValid"})
 * 
 */
class Chain
{

    public function __construct(){
        
        $this->trusted = new \Doctrine\Common\Collections\ArrayCollection();

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
     * @var string
     *
     * @ORM\Column(name="host", type="string", length=255, nullable=TRUE)
     */
    private $host;

	/**
	 *
	 * @ORM\ManyToMany(targetEntity="Trusted")
     * @ORM\JoinTable(name="chain_trusted", 
     *      joinColumns={@ORM\JoinColumn(name="chain_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="trusted_id", referencedColumnName="id")}
     *      )
	 */
	private $trusted;


    /**
     * @var boolean
     *
     * @ORM\Column(name="error", type="boolean", nullable=TRUE)
     */
    private $error;

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
     * @return Chain
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
     * Set host
     *
     * @param string $host
     * @return Chain
     */
    public function setHost($host)
    {
        $this->host = $host;
    
        return $this;
    }

    /**
     * Get host
     *
     * @return string 
     */
    public function getHost()
    {
        return $this->host;
    }


	/* validators */	
	public function isNameValid(ExecutionContextInterface $context){
	
		if(strpos($this->name,' ')){
	
			$context->addViolationAt('name', 'File name, no spaces character', array(), null);
			
		}

		if(strpos($this->name,'/')){
	
			$context->addViolationAt('name', 'File name, no "/" character', array(), null);
			
		}

		if($this->name=='silvanus_test_chain'){
	
			$context->addViolationAt('name', '"silvanus_test_chain" is reserved chain name', array(), null);
			
		}
        
		
	}

    /**
     * Set trusted
     *
     * @param string $trusted
     * @return Chain
     */
    public function setTrusted($trusted)
    {
        $this->trusted = $trusted;
    
        return $this;
    }

    /**
     * Get trusted
     *
     * @return string 
     */
    public function getTrusted()
    {
        return $this->trusted;
    }

    /**
     * Set error
     *
     * @param boolean $error
     * @return Sync
     */
    public function setError($error)
    {
        $this->error = $error;
    
        return $this;
    }

    /**
     * Get error
     *
     * @return boolean 
     */
    public function getError()
    {
        return $this->error;
    }    
    
}
