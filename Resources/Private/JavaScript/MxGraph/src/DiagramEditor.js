import React, {Fragment, PureComponent} from 'react';
import {Button} from '@neos-project/react-ui-components';
import {actions, selectors} from '@neos-project/neos-ui-redux-store';
import {connect} from 'react-redux';

@connect(state => ({
    focused: state?.cr?.nodes?.focused,
    focusedNode: selectors.CR.Nodes.focusedSelector(state),
    focusedNodesContextPaths: selectors.CR.Nodes.focusedNodePathsSelector(state),
    currentIframeUrl: state?.ui?.contentCanvas?.src,
}), {
    persistChanges: actions.Changes.persistChanges
})
export default class DiagramEditor extends PureComponent {
    render() {
        const currentIframeUrl = this.props.currentIframeUrl;
        const persistChanges = this.props.persistChanges;
        const focusedNode = this.props.focusedNode;

        window.SandstormMxGraphApi = {
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

                // Trigger an updateWorkspaceInfo to ensure the publish button
                // is up to date
                persistChanges([
                    {
                        type: 'Sandstorm.MxGraph:ReloadChangedState',
                        subject: focusedNode?.contextPath,
                    }
                ]);
            }
        };

        console.log(this.props.focusedNode);
        const targetUrl = '/neos/mxgraph?diagramNode=' + this.props.focusedNode?.contextPath;
        const clickButton = () => {
            window.SandstormMxGraphApiImport = null;
            window.open(targetUrl, '_blank');
        };

        const importDiagram = () => {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = ".drawio,.png,.svg"
            input.onchange = _ => {
                const files = Array.from(input.files);
                const reader = new FileReader();
                reader.addEventListener(
                    "load",
                    () => {
                        window.SandstormMxGraphApiImport = reader.result;

                        // NOTE: this will trigger a browser popup warning
                        // - there is no way around, because I did not find a way
                        // to open BOTH the file picker and the new tab
                        // without any interaction.
                        window.open(targetUrl + "#import", '_blank');
                    },
                    false
                );

                const isDrawioFile = files[0].name.endsWith(".drawio");
                if (isDrawioFile) {
                    // Drawio files must be read as plain text
                    reader.readAsText(files[0]);
                } else {
                    // all other files can be read as binary
                    reader.readAsDataURL(files[0]);
                }
            };
            input.click();
        };
        return (
            <Fragment>
                <div style={{paddingBottom: '16px'}}>
                    <Button style="brand" onClick={clickButton}>
                        Open Diagram Editor
                    </Button>
                </div>
                <div>
                    <Button onClick={importDiagram}>
                        Import draw.io files
                    </Button>
                </div>
            </Fragment>
        );
    }
}
