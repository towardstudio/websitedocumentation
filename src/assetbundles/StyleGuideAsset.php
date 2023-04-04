<?php
namespace towardstudio\websitedocumentation\assetbundles;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class StyleGuideAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@towardstudio/websitedocumentation/resources";

		$this->js = [
            'js/dist/style-guide.min.js',
        ];

		$this->css = [
            'css/dist/style-guide.min.css',
			'css/dist/sidebar.min.css',
        ];

        parent::init();
    }
}
