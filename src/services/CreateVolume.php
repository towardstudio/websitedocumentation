<?php
namespace bluegg\websitedocumentation\services;

use yii\base\Component;

use Craft;
use craft\elements\Asset;
use craft\fs\Local;
use craft\models\Volume;
use craft\helpers\FileHelper;
use yii\web\Response;
use craft\models\AssetIndexingSession;

use Exception;

class CreateVolume extends Component
{
	public static function create()
	{
		// First we need to add a new File System
		$fileService = Craft::$app->getFs();

		// We need to check this doesn't currently exist
		$fileServiceCheck = $fileService->getFilesystemByHandle(
			"websiteGuideImages"
		);

		if (!$fileServiceCheck) {
			// Create the new File Service
			$newFileService = $fileService->createFilesystem([
				"type" => 'craft\fs\Local',
				"name" => "Website Guide Images",
				"handle" => "websiteGuideImages",
				"hasUrls" => true,
				"url" => "/cms-assets/websiteGuideImages",
				"settings" => [
					"path" => "@webroot/cms-assets/websiteGuideImages",
				],
			]);

			if (!$fileService->saveFilesystem($newFileService)) {
				return $this->asModelFailure(
					$newFileService,
					Craft::t("app", "Couldn’t save filesystem."),
					"filesystem"
				);
				Craft::info("This has Failed", "DowleyDev");
			}
		}

		$volumeCheck = Craft::$app
			->getVolumes()
			->getVolumeByHandle("websiteGuideImages");

		if (!$volumeCheck && $fileServiceCheck) {
			$volumesService = Craft::$app->getVolumes();

			$volume = new Volume([
				"id" => null,
				"uid" => null,
				"sortOrder" => null,
				"name" => "Website Guide Images",
				"handle" => "websiteGuideImages",
				"fsHandle" => "websiteGuideImages",
				"transformFsHandle" => "websiteGuideImages",
			]);

			$volumeSuccess = $volumesService->saveVolume($volume);

			if (!$volumeSuccess) {
				$this->setFailFlash(Craft::t("app", "Couldn’t save volume."));

				// Send the volume back to the template
				Craft::$app->getUrlManager()->setRouteParams([
					"volume" => $volume,
				]);
				return null;
			}

			// We need to move our files from the plugin to the new volume
			$filePath = Craft::getAlias(
				"@bluegg/websitedocumentation/resources/images"
			);

			$files = FileHelper::findFiles($filePath);

			$basePath = Craft::getAlias("@webroot") . "/cms-assets";

			if (!file_exists($basePath)) {
				mkdir($basePath, 0775, true);
			}

			$fileServicePath = Craft::getAlias($fileServiceCheck->path);

			if (!file_exists($fileServicePath)) {
				mkdir($fileServicePath, 0775, true);
			}

			$assetIndexerService = Craft::$app->getAssetIndexer();

			foreach ($files as $file) {
				$fileName = str_replace($filePath, "", $file);

				$copy = copy($file, $fileServicePath . "/" . $fileName);

				if (!$copy) {
					Craft::info($file . " cannot be moved", "WebsiteDocError");
					return null;
				}
			}

			$success = $assetIndexerService->startIndexingSession([
				$volume->id,
			]);

			if (!$success) {
				Craft::error("Couldn’t update the asset index", __METHOD__);

				return null;
			}
		}
	}
}
