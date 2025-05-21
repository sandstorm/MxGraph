<?php

namespace Sandstorm\MxGraph\ContentRepository\CommandHook;

use Neos\ContentRepository\Core\CommandHandler\CommandHookInterface;
use Neos\ContentRepository\Core\CommandHandler\CommandInterface;
use Neos\ContentRepository\Core\CommandHandler\Commands;
use Neos\ContentRepository\Core\EventStore\PublishedEvents;
use Neos\ContentRepository\Core\Feature\NodeModification\Command\SetNodeProperties;
use Neos\ContentRepository\Core\Projection\ContentGraph\ContentGraphReadModelInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\VisibilityConstraints;
use Sandstorm\MxGraph\ContentRepository\DiagramContentRepositoryService;
use Sandstorm\MxGraph\MxGraphConstants;

final class DiagramCommandHook implements CommandHookInterface {
    public function __construct(
        protected ContentGraphReadModelInterface $contentGraphReadModel,
        protected DiagramContentRepositoryService $diagramContentRepositoryService,
    )
    {
    }

    public function onBeforeHandle(CommandInterface $command): CommandInterface
    {
        return $command;
    }

    public function onAfterHandle(CommandInterface $command, PublishedEvents $events): Commands
    {
        if ($command instanceof SetNodeProperties) {
            $sourceNode = $this->contentGraphReadModel
                ->getContentGraph($command->workspaceName)
                ->getSubgraph(
                    dimensionSpacePoint: $command->originDimensionSpacePoint->toDimensionSpacePoint(),
                    visibilityConstraints: VisibilityConstraints::default(),
                )
                ->findNodeById($command->nodeAggregateId);

            if ($sourceNode !== null && $sourceNode->nodeTypeName->equals(MxGraphConstants::getNodeTypeName())) {
                // TODO: A "PropertyValuesToWrite->hasProperty(PropertyName $propertyName): bool" API would be nice
                if (array_key_exists(MxGraphConstants::getDiagramIdentifierPropertyName()->value, $command->propertyValues->values)) {
                    // diagramIdentifier was updated -> copy over the latest changes into this node
                    $this->diagramContentRepositoryService->applyLastestDiagramDataOfDiagramIdentifierToNode(
                        // TODO: A "PropertyValuesToWrite->getProperty(PropertyName $propertyName): mixed" API would be nice
                        diagramIdentifier: $command->propertyValues->values[MxGraphConstants::getDiagramIdentifierPropertyName()->value],
                        node: $sourceNode,
                    );
                }
            }
        }

        return Commands::createEmpty();
    }
}
