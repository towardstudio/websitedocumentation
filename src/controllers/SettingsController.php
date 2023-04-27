<?php
namespace towardstudio\websitedocumentation\controllers;

use towardstudio\websitedocumentation\WebsiteDocumentation;
use towardstudio\websitedocumentation\services\CreateField;
use towardstudio\websitedocumentation\services\CreateStructure;
use towardstudio\websitedocumentation\services\UpdateEntryType;
use towardstudio\websitedocumentation\services\CreateEntries;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use craft\helpers\StringHelper;
use craft\services\Volumes;
use craft\volumes\Local;

use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SettingsController extends Controller
{
	// Protected Properties
	// =========================================================================

	protected array|bool|int $allowAnonymous = [];

	// Public Methods
	// =========================================================================

	/**
	 * Plugin settings
	 *
	 * @param null|bool|Settings $settings
	 *
	 * @return Response The rendered result
	 * @throws \yii\web\ForbiddenHttpException
	 */
	public function actionPluginSettings(string $siteHandle = null, $settings = null): Response
	{
		if ($settings === null) {
			$settings = WebsiteDocumentation::$settings;
		}

		if ($siteHandle === null) {
			$siteHandle = Craft::$app->sites->primarySite->handle;
		}

		$siteId = $this->getSiteIdFromHandle($siteHandle);
		$section = Craft::$app->request->getSegment(3);

		if ($settings["structure"]) {
			// Check if Structure Exists
			$sectionRequired = Craft::$app->sections->getSectionByHandle(
				StringHelper::toCamelCase($settings["structure"])
			);

			if ($sectionRequired != null) {
				$settings["structureExists"] = true;
			} else {
				$settings["structureExists"] = false;
			}
		} else {
			$settings["structureExists"] = false;
		}

		// Basic variables
		$variables["fullPageForm"] = true;
		$variables["selectedSubnavItem"] = "settings";
		$variables["settings"] = $settings;

		$variables['controllerHandle'] = 'settings' . '/' . $section;
		$this->setMultiSiteVariables($siteHandle, $siteId, $variables);

		// Logo
		$logo = null;

		if ($settings->sites[$siteHandle]['logo']) {
			foreach ($settings->sites[$siteHandle]['logo'] as $item) {
				$logo = Craft::$app->elements->getElementById($item);
			}
		}

		$variables["logo"] = $logo;

		// Create Variable of current site
		$variables["currentSite"] = $siteHandle;

		return $this->renderTemplate(
			"websitedocumentation/settings/" .
				($section ? (string) $section : ""),
			$variables
		);
	}

	/**
	 * Save General Settings
	 *
	 * @return Response The rendered result
	 * @throws \yii\web\ForbiddenHttpException
	 */
	public function actionSaveGeneralSettings()
	{
		$this->requirePostRequest();
		$updates = Craft::$app->getRequest()->getBodyParams();
		$plugin = Craft::$app->getPlugins()->getPlugin("websitedocumentation");
		$savedSettings = $plugin->settings->sites;

		unset($savedSettings[$updates['siteHandle']]);

		$currentSettings = [
			($updates['siteHandle']) => [
				'logo' 				=> $updates['logo'],
				'brandBgColor'		=> $updates['brandBgColor'],
				'brandTextColor'	=> $updates['brandTextColor'],
				'accentBgColor'		=> $updates['accentBgColor'],
				'accentTextColor'	=> $updates['accentTextColor'],
				'displayStyleGuide'	=> $updates['displayStyleGuide'],
				'displayCmsGuide'	=> $updates['displayCmsGuide'],
			]
		];

		$settings = [
			'sites' => array_merge($currentSettings, $savedSettings),
		];

		// echo '<pre>';
		// 	var_dump($settings);
		// echo '</pre>';
		// die();

		if (!Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)) {
			Craft::$app
				->getSession()
				->setError(Craft::t("app", "Couldn't save plugin settings."));

			return $this->redirectToPostedUrl();
		}
	}

	/**
	 * Save and Create a New Structure.
	 *
	 * @return Response|null
	 * @throws NotFoundHttpException if the requested plugin cannot be found
	 * @throws \yii\web\BadRequestHttpException
	 * @throws \craft\errors\MissingComponentException
	 * @throws \yii\web\ForbiddenHttpException
	 */
	public function actionSaveStructureSettings(): Response
	{
		$this->requirePostRequest();
		$settings = Craft::$app->getRequest()->getBodyParam("settings");
		$plugin = Craft::$app->getPlugins()->getPlugin("websitedocumentation");
		$guideUrl = $this->redirect("websitedocumentation/settings/cms-guide");

		// Check that we can get the plugin by handle
		if ($plugin === null) {
			throw new NotFoundHttpException("Plugin not found");
		}

		// Check that the field hasn't been left empty
		if (empty($settings["structure"])) {
			Craft::$app
				->getSession()
				->setError(
					Craft::t("app", "Please enter a valid structure name")
				);

			return $guideUrl;
		}

		// Check that the section doesn't already exist
		$sectionRequired = Craft::$app->sections->getSectionByHandle(
			StringHelper::toCamelCase($settings["structure"])
		);

		if ($sectionRequired != null) {
			Craft::$app
				->getSession()
				->setError(
					Craft::t(
						"app",
						"A section with this name already exists. Please add a unique name."
					)
				);

			return $guideUrl;
		}

		// Check that the plugin can save the new setting
		if (
			!Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)
		) {
			Craft::$app
				->getSession()
				->setError(Craft::t("app", "Couldn't save plugin settings."));

			return $guideUrl;
		}

		// Create new Field
		try {
			CreateField::create();
		} catch (Exception $e) {
			Craft::info($e, "WebsiteDocError");
			Craft::$app
				->getSession()
				->setNotice(
					Craft::t(
						"app",
						"There has been a problem creating your field. Please check the logs to see why."
					)
				);
			return $guideUrl;
		}

		// Create new Structure
		try {
			CreateStructure::create($settings["structure"]);
		} catch (Exception $e) {
			Craft::info($e, "WebsiteDocError");
			Craft::$app
				->getSession()
				->setNotice(
					Craft::t(
						"app",
						"There has been a problem creating your structure. Please check the logs to see why."
					)
				);
			return $guideUrl;
		}

		$newStructure = Craft::$app->sections->getSectionByHandle(
			StringHelper::toCamelCase($settings["structure"])
		);

		// Update the Entry type with the new Field
		try {
			UpdateEntryType::update($newStructure);
		} catch (Exception $e) {
			Craft::info($e, "WebsiteDocError");
			Craft::$app
				->getSession()
				->setNotice(
					Craft::t(
						"app",
						"There has been a problem adding the field to the entry type. Please check the logs to see why."
					)
				);
			return $guideUrl;
		}

		Craft::$app
			->getSession()
			->setNotice(
				Craft::t(
					"app",
					"Plugin settings saved. Your structure is now available in entries."
				)
			);

		return $guideUrl;
	}

	public function actionSaveDefaultEntries(): Response
	{
		$settings = WebsiteDocumentation::$settings;
		$structureHandle = StringHelper::toCamelCase($settings["structure"]);
		$newStructure = Craft::$app->sections->getSectionByHandle(
			$structureHandle
		);

		// Create Default Entries
		try {
			CreateEntries::create($newStructure);
		} catch (Exception $e) {
			Craft::info($e, "WebsiteDocError");
			Craft::$app
				->getSession()
				->setNotice(
					Craft::t(
						"app",
						"There has been a problem creating the default entries. Please check the logs to see why."
					)
				);
			return $guideUrl;
		}

		Craft::$app
			->getSession()
			->setNotice(
				Craft::t(
					"app",
					"Default Entries have been added to the " .
						$settings["structure"] .
						" Structure successfully"
				)
			);

		return $this->redirect("websitedocumentation/settings/cms-guide");
	}

	// Protected Methods
    // =========================================================================

    /**
     * Return a siteId from a siteHandle
     *
     * @param string $siteHandle
     *
     * @return int|null
     * @throws NotFoundHttpException
     */
    protected function getSiteIdFromHandle($siteHandle)
    {
        // Get the site to edit
        if ($siteHandle !== null) {
            $site = Craft::$app->getSites()->getSiteByHandle($siteHandle);
            if (!$site) {
                throw new NotFoundHttpException('Invalid site handle: ' . $siteHandle);
            }
            $siteId = $site->id;
        } else {
            $siteId = Craft::$app->getSites()->currentSite->id;
        }

        return $siteId;
    }

	/**
     * @param string $siteHandle
     * @param        $siteId
     * @param        $variables
     *
     * @throws ForbiddenHttpException
     */
    protected function setMultiSiteVariables($siteHandle, &$siteId, array &$variables, $element = null)
    {
        // Enabled sites
        $sites = Craft::$app->getSites();
        if (Craft::$app->getIsMultiSite()) {
            // Set defaults based on the section settings
            $variables['enabledSiteIds'] = [];
            $variables['siteIds'] = [];
			$variables['sites'] = (object) [];

            /** @var Site $site */
            foreach ($sites->getEditableSiteIds() as $editableSiteId) {
                $variables['enabledSiteIds'][] = $editableSiteId;
                $variables['siteIds'][] = $editableSiteId;
            }

			foreach ($sites->getEditableSites() as $editableSite) {
				$handle = $editableSite->handle;
				$variables['sites']->$handle = (object) [];
            }

            // Make sure the $siteId they are trying to edit is in our array of editable sites
            if (!in_array($siteId, $variables['enabledSiteIds'], false)) {
                if (!empty($variables['enabledSiteIds'])) {
                    $siteId = reset($variables['enabledSiteIds']);
                } else {
                    $this->requirePermission('editSite:' . $siteId);
                }
            }
        }

        // Set the currentSiteId and currentSiteHandle
        $variables['currentSiteId'] = empty($siteId) ? Craft::$app->getSites()->currentSite->id : $siteId;
        $variables['currentSiteHandle'] = empty($siteHandle)
            ? Craft::$app->getSites()->currentSite->handle
            : $siteHandle;

        // Page title
        $variables['showSites'] = (
            Craft::$app->getIsMultiSite() &&
            count($variables['enabledSiteIds'])
        );

        if ($variables['showSites']) {
            $variables['sitesMenuLabel'] = Craft::t(
                'site',
                $sites->getSiteById((int)$variables['currentSiteId'])->name
            );
        } else {
            $variables['sitesMenuLabel'] = '';
        }
    }

}
