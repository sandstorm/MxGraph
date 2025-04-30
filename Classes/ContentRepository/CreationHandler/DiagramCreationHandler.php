<?php

namespace Sandstorm\MxGraph\ContentRepository\CreationHandler;

use Neos\ContentRepository\Core\Feature\NodeModification\Dto\PropertyValuesToWrite;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationCommands;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationElements;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationHandlerInterface;
use Sandstorm\MxGraph\MxGraphConstants;

class DiagramCreationHandler implements NodeCreationHandlerInterface
{
    public function handle(NodeCreationCommands $commands, NodeCreationElements $elements): NodeCreationCommands
    {
        return $commands->withInitialPropertyValues(PropertyValuesToWrite::fromArray([
            MxGraphConstants::getDiagramIdentifierPropertyName()->value => 'Diagram ' . date('Y-m-d H:i')
        ]));
    }
}
