<?php

namespace Sandstorm\MxGraph\Changes;

use Neos\Neos\Ui\Domain\Model\AbstractChange;
use Sandstorm\MxGraph\Domain\Model\Diagram;

class ReloadChangedState extends AbstractChange
{

    public function canApply()
    {
        return true;
    }

    public function apply()
    {
        $this->updateWorkspaceInfo();
    }
}
