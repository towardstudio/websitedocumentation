# Website Documentation Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 4.1.01 - 2024-08-12

-   Fix issues with Multi Site

## 4.0.1 - 2024-04-29

-   Bug Fixes

## 4.0.0 - 2024-04-29

-   Craft 5 Upgrade

## 3.1.3 - 2023-11-01

-   Change Toolbar from Full Screen to Toggle

## 3.1.2 - 2023-10-18

-   Fix Bug when using Caps in config

## 3.1.1 - 2023-10-18

-   Fixed Vulnerability in PostCSS

## 3.1.0 - 2023-10-10

-   Updated Colour Settings on the Style Guide

## 3.0.0 - 2022-04-27

> {warning} This is a big update to fix Multi-Site Issues. You will need to re-do the general settings for each site. You will also need to update all instances of `websitedocumentation.getUrl()` to `websitedocumentation.getUrl(currentSite.handle)` within your website-docs folder. You will also need to update /website-docs/\_layouts/\_global/ so that `websitedocumentation.getUrl()` contains the site handle: ` websitedocumentation.getUrl(currentSite.handle)`

-   Updated Settings to work per-site
-   Added Multi-Site Support

## 2.6.1 - 2022-04-04

-   Update Default CMS Guide Entries

## 2.6.0 - 2022-04-04

> {warning} This is a rename of the repo. You will need to change the bluegg handles in website-docs > \_layouts > \_global.twig if you are using the default folder.

-   Change Bluegg to Toward
-   Change CMS Guide default entries to remove globals & categories

## 2.0.2 - 2022-08-19

-   Remove Max Levels for the Structure

## 2.0.1 - 2022-07-19

-   New Settings UI
-   Updated Design for Styleguide and CMS Guide
-   Download Templates

## 2.0.0-beta - 2022-03-10

-   Initial Code update to work for Craft 4.

## 1.3.2 - 2022-02-18

-   Make sure plugin still runs if general config doesn't exist

## 1.3.1 - 2022-02-17

-   Hide Settings based on general config

## 1.3.0 - 2022-02-16

-   Added Widget for the Dashboard

## 1.2.0 - 2022-02-15

-   Adding CMS Section to Create Structure & Default Entries

## 1.0.0 - 2022-02-10

### Added

-   Initial release
