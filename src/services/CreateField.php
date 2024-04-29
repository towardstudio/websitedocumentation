<?php
namespace towardstudio\websitedocumentation\services;

use yii\base\Component;

use Craft;
use craft\db\Table;
use craft\elements\Entry;
use craft\helpers\Db;
use craft\helpers\StringHelper;
use craft\models\FieldGroup;

use craft\ckeditor\Plugin as CkEditor;
use craft\ckeditor\Field as CkEditorField;
use craft\ckeditor\CkeConfig;

use Exception;

class CreateField extends Component
{
	public static function create() : bool
	{
        // Field Service
        $fieldsService = Craft::$app->getFields();

        // Create Type of Field
        $type = CkEditorField::class;

        // Check if Config already exists
        $configs = CkEditor::getInstance()->getCkeConfigs()->getAll();

        $oldConfig = array_filter($configs, function($item) {
            return $item->name === 'Website Documentation';
        });

        // If it exists, lets delete it.
        if (!empty($oldConfig)) {
            $oldConfig = reset($oldConfig);
            CkEditor::getInstance()->getCkeConfigs()->delete($oldConfig->uid);
        }

        // Config Toolbar
        $toolbar = [
            "sourceEditing",
            "heading",
            "bold",
            "italic",
            "link",
            "|",
            "bulletedList",
            "numberedList",
            "|",
            "insertImage",
            "|",
            "undo",
            "redo"
        ];

		// Set the config for the field
        $ckeConfig = new CkeConfig([
            'uid' => StringHelper::UUID(),
            'name' => "Website Documentation",
            'toolbar' => $toolbar,
            'headingLevels' => [2, 3],
        ]);

        // Save Config
        if (!CkEditor::getInstance()->getCkeConfigs()->save($ckeConfig)) {
            throw new Exception("Couldn't save CkeConfig Settings");
        }

        // Check if Field Exists
        $fieldExists = $fieldsService->getFieldByHandle("websiteDocumentationText");

        if (!$fieldExists)
        {
            // Create a new CKEditor field
		    $field = $fieldsService->createField([
			    'type' => CkEditorField::class,
			    'id' => null,
			    'uid' => null,
			    'name' => 'Website Documentation Text',
			    'handle' => 'websiteDocumentationText',
			    'columnSuffix' => null,
			    'instructions' => '',
			    'searchable' => false,
			    'ckeConfig' => $ckeConfig->uid,
			    'availableVolumes' => '*',
			    'purifyHtml' => '',
		    ]);

		    // Return false if the field cannot be saved
		    if (!$fieldsService->saveField($field)) {
			    throw new Exception("Couldn't save field");
		    }
        } else {
            $fieldExists->ckeConfig = $ckeConfig->uid;

            // Return false if the field cannot be saved
		    if (!$fieldsService->saveField($fieldExists)) {
			    throw new Exception("Couldn't save field");
		    }
        }

        return true;
	}
}
