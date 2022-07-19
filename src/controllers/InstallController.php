<?php
namespace bluegg\websitedocumentation\controllers;

use bluegg\websitedocumentation\WebsiteDocumentation;

use Craft;
use craft\web\Controller;

class InstallController extends Controller
{

	/**
	 * Install Base Templates
	 *
	 * @return Response The rendered result
	 */
	public function actionInstallTemplates()
	{
		$this->requirePostRequest();

		// Get Craft Template folder path
		$templatesFolder = Craft::getAlias('@templates') . '/website-docs';

		// Get Base Templates Path
		$websiteDocs = Craft::getAlias('@websitedocumentation') . '/templates/website-docs';

		// Create new Structure
		try {
			$this->copyDirectory($websiteDocs, $templatesFolder);
		} catch (\Exception $e) {
			Craft::$app
				->getSession()
				->setNotice($e->getMessage());

			return $this->redirect("websitedocumentation/settings/templates");
		}

		Craft::$app
		->getSession()
		->setNotice(
			Craft::t(
				"app",
				"Base Templates have been created successfully"
			)
		);

		return $this->redirect("websitedocumentation/settings/templates");

		// copy($source, $dest)
	}

	public function copyDirectory($directory, $destination)
	{

		# The directory will be created if it doesn't already exist
		if (!file_exists($destination)) {
			if (!mkdir($destination)) {
				return false;
			}
		} else {
			throw new \Exception('Folder already exists');
		}

		$directoryList = @scandir($directory);

		# Directory scanning
		if (!$directoryList) {
			return false;
		}

		foreach ($directoryList as $itemName) {
			$item = $directory . DIRECTORY_SEPARATOR . $itemName;

			if ($itemName == '.' || $itemName == '..') {
				continue;
			}

			if (filetype($item) == 'dir') {
				$this->copyDirectory($item, $destination . DIRECTORY_SEPARATOR . '/' . $itemName);
			} else {
				if (!copy($item, $destination . DIRECTORY_SEPARATOR . $itemName)) {
					return false;
				}
			}
		}
		return true;
	}

}
