<?php

$vendorDir = dirname(__DIR__);
$rootDir = dirname(dirname(__DIR__));

return array (
  'bluegg/website-documentation' => 
  array (
    'class' => 'bluegg\\documentation\\Plugin',
    'basePath' => $rootDir . '/src',
    'handle' => 'documentation',
    'aliases' => 
    array (
      '@bluegg/documentation' => $rootDir . '/src',
    ),
    'name' => 'Website Documentation',
    'version' => '1.0.0',
    'description' => 'Creates two links within admin for Styleguide and CMS Guide',
    'developer' => 'Bluegg',
    'developerUrl' => 'https://bluegg.co.uk',
  ),
);
