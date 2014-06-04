<?php

namespace Silvanus\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Role
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Silvanus\SecurityBundle\Entity\RoleRepository")
 */
class Role implements RoleInterface
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
     * @ORM\Column(name="role", type="string", length=255)
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

	/**
	 * 
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
	 *
	 */
	private $users;

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
     * Set role
     *
     * @param string $name
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Role
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
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add users
     *
     * @param \Silvanus\SecurityBundle\Entity\user $users
     * @return Role
     */
    public function addUser(\Silvanus\SecurityBundle\Entity\user $users)
    {
        $this->users[] = $users;
    
        return $this;
    }

    /**
     * Remove users
     *
     * @param \Silvanus\SecurityBundle\Entity\user $users
     */
    public function removeUser(\Silvanus\SecurityBundle\Entity\user $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
    
    public function __toString(){
		return $this->role;
	}
 
	//~ public function getRole(){
		//~ return $this->role;	
	//~ }
    
}
