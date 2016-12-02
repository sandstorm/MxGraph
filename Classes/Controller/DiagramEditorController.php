<?php
namespace Sandstorm\MxGraph\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use TYPO3\Flow\Resource\ResourceManager;
use TYPO3\Media\Domain\Model\Image;
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
     * @param NodeInterface $filename
     * @param string $xml
     * @param string $svg
     * @Flow\SkipCsrfProtection
     */
    public function saveAction(NodeInterface $filename, $xml, $svg)
    {
        $filename->setProperty('diagramSource', $xml);

        $persistentResource = $this->resourceManager->importResourceFromContent($svg, 'diagram.svg');
        $image = new Image($persistentResource);
        $filename->setProperty('image', $image);

        return "OK";
    }
}