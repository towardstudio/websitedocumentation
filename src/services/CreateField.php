<?php
namespace bluegg\websitedocumentation\services;

use yii\base\Component;

use Craft;
use craft\elements\Entry;
use craft\models\FieldGroup;

use craft\redactor\Field as Redactor;

class CreateField extends Component
{
	public static function create()
	{
		// Get all the fields
		$fieldsService = Craft::$app->getFields();

		// Create a new Fields Group
		$group = new FieldGroup();
		$group->name = "Document Body Content";

		// Return error if the field group cannot be saved
		if (!$fieldsService->saveGroup($group)) {
			return null;
		}

		// Get our Volume for this section
		$volume = Craft::$app
			->getVolumes()
			->getVolumeByHandle("websiteGuideImages");

		// Set the config for the field
		$config = [
			"buttons" => [
				"html",
				"formatting",
				"bold",
				"italic",
				"unorderedlist",
				"orderedlist",
				"link",
				"image",
				"video",
			],
			"formatting" => ["p"],
			"formattingAdd" => [
				"subheading" => [
					"title" => "Subheading",
					"api" => "module.block.format",
					"args" => [
						"tag" => "h3",
						"class" => "my-6",
					],
				],
				"tab-button" => [
					"title" => "Tab Button",
					"api" => "module.block.format",
					"args" => [
						"tag" => "p",
						"class" => "tab-button",
					],
				],
			],
			"plugins" => ["video"],
			"linkNewTab" => true,
			"toolbarFixed" => true,
		];

		Craft::info($volume->uid, "CharlieChecker");

		// Create a new redactor field
		$field = $fieldsService->createField([
			"type" => Redactor::class,
			"id" => null,
			"uid" => null,
			"groupId" => $group->id,
			"name" => "Document Body Field",
			"handle" => "documentBodyField",
			"columnSuffix" => null,
			"instructions" => "",
			"searchable" => false,
			"configSelectionMode" => "manual",
			"manualConfig" => json_encode($config),
			"availableVolumes" => !empty($volume) ? $volume->uid : "",
			"purifyHtml" => "",
		]);

		// Return false if the field cannot be saved
		if (!$fieldsService->saveField($field)) {
			return false;
		}
	}
}
