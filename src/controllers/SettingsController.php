<?php
namespace towardstudio\websitedocumentation\controllers;

use towardstudio\websitedocumentation\WebsiteDocumentation;
use towardstudio\websitedocumentation\models\Settings;
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
	public function actionPluginSettings($settings = null): Response
	{
		if ($settings === null) {
			$settings = WebsiteDocumentation::$settings;
		}

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

		// Logo
		$logo = null;
		if ($settings->logo) {
			foreach ($settings->logo as $item) {
				$logo = Craft::$app->elements->getElementById($item);
			}
		}
		$variables["logo"] = $logo;

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

		if (!Craft::$app->getPlugins()->savePluginSettings($plugin, $updates)) {
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
}
