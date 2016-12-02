# MxGraph

**Integrates MXGraph / Draw.io into Neos as Node Type for creating interactive diagrams and flowcharts**

**Thanks to the awesome people from MXGraph  / Draw.io - without them this project would not be possible.**

## Demo

![graphdemo](https://cloud.githubusercontent.com/assets/190777/20837205/53770d2a-b8a3-11e6-8c89-2f925c55e894.gif)

## Features

* provides a Node Type "Diagram" which contains the rendered representation for the diagram and the internal xml code
* you can click an inspector button opening the graph editor
* when you save, the graph data is directly stored inside the Node
* when you save the graph, Neos refreshes automatically

## How to install

* Neos 2.3: `composer install sandstorm/mxgraph` - Version 1.x
* Neos Master (not yet supported)

then, enjoy :-)

## Next Steps

* support customizing the diagram editor; e.g. by making styles configurable
* add a version for Neos Master

## License

GPL v3

Created with ❤ by sandstorm|media 2016.
