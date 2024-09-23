<?php
namespace Sandstorm\MxGraph\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Model\Image;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Neos\Domain\Service\UserService;
use Sandstorm\MxGraph\DiagramIdentifierSearchService;
use Sandstorm\MxGraph\Domain\Model\Diagram;

class DiagramEditorController extends ActionController
{

    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @Flow\Inject
     * @var UserService
     */
    protected $userService;

    /**
     * @Flow\Inject
     * @var DiagramIdentifierSearchService
     */
    protected $diagramIdentifierSearchService;

    /**
     * @Flow\InjectConfiguration(path="drawioEmbedUrl")
     * @var string
     */
    protected $drawioEmbedUrl;
    const LOCAL_DRAWIO_EMBED_URL = 'LOCAL';

    /**
     * @Flow\InjectConfiguration(path="drawioEmbedParameters")
     * @var array
     */
    protected $drawioEmbedParameters;

    /**
     * @Flow\InjectConfiguration(path="drawioConfiguration")
     * @var array
     */
    protected $drawioConfiguration;


    /**
     * @param NodeInterface $diagramNode
     */
    public function indexAction(NodeInterface $diagramNode)
    {
        $drawioEmbedUrlWithParameters = $this->drawioEmbedUrl;
        if ($drawioEmbedUrlWithParameters === self::LOCAL_DRAWIO_EMBED_URL) {
            $drawioEmbedUrlWithParameters = $this->uriBuilder->uriFor('offlineLocalDiagramsNet');
        }
        $drawioEmbedParameters = $this->drawioEmbedParameters;
        // these parameters must be hard-coded; otherwise our application won't work
        $drawioEmbedParameters['embed'] = '1';
        $drawioEmbedParameters['configure'] = '1';
        $drawioEmbedParameters['proto'] = 'json';

        $drawioLanguage = '';
        $interfaceLanguage = $this->userService->getCurrentUser()?->getPreferences()->getInterfaceLanguage();
        if ($interfaceLanguage === 'da') {
            $drawioLanguage = 'da';
        } elseif ($interfaceLanguage === 'de') {
            $drawioLanguage = 'de';
        } elseif ($interfaceLanguage === 'en') {
            // default
        } elseif ($interfaceLanguage === 'es') {
            $drawioLanguage = 'es';
        } elseif ($interfaceLanguage === 'fi') {
            $drawioLanguage = 'fi';
        } elseif ($interfaceLanguage === 'fr') {
            $drawioLanguage = 'fr';
        } elseif ($interfaceLanguage === 'km') {
            // TODO: MISSING AS IT SEEMS
        } elseif ($interfaceLanguage === 'lv') {
            $drawioLanguage = 'lv';
        } elseif ($interfaceLanguage === 'nl') {
            $drawioLanguage = 'nl';
        } elseif ($interfaceLanguage === 'no') {
            $drawioLanguage = 'no';
        } elseif ($interfaceLanguage === 'pl') {
            $drawioLanguage = 'pl';
        } elseif ($interfaceLanguage === 'pt-BR') {
            $drawioLanguage = 'pt-br';
        } elseif ($interfaceLanguage === 'ru') {
            $drawioLanguage = 'ru';
        } elseif ($interfaceLanguage === 'zh-CN') {
            $drawioLanguage = 'zh';
        }

        if (!empty($drawioLanguage)) {
            $drawioEmbedParameters['lang'] = $drawioLanguage;
        }

        $drawioEmbedUrlWithParameters .= '?' .  http_build_query($drawioEmbedParameters);

        $this->view->assign('diagram', $diagramNode->getProperty('diagramSource'));
        $this->view->assign('diagramNode', $diagramNode->getContextPath());
        $this->view->assign('drawioEmbedUrlWithParameters', $drawioEmbedUrlWithParameters);
        $this->view->assign('drawioConfiguration', is_array($this->drawioConfiguration) ? $this->drawioConfiguration : []);

    }

    /**
     */
    public function offlineLocalDiagramsNetAction()
    {
    }


    /**
     * @param NodeInterface $node
     * @param string $xml
     * @param string $svg
     * @Flow\SkipCsrfProtection
     */
    public function saveAction(NodeInterface $node, $xml, $svg)
    {

        if (empty($svg)) {
            // XML without SVG -> autosaved - not supported right now.
            $node->setProperty('diagramSourceAutosaved', $xml);
            throw new \RuntimeException("TODO - autosave not supported right now.");
        }

        $node->setProperty('diagramSource', $xml);
        // NEW since version 3.0.0
        $node->setProperty('diagramSvgText', $svg);

        $diagramIdentifier = $node->getProperty('diagramIdentifier');
        if (!empty($diagramIdentifier)) {
            // also update related diagrams
            foreach ($this->diagramIdentifierSearchService->findRelatedDiagramsWithIdentifierExcludingOwn($diagramIdentifier, $node) as $relatedDiagramNode) {
                $relatedDiagramNode->setProperty('diagramSource', $xml);
                $relatedDiagramNode->setProperty('diagramSvgText', $svg);
            }
        }

        // BEGIN DEPRECATION since version 3.0.0
        $persistentResource = $this->resourceManager->importResourceFromContent($svg, 'diagram.svg');

        $image = $node->getProperty('image');
        if ($image instanceof Asset) {
            // BUG: this also changes the live workspace - nasty. But if we remove it, we get 1000s of assets
            // cluttering the Media UI.
            $image->setResource($persistentResource);
        } else {
            $image = new Image($persistentResource);
        }

        $node->setProperty('image', $image);
        // END DEPRECATION since version 3.0.0

        return 'OK';
    }
}
