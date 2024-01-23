<?php

namespace Sandstorm\MxGraph;

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\I18n\Translator;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Sandstorm\LazyDataSource\LazyDataSourceTrait;

class DiagramIdentifierDataSource extends AbstractDataSource
{

    use LazyDataSourceTrait;

    static protected $identifier = 'drawio-diagram-identifier';

    /**
     * @Flow\Inject
     * @var Translator
     */
    protected $translator;

    /**
     * @Flow\Inject
     * @var DiagramIdentifierSearchService
     */
    protected $diagramIdentifierSearchService;

    protected function getDataForIdentifiers(array $identifiers, NodeInterface $node = null, array $arguments = [])
    {
        // all identifiers will be returned as is (with a label containing usage count)
        $options = [];
        foreach ($identifiers as $id) {
            $options[$id] = ['label' => $this->getLabelFor($id, $node)];
        }
        return $options;
    }

    protected function searchData(string $searchTerm, NodeInterface $node = null, array $arguments = [])
    {
        $options = [];
        if ($node !== null) {
            $diagramIdentifiers = $this->diagramIdentifierSearchService->findInIdentifier($searchTerm, $node);
            foreach ($diagramIdentifiers as $diagramIdentifier) {
                $options[$diagramIdentifier] = ['label' => $this->getLabelFor($diagramIdentifier, $node)];
            }
        }

        $options[$searchTerm] = ['label' => $searchTerm, 'icon' => 'plus'];
        return $options;
    }

    protected function getLabelFor(string $diagramIdentifier, $node): string
    {
        $relatedCount = count($this->diagramIdentifierSearchService->findRelatedDiagramsWithIdentifierExcludingOwn($diagramIdentifier, $node));
        $label = $diagramIdentifier;
        if ($relatedCount > 0) {
            $label .= $this->translator->translateById('diagramIdentifierUsageLabel', [$relatedCount+1], null, null, 'Main', 'Sandstorm.MxGraph');
        }
        return $label;
    }
}
