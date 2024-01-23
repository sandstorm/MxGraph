#!/bin/bash
############################## DEV_SCRIPT_MARKER ##############################
# This script is used to document and run recurring tasks in development.     #
#                                                                             #
# You can run your tasks using the script `./dev some-task`.                  #
# You can install the Sandstorm Dev Script Runner and run your tasks from any #
# nested folder using `dev some-task`.                                        #
# https://github.com/sandstorm/Sandstorm.DevScriptRunner                      #
###############################################################################

set -e

######### TASKS #########

function analyze-diagrams-net() {
  rm -Rf Documentation/tmp
  mkdir -p Documentation/tmp
  cd Documentation/tmp
  curl -o appDiagramsNet.html https://app.diagrams.net/
  curl -o embedDiagramsNet.html https://embed.diagrams.net/
}

function sync-drawio-offline-source() {
  mkdir tmp
  cd tmp
  if [ ! -d drawio ]; then
    git clone https://github.com/jgraph/drawio.git
  fi

  rm -Rf ../Resources/Public/vendor/drawio
  mkdir -p ../Resources/Public/vendor/drawio

  # the following folders have been determined empirically -
  mkdir -p ../Resources/Public/vendor/drawio/styles/
  mkdir -p ../Resources/Public/vendor/drawio/mxgraph/css
  mkdir -p ../Resources/Public/vendor/drawio/js
  mkdir -p ../Resources/Public/vendor/drawio/resources
  mkdir -p ../Resources/Public/vendor/drawio/images

  cp -R drawio/src/main/webapp/styles/grapheditor.css ../Resources/Public/vendor/drawio/styles/grapheditor.css
  cp -R drawio/src/main/webapp/styles/fonts ../Resources/Public/vendor/drawio/styles/fonts
  cp -R drawio/src/main/webapp/mxgraph/css/common.css ../Resources/Public/vendor/drawio/mxgraph/css/common.css
  cp -R drawio/src/main/webapp/js/app.min.js ../Resources/Public/vendor/drawio/js/
  cp -R drawio/src/main/webapp/js/shapes-*.min.js ../Resources/Public/vendor/drawio/js/
  cp -R drawio/src/main/webapp/js/extensions.min.js ../Resources/Public/vendor/drawio/js/
  cp -R drawio/src/main/webapp/js/stencils.min.js ../Resources/Public/vendor/drawio/js/
  cp -R drawio/src/main/webapp/resources/dia.txt ../Resources/Public/vendor/drawio/resources/dia.txt
  cp -R drawio/src/main/webapp/images/ ../Resources/Public/vendor/drawio/images

}

function clean() {
  rm -Rf Resources/Private/JavaScript/MxGraph/node_modules
}

function setup() {
  cd Resources/Private/JavaScript/MxGraph
  yarn install
}

function watch() {
  # https://stackoverflow.com/questions/69692842/error-message-error0308010cdigital-envelope-routinesunsupported
  export NODE_OPTIONS=--openssl-legacy-provider

  cd Resources/Private/JavaScript/MxGraph
  yarn run watch
}

function build() {
  # https://stackoverflow.com/questions/69692842/error-message-error0308010cdigital-envelope-routinesunsupported
  export NODE_OPTIONS=--openssl-legacy-provider

  cd Resources/Private/JavaScript/MxGraph
  yarn run build
}


####### Utilities #######

_log_success() {
  printf "\033[0;32m%s\033[0m\n" "${1}"
}
_log_warning() {
  printf "\033[1;33m%s\033[0m\n" "${1}"
}
_log_error() {
  printf "\033[0;31m%s\033[0m\n" "${1}"
}

# THIS NEEDS TO BE LAST!!!
# this will run your tasks
"$@"
