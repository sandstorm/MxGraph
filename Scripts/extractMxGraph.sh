#!/usr/bin/env bash

git clone https://github.com/jgraph/mxgraph.git
rm -Rf ../Resources/Public/vendor/mxgraph
mkdir -p ../Resources/Public/vendor/mxgraph
cp -R mxgraph/javascript/examples/grapheditor/www/ ../Resources/Public/vendor/mxgraph/

cp -R mxgraph/javascript/mxClient.js ../Resources/Public/vendor/mxgraph/
cp -R mxgraph/javascript/src/css/ ../Resources/Public/vendor/mxgraph/css
cp -R mxgraph/javascript/src/images/ ../Resources/Public/vendor/mxgraph/images

rm -Rf ../Resources/Public/vendor/mxgraph/*.html