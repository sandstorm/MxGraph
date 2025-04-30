<?php

namespace Sandstorm\MxGraph\ContentRepository\CommandHook;

use Neos\ContentRepository\Core\CommandHandler\CommandHookInterface;
use Neos\ContentRepository\Core\Factory\CommandHookFactoryInterface;
use Neos\ContentRepository\Core\Factory\CommandHooksFactoryDependencies;
use Sandstorm\MxGraph\ContentRepository\DiagramContentRepositoryService;

class DiagramCommandHookFactory implements CommandHookFactoryInterface
{
    public function __construct(
        protected DiagramContentRepositoryService $diagramContentRepositoryService,
    )
    {
    }

    public function build(CommandHooksFactoryDependencies $commandHooksFactoryDependencies): CommandHookInterface
    {
        return new DiagramCommandHook(
            $commandHooksFactoryDependencies->contentGraphReadModel,
            $this->diagramContentRepositoryService,
        );
    }
}
