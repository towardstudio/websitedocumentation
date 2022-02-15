<?php

namespace bluegg\websitedocumentation;
use bluegg\websitedocumentation\WebsiteDocumentation;

class DocsVariables
{
	public function displayStructure()
	{
		return WebsiteDocumentation::$settings;
	}
}
