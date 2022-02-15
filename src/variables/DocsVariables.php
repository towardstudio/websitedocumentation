<?php
namespace bluegg\websitedocumentation\variables;

use bluegg\websitedocumentation\WebsiteDocumentation;

class DocsVariables
{
	public function displayStructure()
	{
		return WebsiteDocumentation::$settings;
	}
}
