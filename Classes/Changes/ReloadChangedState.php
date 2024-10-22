<?php

namespace Sandstorm\MxGraph\Changes;

use Neos\Neos\Ui\Domain\Model\AbstractChange;

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
