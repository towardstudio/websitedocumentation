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

	// Public Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [["structure", "string"]];
	}
}
