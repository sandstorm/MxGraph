<?php

namespace Sandstorm\MxGraph\ContentRepository;

use Neos\ContentRepository\Core\Feature\NodeModification\Command\SetNodeProperties;
use Neos\ContentRepository\Core\Feature\NodeModification\Dto\PropertyValuesToWrite;
use Neos\ContentRepository\Core\Feature\Security\Exception\AccessDenied;
use Neos\ContentRepository\Core\NodeType\NodeTypeNames;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\CountDescendantNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindClosestNodeFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindDescendantNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\NodeType\NodeTypeCriteria;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\Ordering\OrderingDirection;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\Ordering\OrderingField;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\Ordering\TimestampField;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueContains;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueEquals;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\Domain\Service\NodeTypeNameFactory;
use Psr\Log\LoggerInterface;
use Sandstorm\MxGraph\MxGraphConstants;

#[Flow\Scope("singleton")]
class DiagramContentRepositoryService
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    #[Flow\Inject]
    protected LoggerInterface $logger;


    /**
     * @throws AccessDenied
     */
    public function searchByDiagramIdentifier(string $searchTerm, Node $contextNode): Nodes
    {
        $contentRepository = $this->contentRepositoryRegistry->get($contextNode->contentRepositoryId);
        $subgraph = $contentRepository->getContentSubgraph(
            workspaceName: $contextNode->workspaceName,
            dimensionSpacePoint: $contextNode->dimensionSpacePoint
        );
        $siteNode = $subgraph->findClosestNode(
            entryNodeAggregateId: $contextNode->aggregateId,
            filter: FindClosestNodeFilter::create(nodeTypes: NodeTypeNameFactory::NAME_SITE)
        );

        return $subgraph->findDescendantNodes(
            $siteNode->aggregateId,
            FindDescendantNodesFilter::create(
                nodeTypes: MxGraphConstants::getNodeTypeName(),
                propertyValue: PropertyValueContains::create(
                    propertyName: MxGraphConstants::getDiagramIdentifierPropertyName(),
                    value: $searchTerm,
                    caseSensitive: false,
                ),
            ),
        );
    }

    /**
     * @throws AccessDenied
     */
    public function countNodesWithDiagramIdentifier(string $diagramIdentifier, Node $contextNode): int
    {
        $contentRepository = $this->contentRepositoryRegistry->get($contextNode->contentRepositoryId);
        $subgraph = $contentRepository->getContentSubgraph(
            workspaceName: $contextNode->workspaceName,
            dimensionSpacePoint: $contextNode->dimensionSpacePoint
        );
        $siteNode = $subgraph->findClosestNode(
            entryNodeAggregateId: $contextNode->aggregateId,
            filter: FindClosestNodeFilter::create(nodeTypes: NodeTypeNameFactory::NAME_SITE)
        );

        return $subgraph->countDescendantNodes(
            $siteNode->aggregateId,
            CountDescendantNodesFilter::create(
                nodeTypes: MxGraphConstants::getNodeTypeName(),
                propertyValue: PropertyValueContains::create(
                    propertyName: MxGraphConstants::getDiagramIdentifierPropertyName(),
                    value: $diagramIdentifier,
                    caseSensitive: true,
                ),
            ),
        );
    }

    /**
     * @throws AccessDenied
     */
    public function applyDataToDiagramNodeAndRelatedNodes(Node $sourceNode, string $xml, string $svg): void
    {
        $contentRepository = $this->contentRepositoryRegistry->get($sourceNode->contentRepositoryId);
        $subgraph = $contentRepository->getContentSubgraph(
            workspaceName: $sourceNode->workspaceName,
            dimensionSpacePoint: $sourceNode->dimensionSpacePoint
        );
        $siteNode = $subgraph->findClosestNode(
            entryNodeAggregateId: $sourceNode->aggregateId,
            filter: FindClosestNodeFilter::create(nodeTypes: NodeTypeNameFactory::NAME_SITE)
        );

        $diagramIdentifierPropertyName = MxGraphConstants::getDiagramIdentifierPropertyName();
        $diagramSourcePropertyName = MxGraphConstants::getDiagramSourcePropertyName();
        $diagramSvgTextPropertyName = MxGraphConstants::getDiagramSvgTextPropertyName();

        $diagramIdentifier = $sourceNode->getProperty($diagramIdentifierPropertyName);

        $nodesWithDiagramIdentifier = $subgraph->findDescendantNodes(
            entryNodeAggregateId: $siteNode->aggregateId,
            filter: FindDescendantNodesFilter::create(
                nodeTypes: NodeTypeCriteria::createWithAllowedNodeTypeNames(NodeTypeNames::with(MxGraphConstants::getNodeTypeName())),
                propertyValue: PropertyValueEquals::create(
                    propertyName: $diagramIdentifierPropertyName,
                    value: $diagramIdentifier,
                    caseSensitive: true,
                ),
            ),
        );

        $propertiesToWrite = PropertyValuesToWrite::fromArray([
            $diagramSourcePropertyName->value => $xml,
            $diagramSvgTextPropertyName->value => $svg,
        ]);

        foreach ($nodesWithDiagramIdentifier as $diagramNode) {
            $diagramId = $diagramNode->getProperty($diagramIdentifierPropertyName);

            $this->logger->debug("DiagramContentRepositoryService::applyDataToDiagramNodeAndToRelatedNodes -> trigger data update for diagram node: '$diagramNode->aggregateId' with diagram identifier '$diagramId'");

            $contentRepository->handle(SetNodeProperties::create(
                workspaceName: $diagramNode->workspaceName,
                nodeAggregateId: $diagramNode->aggregateId,
                originDimensionSpacePoint: $diagramNode->originDimensionSpacePoint,
                propertyValues: $propertiesToWrite,
            ));
        }
    }

    /**
     * @throws AccessDenied
     */
    public function applyLastestDiagramDataOfDiagramIdentifierToNode(string $diagramIdentifier, Node $node): void
    {
        $this->logger->debug("DiagramCommandHook::handleDiagramIdentifierChange: Diagram node '$node->aggregateId' changed property nodeIdentifier to '$diagramIdentifier'");

        $diagramNodeWithLatestChange = $this->getLatestDiagramNodeByDiagramIdentifier($diagramIdentifier, $node);

        if ($diagramNodeWithLatestChange === null) {
            return;
        }

        $this->logger->debug("DiagramCommandHook::handleDiagramIdentifierChange: Found latest Diagram node '$diagramNodeWithLatestChange->aggregateId' and applying it's data to node '$node->aggregateId'");

        $diagramSourcePropertyName = MxGraphConstants::getDiagramSourcePropertyName();
        $diagramSvgTextPropertyName = MxGraphConstants::getDiagramSvgTextPropertyName();

        $contentRepository = $this->contentRepositoryRegistry->get($node->contentRepositoryId);

        $contentRepository->handle(SetNodeProperties::create(
            workspaceName: $node->workspaceName,
            nodeAggregateId: $node->aggregateId,
            originDimensionSpacePoint: $node->originDimensionSpacePoint,
            propertyValues: PropertyValuesToWrite::fromArray([
                $diagramSourcePropertyName->value => $diagramNodeWithLatestChange->getProperty($diagramSourcePropertyName),
                $diagramSvgTextPropertyName->value => $diagramNodeWithLatestChange->getProperty($diagramSvgTextPropertyName),
            ]),
        ));
    }

    public function getLatestDiagramNodeByDiagramIdentifier(string $diagramIdentifier, Node $contextNode): Node|null
    {
        $contentRepository = $this->contentRepositoryRegistry->get($contextNode->contentRepositoryId);
        $subgraph = $contentRepository->getcontentsubgraph(
            workspaceName: $contextNode->workspaceName,
            dimensionSpacePoint: $contextNode->dimensionSpacePoint
        );
        $siteNode = $subgraph->findClosestNode(
            entryNodeAggregateId: $contextNode->aggregateId,
            filter: FindClosestNodeFilter::create(nodeTypes: NodeTypeNameFactory::NAME_SITE)
        );

        // get diagram with the latest changes
        $diagramNodesOrdered = $subgraph->findDescendantNodes(
            entryNodeAggregateId: $siteNode->aggregateId,
            filter: FindDescendantNodesFilter::create(
                nodeTypes: NodeTypeCriteria::createWithAllowedNodeTypeNames(NodeTypeNames::with(MxGraphConstants::getNodeTypeName())),
                propertyValue: PropertyValueEquals::create(
                    propertyName: MxGraphConstants::getDiagramIdentifierPropertyName(),
                    value: $diagramIdentifier,
                    caseSensitive: true,
                ),
                ordering: [
                    OrderingField::byTimestampField(TimestampField::LAST_MODIFIED, OrderingDirection::DESCENDING),
                    OrderingField::byTimestampField(TimestampField::CREATED, OrderingDirection::DESCENDING),
                ],
            ),
        );

        // get first node that is not the node itself
        foreach ($diagramNodesOrdered as $diagramNode) {
            if ($diagramNode->equals($contextNode)) {
                continue;
            }

            return $diagramNode;
        }

        return null;
    }
}
