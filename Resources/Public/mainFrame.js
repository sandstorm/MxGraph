let diagramIframe;
window.addEventListener('DOMContentLoaded', () => {
    diagramIframe = document.getElementById('diagramIframe');
})

;

(function() {
// Modeled after https://github.com/jgraph/drawio-integration/blob/master/diagram-editor.js
    function handleMessageEvent(evt) {
        if (diagramIframe != null && evt.source == diagramIframe.contentWindow && evt.data.length > 0) {
            try {
                var msg = JSON.parse(evt.data);

                if (msg != null) {
                    handleMessage(msg);
                }
            } catch (e) {
                console.error(e, evt);
            }
        }
    }

    function postMessageToDrawioIframe(msg) {
        if (diagramIframe != null) {
            diagramIframe.contentWindow.postMessage(JSON.stringify(msg), '*');
        }
    }


    function stopEditing() {
        window.close();
    }


    let shouldExitAfterNextExport = false;
    function handleMessage(msg) {
        if (msg.event === 'configure') {
            postMessageToDrawioIframe({action: 'configure', config: window.DRAWIO_CONFIGURATION});
        } else if (msg.event == 'init') {
            // TODO: autosave is disabled for now, as we need to export on every save.
            postMessageToDrawioIframe({action: 'load', autosave: 0, saveAndExit: '1',
                modified: 'unsavedChanges', xml: window.DIAGRAM_CONTENTS});
        } else if (msg.event == 'autosave') {
            const formData = new FormData();
            formData.append("node", window.DIAGRAM_NODE);
            formData.append("xml", msg.xml);
            formData.append("svg", "");
            fetch(window.SAVE_URL, {
                method: "POST",
                credentials: "same-origin",
                body: formData
            });
        } else if (msg.event == 'save') {
            postMessageToDrawioIframe({
                action: 'export', format: "svg",
            });

            if (msg.exit) {
                shouldExitAfterNextExport = true;
            }
        } else if (msg.event == 'export') {
            fetch(msg.data)
                .then(res => res.text())
                .then(svgBlob => {
                    const formData = new FormData();


                    formData.append("node", window.DIAGRAM_NODE);
                    formData.append("xml", msg.xml);
                    formData.append("svg", svgBlob);
                    fetch(window.SAVE_URL, {
                        method: "POST",
                        credentials: "same-origin",
                        body: formData
                    }).then(() => {
                        postMessageToDrawioIframe({action: 'status', messageKey: 'allChangesSaved', modified: false});
                        if (window.opener && window.opener.Typo3Neos && window.opener.Typo3Neos.Content) {
                            window.opener.Typo3Neos.Content.reloadPage();
                        }

                        if (shouldExitAfterNextExport) {
                            stopEditing(msg);
                        }
                    });
                })

        } else if (msg.event == 'exit') {
            shouldExitAfterNextExport = true;
            postMessageToDrawioIframe({
                action: 'export', format: "svg",
            });
        }
    }

    window.addEventListener('message', handleMessageEvent);

})()
