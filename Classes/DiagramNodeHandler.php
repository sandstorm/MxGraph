<?php

namespace Sandstorm\MxGraph;

use Neos\ContentRepository\Core\Feature\NodeModification\Command\SetNodeProperties;
use Neos\ContentRepository\Core\Feature\NodeModification\Dto\PropertyValuesToWrite;
use Neos\ContentRepository\Core\NodeType\NodeTypeName;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;

class DiagramNodeHandler
{
    /**
     * @Flow\Inject
     * @var DiagramIdentifierSearchService
     */
    protected $diagramIdentifierSearchService;

    /**
     * @Flow\Inject
     * @var ContentRepositoryRegistry
     */
    protected $contentRepositoryRegistry;

    /**
     * Signals that the property of a node was changed.
     *
     * @Flow\Signal
     * @param Node $node
     * @param string $propertyName name of the property that has been changed/added
     * @param mixed $oldValue the property value before it was changed or NULL if the property is new
     * @param mixed $newValue the new property value
     * @return void
     */
    public function nodePropertyChanged(Node $node, $propertyName, $oldValue, $newValue)
    {
        if (!$node->nodeTypeName->equals(NodeTypeName::fromString('Sandstorm.MxGraph:Diagram'))) {
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
            $contentRepository = $this->contentRepositoryRegistry->get($node->contentRepositoryId);
            $contentRepository->handle(SetNodeProperties::create(
                $node->workspaceName,
                $node->aggregateId,
                $node->originDimensionSpacePoint,
                PropertyValuesToWrite::fromArray([
                    'diagramSource' => $sourceDiagramNode->getProperty('diagramSource'),
                    'diagramSvgText' => $sourceDiagramNode->getProperty('diagramSvgText'),
                    'image' => $sourceDiagramNode->getProperty('image'),
                ]),
            ));
        }
    }
}
