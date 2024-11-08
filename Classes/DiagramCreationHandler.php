<?php

namespace Sandstorm\MxGraph;

use Neos\ContentRepository\Core\Feature\NodeModification\Dto\PropertyValuesToWrite;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationCommands;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationElements;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationHandlerInterface;

class DiagramCreationHandler implements NodeCreationHandlerInterface
{
    public function handle(NodeCreationCommands $commands, NodeCreationElements $elements): NodeCreationCommands
    {
        return $commands->withInitialPropertyValues(PropertyValuesToWrite::fromArray([
            'diagramIdentifier' => 'Diagram ' . date('Y-m-d H:i'),
        ]));
    }
}
