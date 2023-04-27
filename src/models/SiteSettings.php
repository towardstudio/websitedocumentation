<?php
namespace towardstudio\websitedocumentation\models;

use towardstudio\websitedocumentation\WebsiteDocumentation;

use Craft;
use craft\base\Model;

class SiteSettings extends Model
{
	// Public Properties
	// =========================================================================

	/**
	 * @var string
	 */

	public $logo;
	public $brandBgColor;
	public $brandTextColor;
	public $accentBgColor;
	public $accentTextColor;
	public $displayStyleGuide = "1";
	public $displayCmsGuide = "1";

	// Public Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	public function rules(): array
	{
		return [
			["brandBgColor", "string"],
			["brandTextColor", "string"],
			["accentBgColor", "string"],
			["accentTextColor", "string"],
			["displayStyleGuide", "string"],
			["displayCmsGuide", "string"],
		];
	}
}
