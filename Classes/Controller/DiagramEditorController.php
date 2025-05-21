<?php
namespace Sandstorm\MxGraph\Controller;

use Neos\ContentRepository\Core\Feature\Security\Exception\AccessDenied;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAddress;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Neos\Domain\Service\UserService;
use Sandstorm\MxGraph\ContentRepository\DiagramContentRepositoryService;

class DiagramEditorController extends ActionController
{
    const LOCAL_DRAWIO_EMBED_URL = 'LOCAL';

    #[Flow\Inject]
    protected UserService $userService;

    #[Flow\Inject]
    protected DiagramContentRepositoryService $diagramContentRepositoryService;

    #[Flow\InjectConfiguration(path: 'drawioEmbedUrl')]
    protected string $drawioEmbedUrl;

    #[Flow\InjectConfiguration(path: 'drawioEmbedParameters')]
    protected array $drawioEmbedParameters;

    #[Flow\InjectConfiguration(path: 'drawioConfiguration')]
    protected array|null $drawioConfiguration;

    public function indexAction(Node $diagramNode): void
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
        } elseif ($interfaceLanguage === 'es') {
            $drawioLanguage = 'es';
        } elseif ($interfaceLanguage === 'fi') {
            $drawioLanguage = 'fi';
        } elseif ($interfaceLanguage === 'fr') {
            $drawioLanguage = 'fr';
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
        } elseif ($interfaceLanguage !== 'en') {
            // default or míssing language setting
            $this->logger->warning('Unknown interface language: ' . $interfaceLanguage);
        }

        if (!empty($drawioLanguage)) {
            $drawioEmbedParameters['lang'] = $drawioLanguage;
        }

        $drawioEmbedUrlWithParameters .= '?' .  http_build_query($drawioEmbedParameters);

        $this->view->assign('diagram', $diagramNode->getProperty('diagramSource'));
        $this->view->assign('diagramNode', NodeAddress::fromNode($diagramNode)->toJson());
        $this->view->assign('drawioEmbedUrlWithParameters', $drawioEmbedUrlWithParameters);
        $this->view->assign('drawioConfiguration', (array)$this->drawioConfiguration);

    }

    /**
     * This renders Resources/Private/Templates/DiagramEditor/OfflineLocalDiagramsNet.html (see indexAction)
     */
    public function offlineLocalDiagramsNetAction()
    {
    }

    /**
     * @Flow\SkipCsrfProtection
     * @throws AccessDenied
     */
    public function saveAction(Node $node, string $xml, string $svg): string
    {
        if (empty($svg)) {
            // XML without SVG -> autosaved - not supported right now.
            throw new \RuntimeException("DiagramEditorController::saveAction: autosave not supported right now.");
        }

        $this->diagramContentRepositoryService->applyDataToDiagramNodeAndRelatedNodes($node, $xml, $svg);

        return 'OK';
    }
}
