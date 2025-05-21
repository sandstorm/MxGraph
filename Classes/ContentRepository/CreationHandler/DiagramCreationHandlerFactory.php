<?php

namespace Sandstorm\MxGraph\ContentRepository\CreationHandler;

use Neos\ContentRepository\Core\ContentRepository;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationCommands;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationElements;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationHandlerFactoryInterface;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationHandlerInterface;

final class DiagramCreationHandlerFactory implements NodeCreationHandlerFactoryInterface
{
    public function build(ContentRepository $contentRepository): NodeCreationHandlerInterface
    {
        return new class implements NodeCreationHandlerInterface {
            public function handle(NodeCreationCommands $commands, NodeCreationElements $elements): NodeCreationCommands
            {
                return (new DiagramCreationHandler)->handle($commands, $elements);
            }
        };
    }
}
