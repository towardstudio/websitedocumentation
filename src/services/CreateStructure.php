<?php
namespace bluegg\websitedocumentation\services;

use yii\base\Component;

use Craft;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use craft\helpers\StringHelper;

class CreateStructure extends Component
{
	public static function create($name)
	{
		// Create a new section for the Model
		$section = new Section();

		// Set the new section Name
		$section->name = $name;

		// Set the new section handle
		$section->handle = StringHelper::toCamelCase($name);

		// Set the section type to Stucture
		$section->type = Section::TYPE_STRUCTURE;

		// Enable versioning on the structure
		$section->enableVersioning = 1;

		// Hide previews for the structure as we won't have urls
		$section->previewTargets = [];

		// Set the Max Levels to 2. This should be default. If anymore are needed this can be changed in settings.
		$section->maxLevels = 2;

		// Set the settings for all sites, this is in case of a multisite install.
		$allSiteSettings = [];

		foreach (Craft::$app->getSites()->getAllSites() as $site) {
			$siteSettings = new Section_SiteSettings();
			$siteSettings->siteId = $site->id;

			// Remove Urls & Enable any new entries in the section by default.
			$siteSettings->uriFormat = null;
			$siteSettings->enabledByDefault = true;
			$siteSettings->hasUrls = false;

			$allSiteSettings[$site->id] = $siteSettings;
		}

		// Save the Settings to the new section
		$section->setSiteSettings($allSiteSettings);

		$success = Craft::$app->sections->saveSection($section);

		if (!$success) {
			Craft::error(
				'Couldn’t save the section "' . $name . '"',
				__METHOD__
			);

			return false;
		}
	}
}
