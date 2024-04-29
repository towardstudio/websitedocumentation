<?php
namespace towardstudio\websitedocumentation\services;

use yii\base\Component;

use Craft;
use craft\elements\Entry;
use craft\helpers\StringHelper;
use craft\models\FieldGroup;

use craft\ckeditor\Plugin as CkEditor;
use craft\ckeditor\Field as CkEditorField;
use craft\ckeditor\CkeConfig;

use Exception;

class CreateField extends Component
{
	public static function create()
	{
		// Get all the fields
		$fieldsService = Craft::$app->getFields();

		// Check the Fields Group doesn't exist
		$fieldGroups = $fieldsService->getAllGroups();
		$groupExists = array_filter($fieldGroups, function ($key) {
			return $key['name'] == 'Document Body Content';
		});

		// Create a new Fields Group
		if (empty($groupExists)) {
			$group = new FieldGroup();
			$group->name = 'Document Body Content';
		} else {
			$groupExists = reset($groupExists);
			$group = $fieldsService->getGroupById($groupExists->id);
		}

		// Return error if the field group cannot be saved
		if (!$fieldsService->saveGroup($group)) {
			throw new Exception('Missing Group');
		}

		// Set the config for the field
		$config = [
			'uid' => StringHelper::UUID(),
			'name' => 'Document Body Content',
			'toolbar' => [
				'sourceEditing',
				'heading',
				'bold',
				'italic',
				'|',
				'bulletedList',
				'numberedList',
				'|',
				'insertImage',
				'mediaEmbed',
				'|',
				'undo',
				'redo',
			],
			'headingLevels' => [2, 3],
		];

		$ckeConfig = new CkeConfig($config);
		$success = CkEditor::getInstance()
			->getCkeConfigs()
			->save($ckeConfig);
		$ckeConfigUid = $ckeConfig->uid;

		// Create a new CKEditor field
		$field = $fieldsService->createField([
			'type' => CkEditorField::class,
			'id' => null,
			'uid' => null,
			'groupId' => $group->id,
			'name' => 'Document Body Field',
			'handle' => 'documentBodyField',
			'columnSuffix' => null,
			'instructions' => '',
			'searchable' => false,
			'ckeConfig' => $ckeConfigUid,
			'availableVolumes' => '*',
			'purifyHtml' => '',
		]);

		// Return false if the field cannot be saved
		if (!$fieldsService->saveField($field)) {
			throw new Exception("Couldn't save field");
		}
	}
}
