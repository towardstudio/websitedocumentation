<?php
namespace bluegg\websitedocumentation\assetbundles;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ComponentAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@bluegg/websitedocumentation/resources";

		$this->css = [
			'css/dist/components.min.css',
        ];

        parent::init();
    }
}
