<?php
namespace towardstudio\websitedocumentation\services;

use yii\base\Component;

use Craft;
use craft\fieldlayoutelements\CustomField;
use craft\helpers\StringHelper;
use craft\helpers\Json;
use craft\models\EntryType;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;

class CreateEntryType extends Component
{
	public static function create()
	{
        // Section Service
        $sectionsService = Craft::$app->getEntries();

        // Entry Type exists?
        $entryType = $sectionsService->getEntryTypeByHandle("websiteDocumentationContent");

        if (!$entryType)
        {
            $entryType = new EntryType();
        }

        // Set the simple stuff
        $entryType->name = "Website Documentation Content";
        $entryType->handle = "websiteDocumentationContent";
        $entryType->icon = "book";
        $entryType->color = null;
        $entryType->hasTitleField = "1";
        $entryType->showSlugField = "1";
        $entryType->showStatusField = "1";

        // Get our new field
	    $bodyField = Craft::$app->fields->getFieldByHandle("websiteDocumentationText");

        if (!$bodyField) {
            throw new Exception("Missing Field");
		    return false;
        }

        // Create a new field Layout
	    $layout = new FieldLayout();
	    $tabs = [];
	    $fields = [];
	    $tabSortOrder = 0;

        // Create a new default layout tab
	    $tab = $tabs[] = new FieldLayoutTab([
		    'layout' => $layout,
	    ]);
	    $tab->name = "Content";
	    $tab->sortOrder = 1;
	    $tab->elements = [];

	    $elementConfigs = Json::encode([
		    "type" => CustomField::class,
		    "fieldUid" => $bodyField->uid,
	    ]);

	    $elementConfig = Json::decode($elementConfigs);

	    $fieldsService = Craft::$app->getFields();
	    $layoutElement = $fieldsService->createLayoutElement($elementConfig);

	    $tab->setElements([$layoutElement]);

        // Add the field to the layout
	    $fieldUid = $layoutElement->getFieldUid();
	    $field = Craft::$app->fields->getFieldByUid($fieldUid);
	    if (!$field) {
		    throw new BadRequestHttpException("Invalid field UUID: $fieldUid");
	    }
	    $field->required = (bool) ($elementConfig["required"] ?? false);
	    $fields[] = $field;

	    $layout->setTabs($tabs);


        // Add Layout to Entry Type
        $entryType->setFieldLayout($layout);


        // Save it
        if (!$sectionsService->saveEntryType($entryType)) {
            throw new Exception("Couldn't save entry type");

		    return false;
        }

	}
}
