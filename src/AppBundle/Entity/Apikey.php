<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Apikey
 *
 * @ORM\Table(name="apikey")
 * @ORM\Entity
 */
class Apikey {

	/**
	 * @var string
	 * @ORM\Id
	 * @ORM\Column(name="apikey", type="string", length=32)
	 */
	private $apikey;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="comment", type="string", length=255)
	 */
	private $comment;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="expires", type="datetime")
	 */
	private $expires;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="createdOn", type="datetime")
	 */
	private $createdOn;

	/**
	 * Create a new Apikey instance. Because the API key is also the primary key it has
	 * to be set on instantiaion.
	 * @param str $apikey API key for this instance.
	 */
	public function __construct($apikey) {
		$this->apikey = $apikey;
		$this->createdOn = new \DateTime();
	}

	/**
	 * Get apikey
	 *
	 * @return string
	 */
	public function getApikey()
	{
		return $this->apikey;
	}

	/**
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return Apikey
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;

		return $this;
	}

	/**
	 * Get comment
	 *
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}

	/**
	 * Set expires
	 *
	 * @param \DateTime $expires
	 *
	 * @return Apikey
	 */
	public function setExpires($expires)
	{
		$this->expires = $expires;

		return $this;
	}

	/**
	 * Get expires
	 *
	 * @return \DateTime
	 */
	public function getExpires()
	{
		return $this->expires;
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

