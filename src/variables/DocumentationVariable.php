<?php
namespace towardstudio\websitedocumentation\variables;

use towardstudio\websitedocumentation\WebsiteDocumentation;

use Craft;
use craft\helpers\UrlHelper;
use yii\di\ServiceLocator;

class DocumentationVariable extends ServiceLocator
{
	public function getUrl(string $siteHandle = null)
	{
		// Get the Config File
		$config = WebsiteDocumentation::customConfig();

		if (empty($config))
		{
			return 'website-docs';
		}

		if (isset($config['documentationUrl']) || isset($config['url'])) {
			$docUrl = isset($config['documentationUrl']) ? $config['documentationUrl'] : $config['url'];
		} elseif(isset($config[$siteHandle]['documentationUrl']) || isset($config[$siteHandle]['url'])) {
			$docUrl = isset($config[$siteHandle]['documentationUrl']) ? $config[$siteHandle]['documentationUrl'] : $config[$siteHandle]['url'];
		} else {
			$docUrl = 'website-docs';
		}

		return $docUrl;

	}

	public function getSettings(string $siteHandle = null)
	{
		if ($siteHandle) {
			return WebsiteDocumentation::$settings->sites[$siteHandle];
		} else {
			return WebsiteDocumentation::$settings;
		}
	}

    /**
    * Returns 'dark' or 'light' based on contrast score
    *
    * @param  string $hexcolor
    * @return string 'dark' or 'light'
    */
    public function getContrastColor($hexcolor)
    {
        $r = hexdec(substr($hexcolor, 1, 2));
        $g = hexdec(substr($hexcolor, 3, 2));
        $b = hexdec(substr($hexcolor, 5, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        return ($yiq >= 160) ? '#000' : '#fff';
    }

}
