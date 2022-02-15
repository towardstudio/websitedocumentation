<?php
namespace bluegg\websitedocumentation\assetbundles;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class DocumentationAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@bluegg/websitedocumentation/resources/";

		$this->depends = [
            CpAsset::class,
        ];

        parent::init();
    }
}
