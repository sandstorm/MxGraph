# Diagrams.net / Draw.io

(formerly known as MXGraph, that explains the name of this package).

**Integrates diagrams.net / Draw.io into Neos as Node Type for creating interactive diagrams and flowcharts**

**Thanks to the awesome people from diagrams.net  / Draw.io - without them this project would not be possible.**

## Demo

![graphdemo](https://cloud.githubusercontent.com/assets/190777/20837205/53770d2a-b8a3-11e6-8c89-2f925c55e894.gif)

## Features

* provides a Node Type "Diagram" which contains the rendered representation for the diagram and the internal xml code
* you can click an inspector button opening the graph editor
* when you save, the graph data is directly stored inside the Node
* when you save the graph, Neos refreshes automatically
* (new since 3.x) all diagrams.net features supported
* (new since 3.x) customization of diagrams.net editor possible
* (new since 3.x, optional) auto-updates can be enabled by using embed.diagrams.net
* (new since 3.x) renders diagram as embedded SVG; so you can f.e. click links
* (new since 3.x) Import .drawio, .svg, .png files

## How to install

* Neos 8.x: `composer install sandstorm/mxgraph` - Version 3.x

then, enjoy :-)

## Upgrading from Sandstorm.MxGraph 2.x to 3.x

In Sandstorm.MxGraph >= 3.0, we do not render the diagram as SVG image embedded via the `<img>`-Tag,
but instead directly embed the SVG at that location. This has the benefit that f.e. [links inside the diagram](https://www.diagrams.net/doc/faq/insert-text-link))
will work as expected.

If you update from 2.x to 3.x, this change might break your styling. To get the old behavior back,
set the following `fusion` configuration:

```neosfusion
prototype(Sandstorm.MxGraph:Diagram) {
    // BACKWARDS COMPATIBILITY: to get the old (2.x) behavior back, set to false.
    renderAsInlineSvg = false
}
```

You need to open and save diagrams created with version <= 2.X to render them as inline SVGs, because the
internal storage format of the Node changed to enable this functionality (and this is only re-written on save).

## Custom Draw.io Configuration

Diagrams.net / Draw.io is very customizable (custom fonts, colors, presets, ...) - lots of configuration options
[documented here](https://www.diagrams.net/doc/faq/configure-diagram-editor). These config options
can be set via `Settings.yaml` at `Sandstorm.MxGraph.drawioConfiguration`, for example, the following sets up
draw.io with colors and fonts for Neos.io:

```yaml
Sandstorm:
  MxGraph:
    # You can configure the diagram editor as explained here: https://www.diagrams.net/doc/faq/configure-diagram-editor
    # !! lots of options possible - see docs for details.
    drawioConfiguration:
      customFonts:
        - fontFamily: Work Sans
          fontUrl: https://fonts.googleapis.com/css?family=Work+Sans
      # Colour codes to be added before presetColors (no leading # for the colour codes, null for a blank entry)
      customPresetColors:
        - '26224C'
        - '00ADEE'
      # colorNames: Names for colors, eg. {‘FFFFFF’: ‘White’, ‘000000’: ‘Black’} that are used as tooltips (uppercase, no leading # for the colour codes).
      colorNames:
        26224C: Neos Dark Blue
        00ADEE: Neos Light Blue

      # Available colour schemes in the style section at the top of the format panel (use leading # for the colour codes).
      # Possible colour keys are fill, stroke, gradient and font (font is ignored for connectors). An optional title can
      # be added to be used as a tooltip. An optional border can be added to define the CSS for the border width and type,
      # eg. ‘2px solid’ or ‘3px dashed’ (only setting the border width is not valid, the border type must be included).
      customColorSchemes:
        # !! the 1st array level is the **page**, then come the entries.
        -
          - title: Neos Light Blue solid
            fill: '#00ADEE'
            stroke: white
            border: 1px solid
            font: white
          - title: Neos Dark Blue solid
            fill: '#26224C'
            stroke: white
            border: 1px solid
            font: white
          - title: Neos Light Blue border
            fill: '#ffffff'
            stroke: '#00ADEE'
            border: 2px solid
            font: '#00ADEE'

```

## Importing files

In the inspector, there is a button to import a drawio file. This works for:

- .drawio files
- .drawio.png files (PNG files where the diagram is embedded)
- .drawio.svg files (SVG files where the diagram is embedded)

## Further Customization of embed mode via URL parameters

Until version <= 2.x, we've used a repository https://github.com/jgraph/mxgraph as basis. This has been discontinued
at 9.11.2020; and the code has been merged into https://github.com/jgraph/drawio/tree/dev/src/main/webapp.

For version 3, the implementation has been completely re-done; and it looks like this:

```text
┌────────────────────────────────────────┐
│                                        │
│   ┌────────────────────────────────┐   │
│   │       embed.diagrams.net       │   │
│   │       - Diagram Editor -       │   │
│   │                                │   │
│   └────────────────────────────────┘   │
│               outer shell              │
│ (communicates with diagrams.net in     │
│  iFrame, and with Neos)                │
└────────────────────────────────────────┘
```

We follow the documentation of the [embed mode](https://www.diagrams.net/doc/faq/embed-mode). The outer shell
embeds diagrams.net in an iFrame, and communicates initially via URL parameters, and then via postMessage API.

We use the following URL parameters:
- embed=1: enables embed mode, required
- configure=1: allows to inject [draw.io configuration](https://www.diagrams.net/doc/faq/configure-diagram-editor) from
  the outside, required.
- proto=json: modern, json-based postMessage API, required.

On top, the following optional URL parameters are set by default, but could be disabled / overridden via Settings.yaml
at key `Sandstorm.MxGraph.drawioEmbedParameters`:

- ui=simple: Use the modern UI theme. the other themes are [listed here](https://www.diagrams.net/doc/faq/editor-theme-change)
- lockdown=1: Ensure no data is transmitted to external services. [see this explanation](https://www.diagrams.net/blog/data-governance-lockdown)
- stealth=1: Disables all features that require external web services (such as PDF export).
- test=1: if enabled, debug logs are printed to the console.
- math=0: disables MathML support. (MathML is disabled for fully-disconnected mode; because it would grow the Neos package
  considerably).

## Fully-Disconnected Mode

By using embed.diagrams.net in the iFrame, you get the following properties:

*Pros*:

- Auto-updated to new versions of diagrams.net, so you can use the latest features
- Integration path is officially supported by the diagrams.net team
- All cloud features (like PDF export) can be enabled if you want.
- All features can be used.

*Cons*:
- While the diagram data is not leaving your local computer (for almost all use cases), the IP address
  of your editors is still sent to embed.diagrams.net. We believe this constitutes personal data under
  the GPDR; so you need to ask for consent from your editors.

That's why we additionally have implemented **a fully-disconnected mode:**

There, the full draw.io application has been bundled **inside this Neos package, with no external calls being made.**
We cannot be 100% sure that all Draw.io functionality will work, but we did not find any problems so far.

That's why the **fully disconnected mode is enabled by default, by setting `Sandstorm.MyGraph.drawioEmbedUrl=LOCAL`**


## License

GPL v3

Created with ❤ by sandstorm|media 2016-2023.
