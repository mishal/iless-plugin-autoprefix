<?php

namespace ILess\Test\Plugin\Autoprefix;

use ILess\Cache\CacheInterface;
use ILess\Cache\FileSystemCache;
use ILess\Parser;
use ILess\Plugin\Autoprefix\AutoprefixPlugin;

class AutoprefixPluginTest extends \PHPUnit_Framework_TestCase
{
    protected function getParser(CacheInterface $cache = null, $pluginOptions = [])
    {
        $parser = new Parser([], $cache);

        $parser->getPluginManager()->addPlugin(new AutoprefixPlugin($pluginOptions));

        return $parser;
    }

    public function testPlugin()
    {
        $parser = $this->getParser();
        $parser->parseFile(__DIR__ . '/_fixtures/test.less');

        // FIXME: this is is going to fail in the future, update it!
        $expected = <<< EXPECTED
a {
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
}

EXPECTED;

        $css = $parser->getCSS();

        $this->assertEquals($expected, $css, 'Generated CSS is ok');
    }

    public function testOutdatedPrefixes()
    {
        $parser = $this->getParser(null, [
            'remove' => false
        ]);

        $parser->parseFile(__DIR__ . '/_fixtures/test_outdated.less');

        $expected = <<< EXPECTED
a {
  -moz-border-radius: 1px;
  border-radius: 1px;
}

EXPECTED;

        $css = $parser->getCSS();

        $this->assertEquals($expected, $css, 'Generated CSS is ok');
    }

}
