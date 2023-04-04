# Website Documentation plugin for Craft CMS 4.x

Creates two links within admin for Style Guide and CMS Guide

## Requirements

This plugin requires Craft CMS 4.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

```
cd /path/to/project/craft
```

2. Then tell Composer to load the plugin:

```
composer require towardstudio/websitedocumentation
```

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Website Documentation.


## Usage

### Installing Demo Templates

Once the plugin has installed you will see a new menu item called Documentation.

If you go to **Plugin Settings** -> **Install Templates** and click install, this will copy the default guides to your templates folder.

Within this folder you can go to Layouts -> CMS Guide / Styleguide and update the paths to where your CSS and JS live.

### General Settings

Within General Settings you can add a logo, and choose colours which will be displayed across both Style and CMS Guides.

### CMS Guide Settings

Here you can create the structure for your CMS Guide and add some default entries to get you started.


## Adding new sections to the styleguide

If you're using the demo templates, you can add a new section by opening up the **_config.twig** file and adding new items to the Object. Each item can have a Sub menu as well.

The naming of these items are used in kebab case for the file in the sections folder. For example if you call a section **Heroes** you'll name your file **heroes.twig**.

[Toward Disclaimer](https://github.com/towardstudio/toward-open-source-disclaimer)

Brought to you by [Toward](https://toward.studio)
