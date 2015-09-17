# ILess Plugin - Autoprefix

Autoprefixes the generated CSS using PostCSS autoprefixer plugin.

## Build Status

[![Build Status](https://travis-ci.org/mishal/iless-plugin-autoprefix.svg)](https://travis-ci.org/mishal/iless-plugin-autoprefix)

## Installation

Install using composer:

    $ composer require mishal/iless-plugin-autoprefix

Install requirements

    $ npm install postcss-cli autoprefixer

## Programmatic Usage

    use ILess\Parser;
    use ILess\Plugin\Autoprefix\AutoprefixPlugin;

    $parser = new Parser();
    // register the plugin
    $parser->getPluginManager()->addPlugin(new AutoprefixPlugin([
        // see https://github.com/ai/browserslist
        'browsers' => ['last 2 versions']
    ]));

    // now I can use schema like directives in my less
    $parser->parseFile('/example.less');

    $css = $parser->getCSS();

### Less Code – Example.less

    a {
      display: flex;
    }


### Generated CSS

    a {
      display: -webkit-flex;
      display: -ms-flexbox;
      display: flex;
    }

### Known limitations

*Source maps* support is not implemented in `postcss-cli`, see [the issue](https://github.com/code42day/postcss-cli/issues/3)
