<?php
namespace bluegg\websitedocumentation\widgets;

use bluegg\websitedocumentation\WebsiteDocumentation;
use bluegg\websitedocumentation\assetbundles\documentwidget\DocumentWidgetAsset;


use Craft;
use craft\base\Widget;

class DocumentationWidget extends Widget
{

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

	public static function icon()
    {
        return Craft::getAlias('@appicons/book.svg');
    }

	/**
     * Returns the widget’s maximum colspan.
     *
     * @return int|null The widget’s maximum colspan, if it has one
     */
    public static function maxColspan()
    {
        return null;
    }

	// Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge(
            $rules,
            [
                ['message', 'string'],
                ['message', 'default', 'value' => 'Hello, world.'],
            ]
        );
        return $rules;
    }

	public function getBodyHtml()
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
