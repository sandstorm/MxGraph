<?php

namespace Sandstorm\MxGraph;


use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Core\Feature\Security\Exception\AccessDenied;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindClosestNodeFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindDescendantNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\NodeType\NodeTypeCriteria;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueContains;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\VisibilityConstraints;
use Neos\ContentRepository\Core\SharedModel\Node\PropertyName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Neos\Domain\Service\NodeTypeNameFactory;

/**
 * @Flow\Scope("singleton")
 */
class DiagramIdentifierSearchService
{
    /**
     * @Flow\Inject
     * @var ContentRepositoryRegistry
     */
    protected $contentRepositoryRegistry;

    /**
     * @param string $searchTerm
     * @param Node $node
     * @return string[]
     */
    public function findInIdentifier(string $searchTerm, Node $node): array
    {
        $results = [];

        $contentRepository = $this->contentRepositoryRegistry->get($node->contentRepositoryId);
        try {
            $subgraph = $contentRepository->getContentSubgraph(
                $node->workspaceName,
                $node->dimensionSpacePoint
            );
        } catch (AccessDenied) {
            return [];
        }
        $siteNode = $subgraph->findClosestNode(
            $node->aggregateId,
            FindClosestNodeFilter::create(nodeTypes: NodeTypeNameFactory::NAME_SITE)
        );

        $propertyConstraint = PropertyValueContains::create(PropertyName::fromString('diagramIdentifier'), $searchTerm, false);
        $possibleResults = $subgraph->findDescendantNodes(
            $siteNode->aggregateId,
            FindDescendantNodesFilter::create(
                nodeTypes: 'Sandstorm.MxGraph:Diagram',
                propertyValue: $propertyConstraint
            ),
        );

        foreach ($possibleResults as $possibleResult) {
            assert($possibleResult instanceof Node);
            $possibleDiagramIdentifier = $possibleResult->getProperty('diagramIdentifier');
            if (!isset($results[$possibleDiagramIdentifier])) {
                $results[$possibleDiagramIdentifier] = $possibleDiagramIdentifier;
            }
        }

        return array_values($results);
    }

    /**
     * @param string $diagramIdentifier
     * @return Node[]
     */
    public function findRelatedDiagramsWithIdentifierExcludingOwn(string $diagramIdentifier, Node $contextNode): array
    {
        $results = [];

        $contentRepository = $this->contentRepositoryRegistry->get($contextNode->contentRepositoryId);
        $subgraph = $contentRepository->getContentGraph($contextNode->workspaceName)->getSubgraph(
            $contextNode->dimensionSpacePoint,
            VisibilityConstraints::default()
        );
        $siteNode = $subgraph->findClosestNode(
            $contextNode->aggregateId,
            FindClosestNodeFilter::create(nodeTypes: NodeTypeNameFactory::NAME_SITE)
        );

        $propertyConstraint = PropertyValueContains::create(PropertyName::fromString('diagramIdentifier'), $diagramIdentifier, false);
        $possibleResults = $subgraph->findDescendantNodes(
            $siteNode->aggregateId,
            FindDescendantNodesFilter::create(
                nodeTypes: NodeTypeCriteria::fromFilterString('Sandstorm.MxGraph:Diagram'),
                propertyValue: $propertyConstraint
            ),
        );

        foreach ($possibleResults as $node) {
            assert($node instanceof Node);
            if ($contextNode->equals($node)) {
                // we skip ourselves
                continue;
            }

            // we still need to check for exact DiagramIdentifier, because nodeSearchService finds across **ALL** properties
            // and with wildcards.
            if ($node->getProperty('diagramIdentifier') === $diagramIdentifier) {
                $results[] = $node;
            }
        }

        return $results;
    }

    /**
     * @param string $diagramIdentifier
     * @param Node $contextNode
     * @return Node|null
     */
    public function findMostRecentDiagramWithIdentifierExcludingOwn(string $diagramIdentifier, Node $contextNode): ?Node
    {
        $relatedDiagramNodes = $this->findRelatedDiagramsWithIdentifierExcludingOwn($diagramIdentifier, $contextNode);

        uasort($relatedDiagramNodes, function (Node $nodeA, Node $nodeB) {
            $timestampA = $nodeA->timestamps->lastModified ?? $nodeA->timestamps->created;
            $timestampB = $nodeB->timestamps->lastModified ?? $nodeA->timestamps->created;

            return $timestampB <=> $timestampA;
        });

        if (count($relatedDiagramNodes) > 0) {
            // the 1st element is the one with the latest modification time.
            return reset($relatedDiagramNodes);
        }
        return null;
    }
}
