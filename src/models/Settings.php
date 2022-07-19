<?php
namespace bluegg\websitedocumentation\models;

use bluegg\websitedocumentation\WebsiteDocumentation;

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
	public $logo;
	public $brandBgColor;
	public $brandTextColor;
	public $accentBgColor;
	public $accentTextColor;

	// Public Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	public function rules(): array
	{
		return [
			["structure", "string"],
			["brandBgColor", "string"],
			["brandTextColor", "string"],
			["accentBgColor", "string"],
			["accentTextColor", "string"]
		];
	}
}
