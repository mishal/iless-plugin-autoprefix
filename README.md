# ILess Plugin - Autoprefix

Autoprefixes the generated CSS

## Build Status

[![Build Status](https://travis-ci.org/mishal/iless-plugin-autoprefix.svg)](https://travis-ci.org/mishal/iless-plugin-autoprefix)

## Installation

Install using composer:

    $ composer require mishal/iless-plugin-autoprefix

## Programmatic Usage

    use ILess\Parser;
    use ILess\Plugin\Autoprefix\AutoprefixPlugin;

    $parser = new Parser();
    // register the plugin
    $parser->getPluginManager()->addPlugin(new AutoprefixPlugin($directories));

    // now I can use schema like directives in my less
    $parser->parseFile('/example.less');

    $css = $parser->getCSS();

### Less Code – Example.less

    // to do
