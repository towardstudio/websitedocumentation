<?php
namespace bluegg\websitedocumentation\variables;

use bluegg\websitedocumentation\WebsiteDocumentation;

use Craft;
use craft\helpers\UrlHelper;
use yii\di\ServiceLocator;

class DocumentationVariable extends ServiceLocator
{
	public function getUrl()
	{
		$config = WebsiteDocumentation::customConfig();
		if (empty($config))
		{
			return 'website-docs';
		} else {
			if ($config['documentationUrl']) {
				return $config['documentationUrl'];
			} else {
				return 'website-docs';
			}
		}
	}

	public function getSettings()
	{
		return WebsiteDocumentation::$settings;
	}
}
