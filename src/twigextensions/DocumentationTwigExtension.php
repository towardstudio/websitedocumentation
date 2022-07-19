<?php
namespace bluegg\websitedocumentation\twigextensions;

use bluegg\websitedocumentation\WebsiteDocumentation;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class DocumentationTwigExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * Returns the globals to add.
     */
    public function getGlobals(): array
    {
        return [
            'websitedocumentation' => WebsiteDocumentation::$documentationVariable,
        ];
    }
}
