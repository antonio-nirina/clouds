<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

   	/**
	 * @ORM\Column(name="temporary_pwd", type="boolean")
	 */
   	protected $temporaryPwd;

   
   	/**
    * @ORM\PrePersist
    */
   	public function initTemporaryPwd()
   	{
   		return $this->setTemporaryPwd(false);
   	}
   
   	/**
     * Set temporaryPwd
     *
     * @param boolean $temporaryPwd
     *
     * @return User
     */
    public function setTemporaryPwd($temporaryPwd)
    {
        $this->temporaryPwd = $temporaryPwd;

        return $this;
    }

    /**
     * Get temporaryPwd
     *
     * @return boolean
     */
    public function getTemporaryPwd()
    {
        return $this->temporaryPwd;
    }

}
