<?php
namespace Sandstorm\MxGraph\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

class StandardController extends ActionController
{
    /**
     * @param NodeInterface $diagramNode
     */
    public function indexAction(NodeInterface $diagramNode)
    {
        $this->view->assign('diagram', $diagramNode->getProperty('diagramSource'));
        $this->view->assign('diagramNode', $diagramNode->getContextPath());
    }

    public function initializeSaveAction()
    {
        $filename = $this->request->getArgument('filename');
        $filename = urldecode($filename);
        $this->request->setArgument('filename', $filename);

        $xml = $this->request->getArgument('xml');
        $xml = urldecode($xml);
        $this->request->setArgument('xml', $xml);
    }

    /**
     * @param NodeInterface $filename
     * @param string $xml
     * @Flow\SkipCsrfProtection
     */
    public function saveAction(NodeInterface $filename, $xml)
    {
        $filename->setProperty('diagramSource', $xml);
        return "OK";
    }
}