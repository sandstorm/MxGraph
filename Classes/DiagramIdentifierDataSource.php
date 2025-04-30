<?php

namespace Sandstorm\MxGraph;

use Neos\ContentRepository\Core\Feature\Security\Exception\AccessDenied;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Translator;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Sandstorm\LazyDataSource\LazyDataSourceTrait;
use Sandstorm\MxGraph\ContentRepository\DiagramContentRepositoryService;

class DiagramIdentifierDataSource extends AbstractDataSource
{
    use LazyDataSourceTrait;

    protected static $identifier = 'drawio-diagram-identifier';

    #[Flow\Inject]
    protected Translator $translator;

    #[Flow\Inject]
    protected DiagramContentRepositoryService $diagramContentRepositoryService;

    protected function getDataForIdentifiers(array $identifiers, Node $node = null, array $arguments = []): array
    {
        // all identifiers will be returned as is (with a label containing usage count)
        $options = [];
        foreach ($identifiers as $diagramIdentifier) {
            $count = $this->diagramContentRepositoryService->countNodesWithDiagramIdentifier($diagramIdentifier, $node);

            $options[$diagramIdentifier] = ['label' => $this->translateLabel($diagramIdentifier, $count)];
        }
        return $options;
    }

    /**
     * @throws AccessDenied
     */
    protected function searchData(string $searchTerm, Node $node = null, array $arguments = []): array
    {
        $options = [];
        if ($node !== null) {
            $nodesWithDiagramIdentifierContainingSearchTerm = $this->diagramContentRepositoryService->searchByDiagramIdentifier($searchTerm, $node);

            // reduce Nodes to unique diagram identifiers with count of occurrences
            $diagramIdentifierWithOccurrence = [];
            foreach ($nodesWithDiagramIdentifierContainingSearchTerm as $node) {
                $diagramIdentifier = $node->getProperty(MxGraphConstants::getDiagramIdentifierPropertyName());

                if (array_key_exists($diagramIdentifier, $diagramIdentifierWithOccurrence)) {
                    $diagramIdentifierWithOccurrence[$diagramIdentifier]++;
                } else {
                    $diagramIdentifierWithOccurrence[$diagramIdentifier] = 1;
                }
            }

            // translate label and add to options
            foreach ($diagramIdentifierWithOccurrence as $diagramIdentifier => $count) {
                $options[$diagramIdentifier] = ['label' => $this->translateLabel($diagramIdentifier, $count)];
            }
        }

        // add option to create from searchTerm if searchTerm is not found in existing diagram identifiers
        if (!array_key_exists($searchTerm, $options)) {
            $options[$searchTerm] = ['label' => $searchTerm, 'icon' => 'plus'];
        }

        return $options;
    }

    private function translateLabel(string $label, int $count): string
    {
        $translatedCount = $this->translator->translateById('diagramIdentifierUsageLabel', [$count], null, null, 'Main', 'Sandstorm.MxGraph');

        return $label . $translatedCount;
    }
}
