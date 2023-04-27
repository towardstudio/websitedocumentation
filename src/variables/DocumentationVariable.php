<?php
namespace towardstudio\websitedocumentation\variables;

use towardstudio\websitedocumentation\WebsiteDocumentation;

use Craft;
use craft\helpers\UrlHelper;
use yii\di\ServiceLocator;

class DocumentationVariable extends ServiceLocator
{
	public function getUrl(string $siteHandle = null)
	{
		// Get the Config File
		$config = WebsiteDocumentation::customConfig();

		if (empty($config))
		{
			return 'website-docs';
		}

		if (isset($config['documentationUrl']) || isset($config['url'])) {
			$docUrl = isset($config['documentationUrl']) ? $config['documentationUrl'] : $config['url'];
		} elseif(isset($config[$siteHandle]['documentationUrl']) || isset($config[$siteHandle]['url'])) {
			$docUrl = isset($config[$siteHandle]['documentationUrl']) ? $config[$siteHandle]['documentationUrl'] : $config[$siteHandle]['url'];
		} else {
			$docUrl = 'website-docs';
		}

		return $docUrl;

	}

	public function getSettings(string $siteHandle = null)
	{
		if ($siteHandle) {
			return WebsiteDocumentation::$settings->sites[$siteHandle];
		} else {
			return WebsiteDocumentation::$settings;
		}
	}
}
