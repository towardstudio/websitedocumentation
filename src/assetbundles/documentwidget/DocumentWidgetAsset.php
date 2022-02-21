<?php
namespace bluegg\websitedocumentation\assetbundles\documentwidget;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class DocumentWidgetAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath =
            "@bluegg/websitedocumentation/assetbundles/documentwidget/dist";

        // define the dependencies
        $this->depends = [CpAsset::class];

        $this->css = ["css/DocumentWidget.css"];

        parent::init();
    }
}
