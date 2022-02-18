<?php
namespace bluegg\websitedocumentation\services;

use yii\base\Component;

use Craft;
use craft\helpers\StringHelper;
use craft\helpers\Json;

use craft\fieldlayoutelements\CustomField;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;

class UpdateEntryType extends Component
{
	public static function update($structure)
	{
		// Get our default entry type
		$entryType = $structure->getEntryTypes()[0];

		// Get our new field
		$bodyField = Craft::$app->fields->getFieldByHandle("documentBodyField");

		$entryLayout = $entryType->getFieldLayout();

        // Create a new field Layout
		$layout = new FieldLayout();
		$layout->id = $entryLayout->id;
		$tabs = [];
		$fields = [];
		$tabSortOrder = 0;

        // Create a new default layout tab
		$tab = $tabs[] = new FieldLayoutTab();
		$tab->name = "Content";
		$tab->sortOrder = 1;
		$tab->elements = [];

		$elementConfigs = Json::encode([
			"type" => CustomField::class,
			"fieldUid" => $bodyField->uid,
		]);

		$elementConfig = Json::decode($elementConfigs);
        
        // Add the config to the layout
		$layoutElement = Craft::$app->fields->createLayoutElement(
			$elementConfig
		);

		$tab->elements[] = $layoutElement;
        
        // Add the field to the layout
		$fieldUid = $layoutElement->getFieldUid();
		$field = Craft::$app->fields->getFieldByUid($fieldUid);
		if (!$field) {
			throw new BadRequestHttpException("Invalid field UUID: $fieldUid");
		}
		$field->required = (bool) ($elementConfig["required"] ?? false);
		$field->sortOrder = 1;
		$fields[] = $field;

		$layout->setTabs($tabs);
		$layout->setFields($fields);
		$entryType->setFieldLayout($layout);

        // Save the entry type
		if (!Craft::$app->sections->saveEntryType($entryType)) {
			throw new \Exception(
				implode(" ", $entryType->getErrorSummary(true))
			);
		}
	}
}
