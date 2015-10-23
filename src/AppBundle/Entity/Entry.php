<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\Table(name="entry")
*/
class Entry {

	/**
	* @ORM\Column(type="string", length=8, unique=true)
	* @ORM\Id
	*/
	protected $token;

	/**
	* @ORM\Column(type="string", length=255)
	*/
	protected $url;

	/**
	* @ORM\Column(type="datetime")
	*/
	protected $createdOn;

	public function __construct() {
		$this->createdOn = new \DateTime();
	}

	/**
	* Set token
	*
	* @param string $token
	*
	* @return Entry
	*/
	public function setToken($token)
	{
		$this->token = $token;

		return $this;
	}

	/**
	* Get token
	*
	* @return string
	*/
	public function getToken()
	{
		return $this->token;
	}

	/**
	* Set url
	*
	* @param string $url
	*
	* @return Entry
	*/
	public function setUrl($url)
	{
		$this->url = $url;

		return $this;
	}

	/**
	* Get url
	*
	* @return string
	*/
	public function getUrl()
	{
		return $this->url;
	}

	/**
	* Set createdOn
	*
	* @param \DateTime $createdOn
	*
	* @return Entry
	*/
	public function setCreatedOn($createdOn)
	{
		$this->createdOn = $createdOn;

		return $this;
	}

	/**
	* Get createdOn
	*
	* @return \DateTime
	*/
	public function getCreatedOn()
	{
		return $this->createdOn;
	}
}
?>