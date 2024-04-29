<?php
namespace towardstudio\websitedocumentation\services;

use yii\base\Component;

use Craft;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use craft\helpers\StringHelper;

use Exception;

class CreateStructure extends Component
{
	public static function create($name)
	{
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

        // Create new Section
        $section = new Section([
            'name' => $name,
            'handle' => StringHelper::toCamelCase($name),
            'type' => Section::TYPE_STRUCTURE,
            'enableVersioning' => true,
            'previewTargets' => [],
            'siteSettings' => $allSiteSettings,
        ]);

        // Add our entry type
        $sectionsService = Craft::$app->getEntries();
        $entryType = $sectionsService->getEntryTypeByHandle("websiteDocumentationContent");
        $section->setEntryTypes([$entryType]);

		$success = Craft::$app->entries->saveSection($section);

		if (!$success) {
			throw new Exception("Couldn't save Structure");
			return false;
		}

	}
}
