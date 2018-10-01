<?php

declare(strict_types=1);

namespace IIS\Configurator;

final class Configurator extends \Nette\Configurator
{
	public const SECRET_DEBUG_NAME = 'IIS_DEBUG';

	private const CONFIG_KEY_IS_PRODUCTION_DOMAIN = 'isProductionDomain';


	public function __construct(string $rootDir)
	{
		parent::__construct();
		$this->addParameters([
			'rootDir' => $rootDir,
			'appDir' => "$rootDir/app",
			'logDir' => "$rootDir/log",
			'tempDir' => "$rootDir/temp",
			'wwwDir' => "$rootDir/www",
			self::CONFIG_KEY_IS_PRODUCTION_DOMAIN => \IIS\Configurator\Helpers::detectProductionDomain(),
		]);
	}


	public function loadConfigs(
		array $configs = [],
		?string $configDir = null,
		string $productionConfig = 'production',
		string $developmentConfig = 'development',
		string $debugConfig = 'debug'
	): self {
		if (!$configs) {
			$configs = ['config'];
		}
		if (!$configDir) {
			$configDir = $this->parameters['appDir'] . '/config';
		}

		$configs[] = $this->parameters[self::CONFIG_KEY_IS_PRODUCTION_DOMAIN] ? $productionConfig : $developmentConfig;
		foreach ($configs as $config) {
			$path = "$configDir/$config.neon";
			if (\is_readable($path)) {
				$this->addConfig($path);
			}
		}

		if ($this->isDebugMode()) {
			$debugPath = "$configDir/$debugConfig.neon";
			if (\is_readable($debugPath)) {
				$this->addConfig($debugPath);
			}
		}

		return $this;
	}
}
