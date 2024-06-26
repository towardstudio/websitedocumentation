<?php
namespace towardstudio\websitedocumentation\services;

use yii\base\Component;

use Craft;
use craft\elements\Entry;
use craft\helpers\StringHelper;
use craft\web\View;

use towardstudio\websitedocumentation\data\DefaultEntries;

use Exception;

class CreateEntries extends Component
{
	public static function create($structure)
	{
		// Get all Default Entries we want to add to the structure
		$entries = DefaultEntries::entries();

		// Loop through the entries so we can create them
		foreach ($entries as $item) {
			// Get the slug of the item. We'll use this to check whether the page already exists & get the default content
			$slug = StringHelper::toKebabCase($item['title']);

			// Lets check if the entry already exists in the section
			$entryExists = Entry::find()
				->section($structure->handle)
				->slug($slug)
				->one();

			// If the entry doesn't exist, we'll create it
			if (empty($entryExists)) {
				// Create a new element
				$entry = new Entry();

				// Assign the entry to our new structure
				$entry->sectionId = $structure->id;

				// Set the Type to 1, so it takes on the default entry type
				$entry->typeId = $structure->getEntryTypes()[0]->id;

				// Enable the entry
				$entry->enabled = true;

				// Set the entry title
				$entry->title = $item['title'];

				// Check to see if any default content exists for this page
				if (Craft::$app->view->doesTemplateExist('websitedocumentation/content/' . $slug)) {
					// We need to render the template if it exists
					$defaultContent = Craft::$app->view->renderTemplate(
						'websitedocumentation/content/' . $slug
					);

					// If the data exists, lets add it to the field
					$entry->setFieldValues([
						'websiteDocumentationText' => $defaultContent,
					]);
				}

				// Save the entry
				$success = Craft::$app->elements->saveElement($entry);

				// If the save fails, lets throw an error
				if (!$success) {
                    throw new Exception('Couldn’t save the entry "' . $entry->title . '"');
					return false;
				}

				// If children are set we need to loop through and save these as well
				if (isset($item['children'])) {
					foreach ($item['children'] as $child) {
						// Create a new element
						$childEntry = new Entry();

						// Assign the entry to our new structure
						$childEntry->sectionId = $structure->id;

						// Set the Type to 1, so it takes on the default entry type
						$childEntry->typeId = $structure->getEntryTypes()[0]->id;

						// Enable the entry
						$childEntry->enabled = true;

						// Set the entry title
						$childEntry->title = $child;

						// Set the parent to the original item ID
						$childEntry->parentId = $entry->id;

						// Check to see if any default content exists for this page
						if (
							Craft::$app->view->doesTemplateExist(
								'websitedocumentation/content/' .
									$slug .
									'/' .
									StringHelper::toKebabCase($child)
							)
						) {
							// We need to render the template if it exists
							$defaultContent = Craft::$app->view->renderTemplate(
								'websitedocumentation/content/' .
									$slug .
									'/' .
									StringHelper::toKebabCase($child)
							);

							// If the data exists, lets add it to the field
							$childEntry->setFieldValues([
								'websiteDocumentationText' => $defaultContent,
							]);
						}

						// Save the entry
						$success = Craft::$app->elements->saveElement($childEntry);

						// If the save fails, lets throw an error
						if (!$success) {
							throw new Exception('Couldn’t save the entry: "' . $childEntry->title . '"');
							return false;
						}
					}
				}
			}
		}
	}
}
