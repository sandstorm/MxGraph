(() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __esm = (fn, res) => function __init() {
    return fn && (res = (0, fn[__getOwnPropNames(fn)[0]])(fn = 0)), res;
  };
  var __commonJS = (cb, mod) => function __require() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
    // If the importer is in node compatibility mode or this is not an ESM
    // file that has been converted to a CommonJS file using a Babel-
    // compatible transform (i.e. "__esModule" has not been set), then set
    // "default" to the CommonJS "module.exports" for node compatibility.
    isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
    mod
  ));
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);
  var __decorateClass = (decorators, target, key, kind) => {
    var result = kind > 1 ? void 0 : kind ? __getOwnPropDesc(target, key) : target;
    for (var i = decorators.length - 1, decorator; i >= 0; i--)
      if (decorator = decorators[i])
        result = (kind ? decorator(target, key, result) : decorator(result)) || result;
    if (kind && result)
      __defProp(target, key, result);
    return result;
  };

  // node_modules/@neos-project/neos-ui-extensibility/dist/manifest.js
  var init_manifest = __esm({
    "node_modules/@neos-project/neos-ui-extensibility/dist/manifest.js"() {
    }
  });

  // node_modules/@neos-project/neos-ui-extensibility/dist/createConsumerApi.js
  var init_createConsumerApi = __esm({
    "node_modules/@neos-project/neos-ui-extensibility/dist/createConsumerApi.js"() {
      init_manifest();
    }
  });

  // node_modules/@neos-project/neos-ui-extensibility/dist/readFromConsumerApi.js
  function readFromConsumerApi(key) {
    return (...args) => {
      if (window["@Neos:HostPluginAPI"] && window["@Neos:HostPluginAPI"][`@${key}`]) {
        return window["@Neos:HostPluginAPI"][`@${key}`](...args);
      }
      throw new Error("You are trying to read from a consumer api that hasn't been initialized yet!");
    };
  }
  var init_readFromConsumerApi = __esm({
    "node_modules/@neos-project/neos-ui-extensibility/dist/readFromConsumerApi.js"() {
    }
  });

  // node_modules/@neos-project/neos-ui-extensibility/dist/registry/AbstractRegistry.js
  var init_AbstractRegistry = __esm({
    "node_modules/@neos-project/neos-ui-extensibility/dist/registry/AbstractRegistry.js"() {
    }
  });

  // node_modules/@neos-project/positional-array-sorter/dist/positionalArraySorter.js
  var init_positionalArraySorter = __esm({
    "node_modules/@neos-project/positional-array-sorter/dist/positionalArraySorter.js"() {
    }
  });

  // node_modules/@neos-project/neos-ui-extensibility/dist/registry/SynchronousRegistry.js
  var init_SynchronousRegistry = __esm({
    "node_modules/@neos-project/neos-ui-extensibility/dist/registry/SynchronousRegistry.js"() {
      init_AbstractRegistry();
      init_positionalArraySorter();
    }
  });

  // node_modules/@neos-project/neos-ui-extensibility/dist/registry/SynchronousMetaRegistry.js
  var init_SynchronousMetaRegistry = __esm({
    "node_modules/@neos-project/neos-ui-extensibility/dist/registry/SynchronousMetaRegistry.js"() {
      init_SynchronousRegistry();
    }
  });

  // node_modules/@neos-project/neos-ui-extensibility/dist/registry/index.js
  var init_registry = __esm({
    "node_modules/@neos-project/neos-ui-extensibility/dist/registry/index.js"() {
      init_SynchronousRegistry();
      init_SynchronousMetaRegistry();
    }
  });

  // node_modules/@neos-project/neos-ui-extensibility/dist/index.js
  var dist_default;
  var init_dist = __esm({
    "node_modules/@neos-project/neos-ui-extensibility/dist/index.js"() {
      init_createConsumerApi();
      init_readFromConsumerApi();
      init_registry();
      dist_default = readFromConsumerApi("manifest");
    }
  });

  // node_modules/@neos-project/neos-ui-extensibility/dist/shims/vendor/react/index.js
  var require_react = __commonJS({
    "node_modules/@neos-project/neos-ui-extensibility/dist/shims/vendor/react/index.js"(exports, module) {
      init_readFromConsumerApi();
      module.exports = readFromConsumerApi("vendor")().React;
    }
  });

  // node_modules/@neos-project/neos-ui-extensibility/dist/shims/neosProjectPackages/react-ui-components/index.js
  var require_react_ui_components = __commonJS({
    "node_modules/@neos-project/neos-ui-extensibility/dist/shims/neosProjectPackages/react-ui-components/index.js"(exports, module) {
      init_readFromConsumerApi();
      module.exports = readFromConsumerApi("NeosProjectPackages")().ReactUiComponents;
    }
  });

  // node_modules/@neos-project/neos-ui-extensibility/dist/shims/neosProjectPackages/neos-ui-redux-store/index.js
  var require_neos_ui_redux_store = __commonJS({
    "node_modules/@neos-project/neos-ui-extensibility/dist/shims/neosProjectPackages/neos-ui-redux-store/index.js"(exports, module) {
      init_readFromConsumerApi();
      module.exports = readFromConsumerApi("NeosProjectPackages")().NeosUiReduxStore;
    }
  });

  // node_modules/@neos-project/neos-ui-extensibility/dist/shims/vendor/react-redux/index.js
  var require_react_redux = __commonJS({
    "node_modules/@neos-project/neos-ui-extensibility/dist/shims/vendor/react-redux/index.js"(exports, module) {
      init_readFromConsumerApi();
      module.exports = readFromConsumerApi("vendor")().reactRedux;
    }
  });

  // src/DiagramEditor.js
  var import_react, import_react_ui_components, import_neos_ui_redux_store, import_react_redux, DiagramEditor;
  var init_DiagramEditor = __esm({
    "src/DiagramEditor.js"() {
      import_react = __toESM(require_react());
      import_react_ui_components = __toESM(require_react_ui_components());
      import_neos_ui_redux_store = __toESM(require_neos_ui_redux_store());
      import_react_redux = __toESM(require_react_redux());
      DiagramEditor = class extends import_react.PureComponent {
        render() {
          const currentIframeUrl = this.props.currentIframeUrl;
          const persistChanges = this.props.persistChanges;
          const focusedNode = this.props.focusedNode;
          window.SandstormMxGraphApi = {
            reloadPage() {
              [].slice.call(document.querySelectorAll(`iframe[name=neos-content-main]`)).forEach((iframe) => {
                const iframeWindow = iframe.contentWindow || iframe;
                if (iframeWindow.location.href === currentIframeUrl) {
                  iframeWindow.location.href = iframeWindow.location.href;
                }
              });
              persistChanges([
                {
                  type: "Sandstorm.MxGraph:ReloadChangedState",
                  subject: focusedNode?.contextPath
                }
              ]);
            }
          };
          console.log(this.props.focusedNode);
          const targetUrl = "/neos/mxgraph?diagramNode=" + this.props.focusedNode?.contextPath;
          const clickButton = () => {
            window.SandstormMxGraphApiImport = null;
            window.open(targetUrl, "_blank");
          };
          const importDiagram = () => {
            const input = document.createElement("input");
            input.type = "file";
            input.accept = ".drawio,.png,.svg";
            input.onchange = (_) => {
              const files = Array.from(input.files);
              const reader = new FileReader();
              reader.addEventListener(
                "load",
                () => {
                  window.SandstormMxGraphApiImport = reader.result;
                  window.open(targetUrl + "#import", "_blank");
                },
                false
              );
              const isDrawioFile = files[0].name.endsWith(".drawio");
              if (isDrawioFile) {
                reader.readAsText(files[0]);
              } else {
                reader.readAsDataURL(files[0]);
              }
            };
            input.click();
          };
          return /* @__PURE__ */ import_react.default.createElement(import_react.Fragment, null, /* @__PURE__ */ import_react.default.createElement("div", { style: { paddingBottom: "16px" } }, /* @__PURE__ */ import_react.default.createElement(import_react_ui_components.Button, { style: "brand", onClick: clickButton }, "Open Diagram Editor")), /* @__PURE__ */ import_react.default.createElement("div", null, /* @__PURE__ */ import_react.default.createElement(import_react_ui_components.Button, { onClick: importDiagram }, "Import draw.io files")));
        }
      };
      DiagramEditor = __decorateClass([
        (0, import_react_redux.connect)((state) => ({
          focused: state?.cr?.nodes?.focused,
          focusedNode: import_neos_ui_redux_store.selectors.CR.Nodes.focusedSelector(state),
          focusedNodesContextPaths: import_neos_ui_redux_store.selectors.CR.Nodes.focusedNodePathsSelector(state),
          currentIframeUrl: state?.ui?.contentCanvas?.src
        }), {
          persistChanges: import_neos_ui_redux_store.actions.Changes.persistChanges
        })
      ], DiagramEditor);
    }
  });

  // src/manifest.js
  var manifest_exports = {};
  var init_manifest2 = __esm({
    "src/manifest.js"() {
      init_dist();
      init_DiagramEditor();
      dist_default("Sandstorm.MxGraph", {}, (globalRegistry) => {
        const editorsRegistry = globalRegistry.get("inspector").get("editors");
        editorsRegistry.set("Sandstorm.MxGraph/Editors/DiagramEditor", {
          component: DiagramEditor
        });
      });
    }
  });

  // src/index.js
  init_manifest2();
})();
