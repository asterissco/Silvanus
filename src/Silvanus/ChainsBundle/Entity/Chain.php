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
     * @ORM\Column(name="policy", type="string", length=255)
     */
    private $policy;

    /**
     * @var string
     *
     * @ORM\Column(name="host", type="string", length=255, nullable=TRUE)
     */
    private $host;


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
     * Set policy
     *
     * @param string $policy
     * @return Chain
     */
    public function setPolicy($policy)
    {
        $this->policy = $policy;
    
        return $this;
    }

    /**
     * Get policy
     *
     * @return string 
     */
    public function getPolicy()
    {
        return $this->policy;
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
        
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	


}
