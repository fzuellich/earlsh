<?php
namespace AppBundle\Tests\ShortUrl;

use AppBundle\ShortUrl\ShortUrlService;

class ShortUrlServiceTest extends \PHPUnit_Framework_TestCase {

	protected $service;

	public function setUp() {
		$urlRepo = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
		$urlRepo->expects($this->any())->method('find')->willReturn(False);

		// Last, mock the EntityManager to return the mock of the repository
		$em = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
			->disableOriginalConstructor()->getMock();
		$em->expects($this->any())->method('getRepository')->willReturn($urlRepo);

		$this->service = new ShortUrlService($em);
	}

	// ////////////////////////////////////////////////////////////////////////
	//	ShortUrlService::is_valid_url($url)
	// ////////////////////////////////////////////////////////////////////////

	public function test_is_valid_url() {
		$result = ShortUrlService::is_valid_url('');
		$this->assertFalse($result);

		$result = ShortUrlService::is_valid_url(123);
		$this->assertFalse($result);

		$result = ShortUrlService::is_valid_url('www.notvalid.com');
		$this->assertFalse($result);

		// valid urls
		$result = ShortUrlService::is_valid_url('http://without.www');
		$this->assertTrue($result);

		$result = ShortUrlService::is_valid_url('http://www.normal.com/');
		$this->assertTrue($result);

		$result = ShortUrlService::is_valid_url('http://www.normal.com/index.php');
		$this->assertTrue($result);

		$result = ShortUrlService::is_valid_url('http://www.normal.com/?id=helloWorld');
		$this->assertTrue($result);
	}

	// ////////////////////////////////////////////////////////////////////////
	//	ShortUrlService#is_url_in_database($url)
	// ////////////////////////////////////////////////////////////////////////

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_is_url_in_database_invalid_url() {
		$this->service->is_url_in_database('notavalidurl');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_is_url_in_database_empty_string() {
		$this->service->is_url_in_database('');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_is_url_in_database_invalid_datatype() {
		$this->service->is_url_in_database(123);
	}

	public function test_is_url_in_database() {
		// First, mock the object to be used in the test
		$url = $this->getMock('\AppBundle\Entity\ShortUrl');
		$url->expects($this->any())->method('getUrl')->willReturn('http://inthedatabase.com');
		$url->expects($this->any())->method('getId')->willReturn(12345);

		// Now, mock the repository so it returns the mock of the employee
		$urlRepo = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
		$urlRepo->expects($this->any())->method('findOneByUrl')->willReturn($url);

		// Last, mock the EntityManager to return the mock of the repository
		$em = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
		$em->expects($this->any())->method('getRepository')->willReturn($urlRepo);

		$service = new ShortUrlService($em);
		$result = $service->is_url_in_database('http://inthedatabase.com');

		// Last, mock the EntityManager to return the mock of the repository
		$em = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
		$em->expects($this->any())->method('getRepository')->willReturn($urlRepo);

		$service = new ShortUrlService($em);
		$result = $service->is_url_in_database('http://www.notinthedatabase.com');
		$this->assertFalse($result);
	}

	// ////////////////////////////////////////////////////////////////////////
	//	ShortUrlService#get_token_for_url($url)
	// ////////////////////////////////////////////////////////////////////////

	public function test_get_token_for_url() {
		//// First, mock the object to be used in the test
		$url = $this->getMock('\AppBundle\Entity\ShortUrl');
		$url->expects($this->any())->method('getUrl')->willReturn('http://inthedatabase.com');
		$url->expects($this->any())->method('getId')->willReturn(12345);

		// Now, mock the repository so it returns the mock of the employee
		$urlRepo = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
		$urlRepo->expects($this->any())->method('find')->willReturn($url);
		$urlRepo->expects($this->any())->method('findOneByUrl')->willReturn(True);

		// Last, mock the EntityManager to return the mock of the repository
		$em = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
		$em->expects($this->any())->method('getRepository')->willReturn($urlRepo);

		$service = new ShortUrlService($em);
		$result = $service->get_token_for_url('http://inthedatabase.com');
		$this->assertEquals('dnh', $result);
	}

	/**
	 * @expectedException AppBundle\ShortUrl\Exception\UrlNotFoundException
	 */
	public function test_get_token_for_url_not_found() {
		$this->service->get_token_for_url('http://www.notinthedatabase.com');
	}

	// ////////////////////////////////////////////////////////////////////////
	//	ShortUrlService#shorten_url($url)
	// ////////////////////////////////////////////////////////////////////////

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_shorten_url_forbidden_javascript() {
		$result = $this->service->shorten_url('javascript:alert("Hello World!");');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_shorten_url_invalid_url() {
		$result = $this->service->shorten_url('www.something');
	}

	public function test_shorten_url() {
		// we test if persistence works and the following conversion of the generated
		// primary key. Because there will be no generatio of a pk when mocking doctrine
		// the TokenConverter will throw an exception of not getting an integer as argument.
		$this->fail('Test me with a real database!');

		#$result = $this->service->shorten_url('http://www.shorten-url.com/index.php');
		#$this->assertTrue(is_string($result));
		#$this->assertFalse(empty($result));
		#$this->assertEquals('dnh', $result);
	}

	// ////////////////////////////////////////////////////////////////////////
	//	ShortUrlService#resolve_token($url)
	// ////////////////////////////////////////////////////////////////////////

	/**
	 * @expectedException AppBundle\ShortUrl\Exception\InvalidTokenException
	 */
	public function test_resolve_token_invalid_token() {
		$this->service->resolve_token('dnhä');
	}

	/**
	 * @expectedException AppBundle\ShortUrl\Exception\TokenNotFoundException
	 */
	public function test_resolve_token_not_found() {
		// Now, mock the repository so it returns the mock of the employee
		$repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
		$repository->expects($this->any())->method('find')->willReturn(False);

		// Last, mock the EntityManager to return the mock of the repository
		$em = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
		$em->expects($this->any())->method('getRepository')->willReturn($repository);

		// test
		$service = new ShortUrlService($em);
		$service->resolve_token('asdf1234');
	}

	public function test_resolve_token() {
		// First, mock the object to be used in the test
		$url = $this->getMock('\AppBundle\Entity\ShortUrl');
		$url->expects($this->any())->method('getUrl')->willReturn('http://inthedatabase.com');
		$url->expects($this->any())->method('getId')->willReturn(12345);

		// Now, mock the repository so it returns the mock of the employee
		$urlRepo = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
		$urlRepo->expects($this->any())->method('findOneByUrl')->willReturn($url);
		$urlRepo->expects($this->any())->method('find')->willReturn($url);

		// Last, mock the EntityManager to return the mock of the repository
		$em = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
		$em->expects($this->any())->method('getRepository')->willReturn($urlRepo);

		// test
		$service = new ShortUrlService($em);
		$result = $service->resolve_token('dnh');
		$this->assertTrue(is_string($result));
		$this->assertFalse(empty($result));
		$this->assertTrue(ShortUrlService::is_valid_url($result));
		$this->assertTrue($service->is_url_in_database($result));
		$this->assertEquals('http://inthedatabase.com', $result);
	}
}

?>