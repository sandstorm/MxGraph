import React, {PureComponent} from 'react';
import {Button} from '@neos-project/react-ui-components';
import {selectors} from '@neos-project/neos-ui-redux-store';
import {connect} from 'react-redux';
import {$get} from 'plow-js';

@connect(state => ({
    focusedNode: selectors.CR.Nodes.focusedSelector(state),
    currentIframeUrl: $get('ui.contentCanvas.src', state)
}))
export default class DiagramEditor extends PureComponent {
    render() {
        const currentIframeUrl = this.props.currentIframeUrl;

        window.Typo3Neos = {
            Content: {
                reloadPage() {
                    [].slice.call(document.querySelectorAll(`iframe[name=neos-content-main]`)).forEach(iframe => {
                        const iframeWindow = iframe.contentWindow || iframe;

                        //
                        // Make sure href is still consistent before reloading - if not, some other process
                        // might be already handling this
                        //
                        if (iframeWindow.location.href === currentIframeUrl) {
                            iframeWindow.location.href = iframeWindow.location.href;
                        }
                    });
                }
            }
        };

        const targetUrl = '/neos/mxgraph?diagramNode=' + $get('contextPath', this.props.focusedNode);
        const clickButton = () => {
            window.open(targetUrl, '_blank');
        };
        return (
            <Button onClick={clickButton}>
                Open Diagram Editor
            </Button>
        );
    }
}
