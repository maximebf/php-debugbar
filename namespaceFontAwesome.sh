#!/bin/bash

sed -i 's/FontAwesome/PhpDebugbarFontAwesome/g' src/DebugBar/Resources/vendor/font-awesome/css/font-awesome.min.css
sed -i 's/fa-/phpdebugbar-fa-/g' src/DebugBar/Resources/vendor/font-awesome/css/font-awesome.min.css
sed -i 's/\.fa/\.phpdebugbar-fa/g' src/DebugBar/Resources/vendor/font-awesome/css/font-awesome.min.css
