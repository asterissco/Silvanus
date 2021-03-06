<?php

namespace Silvanus\SyncBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Sync
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Silvanus\SyncBundle\Entity\SyncRepository")
 * @UniqueEntity("chainId")
 *  
 */
class Sync
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
     * @var integer
     *
     * @ORM\Column(name="chain_id", type="integer")
     */
    private $chainId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var boolean
     *
     * @ORM\Column(name="error", type="boolean")
     */
    private $error;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=255, nullable=TRUE)
     */
    private $action=null;

    /**
     * @var string
     *
     * @ORM\Column(name="chain_name", type="string", length=255, nullable=TRUE)
     */
    private $chainName=null;


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
     * Set chainId
     *
     * @param integer $chainId
     * @return Sync
     */
    public function setChainId($chainId)
    {
        $this->chainId = $chainId;
    
        return $this;
    }

    /**
     * Get chainId
     *
     * @return integer 
     */
    public function getChainId()
    {
        return $this->chainId;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Sync
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
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

    /**
     * Set action
     *
     * @param string $action
     * @return Sync
     */
    public function setAction($action)
    {
        $this->action = $action;
    
        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set chainName
     *
     * @param string $chainName
     * @return Sync
     */
    public function setChainName($chainName)
    {
        $this->chainName = $chainName;
    
        return $this;
    }

    /**
     * Get chainName
     *
     * @return string 
     */
    public function getChainName()
    {
        return $this->chainName;
    }
}
