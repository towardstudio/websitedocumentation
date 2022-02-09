<?php
namespace Bluegg\documentation;

use Craft;
use yii\base\Event;

/**
 * Documentation Plugin
 **/
class Plugin extends \craft\base\Plugin
{
	public $hasCpSection = true;

	public function getCpNavItem()
	{
		// Get the site Url
		$url = Craft::$app->sites->currentSite->baseUrl;

		// Get the documentation url
		$docUrl = getenv("DOCS_URL") ? getenv("DOCS_URL") : "website-docs";

		// Set additional information on the nav item
		$item = parent::getCpNavItem();

		$item["label"] = 'Documentation';
		$item["icon"] = "@app/icons/book.svg";
		$item["subnav"] = [
			"styleguide" => [
				"label" => "Styleguide",
				"url" => $url . $docUrl . '/styleguide',
				"external" => true,
			],
			"guide" => [
				"label" => "CMS Guide",
				"url" => $url . $docUrl . '/guide',
				"external" => true,
			],
		];

		return $item;
	}
}
