<?php
/**
 * Created by JetBrains PhpStorm.
 * User: martinhalamicek
 * Date: 16/09/14
 * Time: 01:50
 * To change this template use File | Settings | File Templates.
 */

namespace Keboola\StorageApi\Options\Components;


class Configuration
{
	private $componentId;

	private $configurationId;

	private $configuration;

	private $name;

	private $description;

	/**
	 * @return mixed
	 */
	public function getComponentId()
	{
		return $this->componentId;
	}

	/**
	 * @param mixed $componentId
	 */
	public function setComponentId($componentId)
	{
		$this->componentId = $componentId;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getConfigurationId()
	{
		return $this->configurationId;
	}

	/**
	 * @param mixed $configurationId
	 */
	public function setConfigurationId($configurationId)
	{
		$this->configurationId = $configurationId;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}

	/**
	 * @param $configuration
	 * @return $this
	 */
	public function setConfiguration($configuration)
	{
		$this->configuration = (array) $configuration;
		return $this;
	}

}