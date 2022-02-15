<?php
namespace bluegg\websitedocumentation\services;

use yii\base\Component;

use Craft;
use bluegg\websitedocumentation\WebsiteDocumentation;

class ReturnSettings extends Component
{
	public function guideSection()
	{
		$settings = WebsiteDocumentation::$settings;
		$structureName = $settings->structure ? $settings->structure : null;

		return $structureName;
	}

	public function formatContent($content)
	{
		$newContent = preg_replace_callback('/<p .*?class="tab-button">(.*?)<\/p>/',
       		function ($name) {
				$tab = strtolower(preg_replace('/\s+/', '-', $name[1]));
         		return '<button class="tab mb-4 block underline" role="tab" data-tab-inner="guide" data-tab="'. $tab .'" data-hash="'. $tab .'">'. $name[1] .'</button>';
       		},
       		$content);

		return $newContent;

	}
}
