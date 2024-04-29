<?php
namespace towardstudio\websitedocumentation\widgets;

use towardstudio\websitedocumentation\WebsiteDocumentation;
use towardstudio\websitedocumentation\assetbundles\documentwidget\DocumentWidgetAsset;


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
		// Register Assets
		Craft::$app->getView()->registerAssetBundle(DocumentWidgetAsset::class);

		// Get all Editable Sites
		$sites = Craft::$app->sites->getEditableSites();

		// Get Settings
		$settings = WebsiteDocumentation::$settings;

		// Icon Directory
		$iconsDirectory = Craft::getAlias('@appicons');

		// Set up settings array
		$config = WebsiteDocumentation::customConfig();
		$array = [];

		foreach($sites as $site) {
			$handle = $site->handle;
			$url = WebsiteDocumentation::getDocUrl($config,$handle);

			$item = [
				"title" => $site->name,
				"guides" => [
					"styleguide" => [
						"label" => "Style Guide",
						"url" => $site->baseUrl . $url . "/style-guide",
						"icon"	=> file_get_contents($iconsDirectory . "/palette.svg"),
						"display" => $settings->sites[$handle]['displayStyleGuide'] != '1' ? false : true,
					],
					"guide" => [
						"label" => "CMS Guide",
						"url" => $site->baseUrl . $url . "/cms-guide",
						"icon"	=> file_get_contents($iconsDirectory . "/book.svg"),
						"display" => $settings->sites[$handle]['displayCmsGuide'] != '1' ? false : true,
					],
				]
			];

			$array[$handle] = $item;

		};

		// Get the documentation url
		$docUrl = getenv("DOCS_URL") ? getenv("DOCS_URL") : "website-docs";


        return Craft::$app->getView()->renderTemplate('websitedocumentation/_components/widgets/body', [
			'settings' => $array,
        ]);
    }

}
