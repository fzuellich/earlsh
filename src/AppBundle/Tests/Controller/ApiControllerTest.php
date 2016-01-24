<?php
namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase {

	public function test_resolveTokenAction() {
		$client = static::createClient();
		$client->request('GET', '/api/url/resolve/b');

		// test header
		$response = $client->getResponse();
		$this->assertTrue($response->headers->contains('Content-Type','application/json'));
		$this->assertTrue($response->isSuccessful());

		// test response
		$json = json_decode($response->getContent(), true);
		$this->assertArrayHasKey('url', $json);


		$client->request('GET', '/api/url/resolve/notin');

		// test header
		$response = $client->getResponse();
		$this->assertTrue($response->headers->contains('Content-Type','application/json'));
		$this->assertTrue($response->isNotFound());

		// test response
		$json = json_decode($response->getContent(), true);
		$this->assertEquals(2, count($json));
		$this->assertArrayHasKey('exception', $json);
		$this->assertEquals('TokenNotFoundException', $json['exception']);
		$this->assertArrayHasKey('message', $json);
		$this->assertEquals('Token was not found.', $json['message']);

		$client->request('GET', '/api/url/resolve/inv_id');

		// test header
		$response = $client->getResponse();
		$this->assertTrue($response->headers->contains('Content-Type','application/json'));
		$this->assertEquals(400, $response->getStatusCode());

		// test response
		$json = json_decode($response->getContent(), true);
		$this->assertEquals(2, count($json));
		$this->assertArrayHasKey('exception', $json);
		$this->assertEquals('InvalidTokenException', $json['exception']);
		$this->assertArrayHasKey('message', $json);
		$this->assertEquals('Invalid character ´_´.', $json['message']);
	}

	public function test_shortenUrlAction() {
		$client = static::createClient();
		$client->request(
				'POST'
				, '/api/url/create'
				, array('url' => 'http://www.github.com', 'apikey' => 'e305648feb4942b0a3e4058545d52a38')
				, array()
				, array('CONTENT-TYPE' => 'application/json')
			);

		// test header
		$response = $client->getResponse();
		$this->assertTrue($response->headers->contains('Content-Type','application/json'));
		$this->assertTrue($response->isSuccessful());

		// test response
		$json = json_decode($response->getContent(), true);
		$this->assertEquals(1, count($json));
		$this->assertArrayHasKey('token', $json);
	}

	public function test_shortenUrlAction_fails_without_apikey() {
		$client = static::createClient();
		$client->request(
				'POST'
				, '/api/url/create'
				, array('url' => 'http://www.github.com')
				, array()
				, array('CONTENT-TYPE' => 'application/json')
			);

		// test header
		$response = $client->getResponse();
		$this->assertTrue($response->headers->contains('Content-Type','application/json'));
		$this->assertFalse($response->isSuccessful());
	}

	public function test_welcomeAction() {
		$client = static::createClient();
		$client->request('GET', '/api');

		// test header
		$response = $client->getResponse();
		$this->assertTrue($response->headers->contains('Content-Type','application/json'));
		$this->assertTrue($response->isSuccessful());

		// test response
		$json = json_decode($response->getContent(), true);
		$this->assertEquals(2, count($json));
		$this->assertArrayHasKey('application', $json);
		$this->assertArrayHasKey('version', $json);
	}
}

?>