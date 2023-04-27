<?php

namespace towardstudio\websitedocumentation\migrations;

use Craft;
use towardstudio\websitedocumentation\WebsiteDocumentation;

use craft\db\Migration;

/**
 * m230427_174004_my_migration_name migration.
 */
class m230427_174004_move_settings extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
		$plugin = Craft::$app->getPlugins()->getPlugin("websitedocumentation");
		$savedSettings = $plugin->settings;

		if (!isset($settings->sites)) {
			$sites = Craft::$app->getSites();

			$settings = [];

			foreach ($sites->getEditableSites() as $editableSite) {
				$handle = $editableSite->handle;
				$newSettings = [
					($handle) => [
						'logo' 				=> $savedSettings->logo[0],
						'brandBgColor'		=> $savedSettings->brandBgColor,
						'brandTextColor'	=> $savedSettings->brandTextColor,
						'accentBgColor'		=> $savedSettings->accentBgColor,
						'accentTextColor'	=> $savedSettings->accentTextColor,
						'displayStyleGuide'	=> $savedSettings->displayStyleGuide,
						'displayCmsGuide'	=> $savedSettings->displayCmsGuide,
					]
				];

				$settings = $newSettings;
			}

			if (!Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)) {
				Craft::$app
					->getSession()
					->setError(Craft::t("app", "Couldn't save plugin settings."));
			}

		}
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m230427_174004_move_settings cannot be reverted.\n";
        return false;
    }
}
