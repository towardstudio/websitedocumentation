<?php
namespace towardstudio\websitedocumentation\data;

class DefaultEntries
{
	public static function entries()
	{
		$defaultEntries = [
			[
				"title" => "Introduction",
			],
			[
				"title" => "Areas in the Control Panel",
				"children" => [
					"Dashboard",
					"Entries",
					"Assets",
					"Users",
				],
			],
			[
				"title" => "Adding/Editing Entries",
			],
			[
				"title" => "Field Types",
				"children" => [
					"Assets Field",
					"Entry Picker Field",
					"Matrix Field",
				],
			],
			[
				"title" => "Entry Sections",
			],
            [
				"title" => "Plugins",
				"children" => [
					"Navigation",
					"Retour",
					"SEOMatic",
				],
			],
		];

		return $defaultEntries;
	}
}
