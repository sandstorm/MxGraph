<?php

namespace Sandstorm\MxGraph\Changes;

use Neos\Neos\Ui\Domain\Model\AbstractChange;

/**
 * This is triggered via PHP when a diagram is saved.
 */
class ReloadChangedState extends AbstractChange
{

    public function canApply(): bool
    {
        return true;
    }

    public function apply(): void
    {
        $this->updateWorkspaceInfo();
    }
}
