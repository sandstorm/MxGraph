<?php
namespace Sandstorm\MxGraph\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Model\ImageInterface;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

class DiagramEditorController extends ActionController
{

    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @param NodeInterface $diagramNode
     */
    public function indexAction(NodeInterface $diagramNode)
    {
        $this->view->assign('diagram', $diagramNode->getProperty('diagramSource'));
        $this->view->assign('diagramNode', $diagramNode->getContextPath());
    }

    /**
     * @param NodeInterface $node
     * @param string $xml
     * @param string $svg
     * @Flow\SkipCsrfProtection
     */
    public function saveAction(NodeInterface $node, $xml, $svg)
    {
        $node->setProperty('diagramSource', $xml);

        $persistentResource = $this->resourceManager->importResourceFromContent($svg, 'diagram.svg');

        $image = $node->getProperty('image');
        if ($image instanceof Asset) {
            $image->setResource($persistentResource);
        } else {
            $image = new Image($persistentResource);
        }

        $node->setProperty('image', $image);

        return 'OK';
    }
}
