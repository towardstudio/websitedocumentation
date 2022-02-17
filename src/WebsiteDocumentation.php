<?php
namespace bluegg\websitedocumentation;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\RegisterUrlRulesEvent;
use craft\events\PluginEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\UrlHelper;
use craft\web\UrlManager;
use craft\web\View;
use craft\web\twig\variables\CraftVariable;

use craft\services\Elements;
use craft\services\Dashboard;
use craft\events\ElementEvent;
use craft\elements\Entry;

use bluegg\websitedocumentation\models\Settings;
use bluegg\websitedocumentation\assetbundles\DocumentationAsset;
use bluegg\websitedocumentation\services\ReturnSettings;
use bluegg\websitedocumentation\widgets\DocumentationWidget;

use yii\base\Event;

/**
 * @author    bluegg
 * @package   WebsiteDocumentation
 * @since     1.0.0
 *
 */
class WebsiteDocumentation extends Plugin
{
    public static $plugin;

    public $hasCpSection = true;
    public $hasCpSettings = true;
    public static $settings;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        self::$plugin = $this;
        self::$settings = $this->getSettings();

        // Handler: UrlManager::EVENT_REGISTER_CP_URL_RULES
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                Craft::debug(
                    "UrlManager::EVENT_REGISTER_CP_URL_RULES",
                    __METHOD__
                );
                // Register our Control Panel routes
                $event->rules = array_merge(
                    $event->rules,
                    $this->customAdminCpRoutes()
                );
            }
        );

        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $e) {
                if (
                    is_dir(
                        $baseDir =
                            $this->getBasePath() .
                            DIRECTORY_SEPARATOR .
                            "templates"
                    )
                ) {
                    $e->roots[$this->id] = $baseDir;
                }
            }
        );

        // Register our widgets
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = DocumentationWidget::class;
            }
        );

        # prettier-ignore
        Event::on(
			CraftVariable::class,
			CraftVariable::EVENT_INIT,
			function (Event $e) {
				/** @var CraftVariable $variable */
				$variable = $e->sender;

				// Attach a service:
				$variable->set(
					"documentSettings", ReturnSettings::class
				);
			}
		);

        Craft::info(
            Craft::t("websitedocumentation", "{name} plugin loaded", [
                "name" => $this->name,
            ]),
            __METHOD__
        );
    }

    // Rename the Control Panel Item & Add Sub Menu
    public function getCpNavItem()
    {
        // Get the site Url
        $url = Craft::$app->sites->currentSite->baseUrl;

        // Get the documentation url
        $docUrl = getenv("DOCS_URL") ? getenv("DOCS_URL") : "website-docs";

        // Set additional information on the nav item
        $item = parent::getCpNavItem();

        $item["label"] = "Documentation";
        $item["icon"] = "@app/icons/book.svg";

        // Create SubNav
        $subNavs = [];

        // Add Dashboard Sub Nav Item
        $subNavs["dashboard"] = [
            "label" => "Dashboard",
            "url" => "websitedocumentation/dashboard",
        ];

        // Add Styleguide External Sub Nav Item
        $subNavs["styleguide"] = [
            "label" => "Styleguide",
            "url" => $url . $docUrl . "/styleguide",
            "external" => true,
        ];

        // Add Guide External Sub Nav Item
        $subNavs["guide"] = [
            "label" => "CMS Guide",
            "url" => $url . $docUrl . "/guide",
            "external" => true,
        ];

        // Add Settings on dev environment
        $editableSettings = true;
        $general = Craft::$app->getConfig()->getGeneral();
        if (!$general->allowAdminChanges) {
            $editableSettings = false;
        }
        if ($editableSettings) {
            $subNavs["settings"] = [
                "label" => "Settings",
                "url" => "websitedocumentation/settings",
            ];
        }

        $item = array_merge($item, [
            "subnav" => $subNavs,
        ]);

        return $item;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    public function getSettingsResponse()
    {
        // Just redirect to the plugin settings page
        Craft::$app
            ->getResponse()
            ->redirect(UrlHelper::cpUrl("websitedocumentation/settings"));
    }

    // Private Methods
    // =========================================================================

    /**
     * Return the custom Control Panel routes
     *
     * @return array
     */
    protected function customAdminCpRoutes(): array
    {
        return [
            "websitedocumentation" => [
                "template" => "websitedocumentation/dashboard",
            ],
            "websitedocumentation/settings" =>
                "websitedocumentation/settings/plugin-settings",
        ];
    }
}
