<?php

namespace Sandstorm\MxGraph;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Neos\Ui\NodeCreationHandler\NodeCreationHandlerInterface;
use Sandstorm\MxGraph\Domain\Model\Diagram;

class DiagramCreationHandler implements NodeCreationHandlerInterface
{
    public function handle(NodeInterface $node, array $data)
    {
        $node->setProperty('diagramIdentifier', 'Diagram ' . date('Y-m-d H:i'));
    }
}
