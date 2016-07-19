<?php

/*
 * This file is part of the ILess Autoprefix Plugin
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Plugin\Autoprefix;

use ILess\Configurable;
use ILess\Plugin\PostProcessorInterface;
use ILess\SourceMap\Generator;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * AutoprefixProcessor
 *
 * @package ILess\Plugin\Autoprefix
 */
class AutoprefixProcessor extends Configurable implements PostProcessorInterface
{
    /**
     * Default options
     *
     * @var array
     */
    protected $defaultOptions = [
        'postcss_bin' => '/usr/bin/postcss'
    ];

    /**
     * @inheritdoc
     */
    public function process($css, array $extra)
    {
        return $this->doAutoprefixing($css, $extra);
    }

    /**
     * Does the auto prefixing work
     *
     * @param string $css
     * @param array $extra
     * @return string
     */
    private function doAutoprefixing($css, array $extra)
    {
        $pb = new ProcessBuilder([$this->getOption('postcss_bin')]);

        $pb->add('--use')->add('autoprefixer');
        $pb->add('--replace');
        $json = $this->prepareJsonConfig($extra);
        $pb->add('-c')->add($json);
        $pb->add($output = $this->prepareInput($css));

        $process = $pb->getProcess();
        if (0 !== $process->run()) {
            throw new ProcessFailedException($process);
        }

        // we need to fix source map
        if ($extra['source_map']) {
            $this->fixSourceMap($extra['source_map'], $output);
        }

        $out = file_get_contents($output);

        // cleanup
        unlink($json);
        unlink($output);

        return $out;
    }

    private function prepareInput($css)
    {
        $tmp = tempnam(sys_get_temp_dir(), 'iless_autoprefix');
        file_put_contents($tmp, $css);

        return $tmp;
    }

    private function fixSourceMap(Generator $sourceMap, $outputFile)
    {
        $writeTo = $sourceMap->getOption('write_to');
        if (!$writeTo) {
            // this is inline map
            return;
        }
        $map = $outputFile . '.map';

        // overwrite the map
        if (is_readable($map)) {
            // autoprefixer puts unreferenced source, so we will "remove" it
            $mapContent = file_get_contents($map);
            $mapContent = json_decode($mapContent, true);
            $file = basename($outputFile);
            foreach ($mapContent['sources'] as $index => $source) {
                if ($source == $file) {
                    $mapContent['sources'][$index] = 'zzzzzoo_ignore_this';
                    $mapContent['sourcesContent'][$index] = '/*autoprefixer adds unreferenced source, just try to ignore this :)*/';
                    break;
                }
            }

            $mapContent = json_encode($mapContent);
            file_put_contents($writeTo, $mapContent);
            unlink($map);
        }
    }

    /**
     * @return string
     */
    private function prepareJsonConfig(array $extra)
    {
        $tmp = tempnam(sys_get_temp_dir(), 'iless_autoprefix');
        $json = $tmp . '.json';

        // the cli does not like tmp extension, rename it to json so its happy :)
        rename($tmp, $json);

        $options = [];
        $options['autoprefixer'] = $this->getOptionsForAutoprefixer();

        if ($extra['source_map']) {
            //@var Generator $sourceMap
            $sourceMap = $extra['source_map'];
            $fixed = null;
            if ($sourceMap->getOption('write_to')) {
                $fixed = $this->fixPreviousMap(file_get_contents($sourceMap->getOption('write_to')));
            }
            $options['map'] = [
                'prev' => $fixed ? json_encode($fixed) : null,
                'inline' => $sourceMap->getOption('write_to') === null,
                'annotation' => $sourceMap->getOption('url'),
                'sourcesContent' => true,
            ];
        }

        file_put_contents($json, json_encode($options));

        return $json;
    }

    private function fixPreviousMap($string)
    {
        $json = json_decode($string, true);

        $json['sources'] = array_map(function ($source) {
            $fixed = str_replace('/', DIRECTORY_SEPARATOR, $source);
            return $fixed;
        }, $json['sources']);

        return $json;
    }

    /**
     * @return array
     */
    private function getOptionsForAutoprefixer()
    {
        $options = $this->getOptions();

        unset($options['postcss_bin']);

        return $options;
    }

}
