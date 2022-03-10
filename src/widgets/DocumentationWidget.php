<?php
namespace bluegg\websitedocumentation\widgets;

use bluegg\websitedocumentation\WebsiteDocumentation;
use bluegg\websitedocumentation\assetbundles\documentwidget\DocumentWidgetAsset;


use Craft;
use craft\base\Widget;

class DocumentationWidget extends Widget
{

	/**
     * @var string The message to display
     */
    public $message = 'Hello, world.';

	 /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return 'Documentation';
    }

	/**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return '';
    }

	/**
     * Returns the path to the widget’s SVG icon.
     *
     * @return string|null The path to the widget’s SVG icon
     */

	public static function icon(): string
    {
        return Craft::getAlias('@appicons/book.svg');
    }

	/**
     * Returns the widget’s maximum colspan.
     *
     * @return int The widget’s maximum colspan
     */
    public static function maxColspan(): int
    {
        return 3;
    }

	public function getBodyHtml(): string
    {

		Craft::$app->getView()->registerAssetBundle(DocumentWidgetAsset::class);

		// Get the site Url
		$url = Craft::$app->sites->currentSite->baseUrl;

		// Get the documentation url
		$docUrl = getenv("DOCS_URL") ? getenv("DOCS_URL") : "website-docs";

		// Icon Directory
		$iconsDirectory = Craft::getAlias('@appicons');

        return Craft::$app->getView()->renderTemplate('websitedocumentation/_components/widgets/body', [
			'settings' => [
				"styleguide" => [
					"label" => "Styleguide",
					"url" => $url . $docUrl . "/styleguide",
					"icon"	=> file_get_contents($iconsDirectory . "/photo.svg"),
				],
				"guide" => [
					"label" => "CMS Guide",
					"url" => $url . $docUrl . "/guide",
					"icon"	=> file_get_contents($iconsDirectory . "/book.svg"),
				],
			],
        ]);
    }

}
