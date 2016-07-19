# ILess Plugin - Autoprefix

Autoprefixes the generated CSS using PostCSS autoprefixer plugin.

## Build Status

[![Build Status](https://travis-ci.org/mishal/iless-plugin-autoprefix.svg)](https://travis-ci.org/mishal/iless-plugin-autoprefix)

## Requirements

To use this plugin you need `node.js > 0.12` installed on the machine.

## Installation

Install using composer:

    $ composer require mishal/iless-plugin-autoprefix

Install requirements

    $ npm install postcss-cli autoprefixer

See `package.json` for required versions.

## Programmatic Usage

    use ILess\Parser;
    use ILess\Plugin\Autoprefix\AutoprefixPlugin;

    $parser = new Parser();
    // register the plugin
    $parser->getPluginManager()->addPlugin(new AutoprefixPlugin([
        // see https://github.com/ai/browserslist
        'browsers' => ['last 2 versions']
    ]));

    $parser->parseFile('/example.less');
    $css = $parser->getCSS();

### Less Code – Example.less

    a {
      display: flex;
    }

### Generated CSS

    a {
      display: -ms-flexbox;
      display: flex;
    }
