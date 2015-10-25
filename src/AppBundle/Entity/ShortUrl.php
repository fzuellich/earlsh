<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\Table(name="shorturl")
*/
class ShortUrl {

	/**
	* @ORM\Column(type="string", length=8, unique=true)
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $id;

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
	* @param string $id
	*
	* @return Entry
	*/
	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}

	/**
	* Get id
	*
	* @return string
	*/
	public function getId()
	{
		return $this->id;
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