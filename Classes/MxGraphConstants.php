<?php

namespace Sandstorm\MxGraph;

use Neos\ContentRepository\Core\NodeType\NodeTypeName;
use Neos\ContentRepository\Core\SharedModel\Node\PropertyName;

final readonly class MxGraphConstants
{
    public static function getNodeTypeName(): NodeTypeName
    {
        return NodeTypeName::fromString('Sandstorm.MxGraph:Diagram');
    }

    public static function getDiagramIdentifierPropertyName(): PropertyName
    {
        return PropertyName::fromString('diagramIdentifier');
    }

    public static function getDiagramSourcePropertyName(): PropertyName
    {
        return PropertyName::fromString('diagramSource');
    }

    public static function getDiagramSvgTextPropertyName(): PropertyName
    {
        return PropertyName::fromString('diagramSvgText');
    }
}
