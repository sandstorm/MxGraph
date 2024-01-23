<?php

namespace Sandstorm\MxGraph;


use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\Domain\Service\NodeSearchServiceInterface;

/**
 * @Flow\Scope("singleton")
 */
class DiagramIdentifierSearchService
{

    /**
     * @Flow\Inject
     * @var NodeSearchServiceInterface
     */
    protected $nodeSearchService;

    /**
     * @param string $query
     * @param NodeInterface $node
     * @return string[]
     */
    public function findInIdentifier(string $searchTerm, NodeInterface $node): array
    {
        $results = [];
        $possibleResults = $this->nodeSearchService->findByProperties($searchTerm, ['Sandstorm.MxGraph:Diagram'], $node->getContext());
        foreach ($possibleResults as $possibleResult) {
            assert($possibleResult instanceof NodeInterface);

            // we include the diagram identifier if it contains the $searchTerm (case-insensitively)
            $possibleDiagramIdentifier = $possibleResult->getProperty('diagramIdentifier');
            if (str_contains(strtolower($possibleDiagramIdentifier), strtolower($searchTerm))) {
                if (!isset($results[$possibleDiagramIdentifier])) {
                    $results[$possibleDiagramIdentifier] = $possibleDiagramIdentifier;
                }
            }
        }

        return array_values($results);
    }

    /**
     * @param string $diagramIdentifier
     * @return NodeInterface[]
     */
    public function findRelatedDiagramsWithIdentifierExcludingOwn(string $diagramIdentifier, NodeInterface $contextNode): array
    {
        $results = [];
        $possibleResults = $this->nodeSearchService->findByProperties($diagramIdentifier, ['Sandstorm.MxGraph:Diagram'], $contextNode->getContext());
        foreach ($possibleResults as $node) {
            assert($node instanceof NodeInterface);
            if ($contextNode->getContextPath() === $node->getContextPath()) {
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
     * @param NodeInterface $contextNode
     * @return NodeInterface|null
     */
    public function findMostRecentDiagramWithIdentifierExcludingOwn(string $diagramIdentifier, NodeInterface $contextNode): ?NodeInterface
    {
        $relatedDiagramNodes = $this->findRelatedDiagramsWithIdentifierExcludingOwn($diagramIdentifier, $contextNode);

        uasort($relatedDiagramNodes, function(NodeInterface $a, NodeInterface $b) {
            if (method_exists($a, 'getLastModificationDateTime') && method_exists($b, 'getLastModificationDateTime')) {
                return $b->getLastModificationDateTime() <=> $a->getLastModificationDateTime();
            }

            return 0;
        });

        if (count($relatedDiagramNodes) > 0) {
            // the 1st element is the one with the latest modification time.
            return reset($relatedDiagramNodes);
        }
        return null;
    }
}
