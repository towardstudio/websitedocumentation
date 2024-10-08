<?php
namespace towardstudio\websitedocumentation;

use Craft;
use craft\base\Plugin;
use craft\base\Model;
use craft\services\Plugins;
use craft\events\RegisterUrlRulesEvent;
use craft\events\PluginEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\App;
use craft\helpers\UrlHelper;
use craft\web\Request;
use craft\web\UrlManager;
use craft\web\View;
use craft\web\twig\variables\CraftVariable;

use craft\services\Elements;
use craft\services\Dashboard;
use craft\events\ElementEvent;
use craft\elements\Entry;

use towardstudio\websitedocumentation\models\Settings;
use towardstudio\websitedocumentation\assetbundles\DocumentationAsset;
use towardstudio\websitedocumentation\services\ReturnSettings;
use towardstudio\websitedocumentation\twigextensions\DocumentationTwigExtension;
use towardstudio\websitedocumentation\widgets\DocumentationWidget;
use towardstudio\websitedocumentation\variables\DocumentationVariable;

use yii\base\Event;

/**
 * @author    towardstudio
 * @package   WebsiteDocumentation
 * @since     1.0.0
 *
 */
class WebsiteDocumentation extends Plugin
{
	public static ?WebsiteDocumentation $plugin;
    public static ?DocumentationVariable $documentationVariable;

	public bool $hasCpSection = true;
	public bool $hasCpSettings = true;
	public static ?Settings $settings;

	// Public Methods
	// =========================================================================

	public function init()
	{
		parent::init();
		self::$plugin = $this;
		self::$settings = $this->getSettings();
		self::$documentationVariable = new DocumentationVariable();

		// Create Custom Alias
		Craft::setAlias('@websitedocumentation', __DIR__);

		// Run custom functions
		$this->initRoutes();
		$this->_registerTwigExtensions();

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

		$request = Craft::$app->getRequest();

		if (
			!$request->getIsConsoleRequest() &&
			$request->getSegment(-1) === "guide"
		) {
			Craft::$app
				->getView()
				->registerAssetBundle(DocumentationAsset::class);
		}

		Craft::info(
			Craft::t("websitedocumentation", "{name} plugin loaded", [
				"name" => $this->name,
			]),
			__METHOD__
		);
	}

	// Rename the Control Panel Item & Add Sub Menu
	public function getCpNavItem(): ?array
	{
		// Get the site info
		$handle = Craft::$app->sites->currentSite->handle;
		$url = Craft::$app->sites->currentSite->baseUrl;

		// Get the documentation url
		$config = WebsiteDocumentation::customConfig();
		$docUrl = $this->getDocUrl($config, $handle);

		// Set additional information on the nav item
		$item = parent::getCpNavItem();

		$item["label"] = "Documentation";
		$item["icon"] = "@app/icons/book.svg";

		// Create SubNav
		$subNavs = [];

		// Get Documentation Settings
		$settings = $this->getSettings();

		// Check for Multi-Site
		$request = Craft::$app->getRequest();
        $siteHandle = '';
        if ($request->getSegment(1) === 'websitedocumentation') {
            $segments = $request->getSegments();
            $lastSegment = end($segments);
            $site = Craft::$app->getSites()->getSiteByHandle($lastSegment);
            if ($site !== null) {
                $siteHandle = '/' . $lastSegment;
            }
        }

		// Add Dashboard Sub Nav Item
		$subNavs["dashboard"] = [
			"label" => "Dashboard",
			"url" => "websitedocumentation/dashboard" . $siteHandle,
		];

 		// Add Styleguide External Sub Nav Item
		$subNavs["styleguide"] = [
			"label" => "Style Guide",
			"url" => $url . $docUrl . "/style-guide",
			"external" => true,
		];

		// Add Guide External Sub Nav Item
		$subNavs["guide"] = [
			"label" => "CMS Guide",
			"url" => $url . $docUrl . "/cms-guide",
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
				"label" => "Plugin Settings",
				"url" => "websitedocumentation/settings" . $siteHandle,
			];
		}

		$item = array_merge($item, [
			"subnav" => $subNavs,
		]);

		return $item;
	}

	public static function customConfig()
	{
		$config = Craft::$app->config->getConfigFromFile(self::$plugin->id);

		if ($config) {
			return $config;
		}

	}

	// Rename the Control Panel Item & Add Sub Menu
	public static function getDocUrl($config, $handle): ?string
	{
		if (empty($config))
		{
			$docUrl = 'website-docs';
		} else {
			if (isset($config['documentationUrl']) || isset($config['url'])) {
				$docUrl = isset($config['documentationUrl']) ? $config['documentationUrl'] : $config['url'] ;
			}  elseif(isset($config[$handle]['documentationUrl']) || isset($config[$handle]['url'])) {
				$docUrl = isset($config[$handle]['documentationUrl']) ? $config[$handle]['documentationUrl'] : $config[$handle]['url'];
			} else {
				$docUrl = 'website-docs';
			}
		}

		return $docUrl;

	}

	// Protected Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	protected function createSettingsModel(): ?Model
	{
		return new Settings();
	}

	protected function settingsHtml(): string
	{
		return Craft::$app
			->getView()
			->renderTemplate("websitedocumentation/settings", [
				"settings" => $this->getSettings(),
			]);
	}

	protected function customAdminCpRoutes(): array
	{
		return [
			"websitedocumentation" => [
				"template" => "websitedocumentation/dashboard",
			],
			"websitedocumentation/settings" =>
				"websitedocumentation/settings/plugin-settings",
			'websitedocumentation/settings/<subSection:{handle}>/<siteHandle:{handle}>' =>
                "websitedocumentation/settings/plugin-settings",
		];
	}

	// Private Methods
	// =========================================================================


	private function initRoutes()
	{
		Event::on(
			UrlManager::class,
			UrlManager::EVENT_REGISTER_CP_URL_RULES,
			function (RegisterUrlRulesEvent $event) {
				$routes = include __DIR__ . "/routes.php";
				$event->rules = array_merge($event->rules, $routes);
			}
		);
	}

	/**
     * Registers Twig extensions.
     */
    private function _registerTwigExtensions()
    {
        Craft::$app->view->registerTwigExtension(new DocumentationTwigExtension());
    }

}
