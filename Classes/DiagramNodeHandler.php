<?php

namespace Sandstorm\MxGraph;

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeInterface;

class DiagramNodeHandler
{
    /**
     * @Flow\Inject
     * @var DiagramIdentifierSearchService
     */
    protected $diagramIdentifierSearchService;

    /**
     * Signals that the property of a node was changed.
     *
     * @Flow\Signal
     * @param NodeInterface $node
     * @param string $propertyName name of the property that has been changed/added
     * @param mixed $oldValue the property value before it was changed or NULL if the property is new
     * @param mixed $newValue the new property value
     * @return void
     */
    public function nodePropertyChanged(NodeInterface $node, $propertyName, $oldValue, $newValue)
    {
        if (!$node->getNodeType()->isOfType('Sandstorm.MxGraph:Diagram')) {
            return;
        }

        if ($propertyName !== 'diagramIdentifier') {
            return;
        }

        if ($oldValue === $newValue) {
            return;
        }

        if (empty($newValue)) {
            return;
        }

        $sourceDiagramNode = $this->diagramIdentifierSearchService->findMostRecentDiagramWithIdentifierExcludingOwn($newValue, $node);
        if ($sourceDiagramNode) {
            $node->setProperty('diagramSource', $sourceDiagramNode->getProperty('diagramSource'));
            $node->setProperty('diagramSvgText', $sourceDiagramNode->getProperty('diagramSvgText'));
            $node->setProperty('image', $sourceDiagramNode->getProperty('image'));
        }
    }
}
