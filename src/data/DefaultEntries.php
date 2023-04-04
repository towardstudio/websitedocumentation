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
					"Globals",
					"Assets",
					"Users",
				],
			],
			[
				"title" => "Adding/Editing Entries",
			],
			[
				"title" => "Navigation",
			],
			[
				"title" => "Retour",
			],
			[
				"title" => "SEOMatic",
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
		];

		return $defaultEntries;
	}
}
