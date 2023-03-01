# Draw.io Analysis

## How does Real Time collab work?

https://github.com/jgraph/drawio-nextcloud/issues/22

?? embedRT -> geht nur mit embed drawio app.?
=> `/ws` endpoint. P2PCollab.js
`window.RT_WEBSOCKET_URL` - der websocket ist encrypted per AES :)


## URL Params

Doku URL Params:

https://www.diagrams.net/doc/

https://www.diagrams.net/doc/faq/supported-url-parameters
https://www.diagrams.net/doc/faq/embed-mode
https://www.diagrams.net/blog/embedding-walkthrough


```bash
src/main/webapp/js/diagramly/Init.js

```


## Difference between https://app.diagrams.net/ and https://embed.diagrams.net/

```bash
dev analyze-diagrams-net
diff -u Documentation/tmp/appDiagramsNet.html Documentation/tmp/embedDiagramsNet.html
```

## Scratch.diagrams.net

```
=> redirect to
https://app.diagrams.net/?splash=0&ui=sketch
```


## How does the RT server work?

```
$frame_params = "?embed=1&embedRT=1"; // + Plugin ALLOW_CUSTOM_PLUGINS
// EmbedFile.prototype.isRealtimeSupported = function()
{
return true;
};
```

```
P2PCollab.js
config: { iceServers: [{ urls: 'stun:54.89.235.160:3478' }] }

case 'message':
  processMsg(data.msg, data.from);
  data.msg == plain text, json
break;
case 'clientsList':
  clientsList(data.msg);
    myClientId = data.msg.cId;
    data.msg.list[i]
break;
case 'signal':
  signal(data.msg);
    data.msg.from
    data.msg.signal
break;
case 'newClient':
  newClient(data.msg);
    data.msg == clientId
break;
case 'clientLeft':
  clientLeft(data.msg);
    daa.msg.clientId
break;
case 'sendSignalFailed':
  sendSignalFailed(data.msg);
    data.msg.to
break;
```

## How does the lane expansion etc work?

BPMN -> Horizontal Pool

https://github.com/jgraph/drawio/blob/dev/src/main/webapp/shapes/bpmn/mxBpmnShape2.js
=> https://www.diagrams.net/doc/faq/collapse-expand-enable-disable


## Configuration

https://www.diagrams.net/doc/faq/configure-diagram-editor


## Offline Mode

```
if ($_["drawioOfflineMode"] === "yes")
    {
        $frame_params .= "&offline=1&stealth=1";
    }

```

## Debugging

URL Param: test=1 -> for debugging messages

## Plugins




