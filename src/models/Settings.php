<?php
namespace towardstudio\websitedocumentation\models;

use towardstudio\websitedocumentation\WebsiteDocumentation;

use Craft;
use craft\base\Model;

class Settings extends Model
{
	// Public Properties
	// =========================================================================

	/**
	 * @var string
	 */

	public $name = "Website Docs";
	public $structure;
	public $structureExists;
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
			["structure", "string"],
			["structureExists", "boolean"],
			["brandBgColor", "string"],
			["brandTextColor", "string"],
			["accentBgColor", "string"],
			["accentTextColor", "string"],
			["displayStyleGuide", "string"],
			["displayCmsGuide", "string"],
		];
	}
}
