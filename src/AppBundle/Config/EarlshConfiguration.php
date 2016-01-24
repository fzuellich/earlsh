<?php

namespace AppBundle\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

/**
 * Class that should be used as a service to access the earlsh specific configuration
 * files.
 */
class EarlshConfiguration implements ConfigurationInterface {

	private $configdata;
	private $locator;

	/**
	 * Create a new instance and locate the earlsh configuration files.
	 * @param String $config_directory The path to the earlsh.yml file.
	 */
	public function __construct($config_directory) {
		$this->locator = new FileLocator($config_directory);
		$configfile = $this->locator->locate('earlsh.yml', null, true);

		$processor = new Processor();
		$this->configdata = $processor->processConfiguration($this, Yaml::parse($configfile));
		$this->createRegexPatterns();
	}

	protected function createRegexPatterns() {
		$patterns = array();
		foreach ($this->configdata['rejected_sites'] as $regex) {
			$patterns[] = sprintf('#%s#i', $regex);
		}

		$this->configdata['rejected_sites'] = $patterns;
	}

	public function isPreventLocalUrls() {
		return $this->configdata['prevent_local_urls'] === true;
	}

	public function getHostname() {
		return $this->configdata['hostname'];
	}

	public function getRejectedSites() {
		return $this->configdata['rejected_sites'];
	}

	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('shortener');

		$rootNode
			->children()
				->booleanNode('prevent_local_urls')
					->defaultTrue()
				->end()
				->scalarNode('hostname')
				->end()
				->arrayNode('rejected_sites')
					->prototype('scalar')->end()
				->end()
			->end();

		return $treeBuilder;
	}

}

?>