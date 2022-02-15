<?php
namespace bluegg\websitedocumentation\services;

use yii\base\Component;

use Craft;
use craft\volumes\Local;
use craft\helpers\FileHelper;

use Exception;

class CreateVolume extends Component
{
	public static function create()
	{
		$volumeCheck = Craft::$app
			->getVolumes()
			->getVolumeByHandle("websiteGuideImages");

		if (!$volumeCheck) {
			$volume = new Local([
				"name" => "Website Guide Images",
				"handle" => "websiteGuideImages",
				"hasUrls" => true,
				"url" => "/cms-assets/websiteGuideImages",
				"path" => "@webroot/cms-assets/websiteGuideImages",
			]);

			if (!Craft::$app->volumes->saveVolume($volume)) {
				throw new Exception("Couldn’t save volume.");
			}

			// We need to move our files from the plugin to the new volume
			$filePath = Craft::getAlias(
				"@bluegg/websitedocumentation/resources/"
			);

			$files = FileHelper::findFiles($filePath);

			$volumePath = Craft::getAlias($volume->path);

			foreach ($files as $file) {
				$fileName = str_replace($filePath, "", $file);

				$copy = copy($file, $volumePath . "/" . $fileName);

				if (!$copy) {
					Craft::info($file . " cannot be moved", "WebsiteDocError");

					return null;
				}

				$assetIndexerService = Craft::$app->getAssetIndexer();
				$sessionId = $assetIndexerService->getIndexingSessionId();

				$success = $assetIndexerService->indexFile(
					$volume,
					$fileName,
					$sessionId
				);

				if (!$success) {
					Craft::error("Couldn’t update the asset index", __METHOD__);
				}
			}
		}
	}
}
