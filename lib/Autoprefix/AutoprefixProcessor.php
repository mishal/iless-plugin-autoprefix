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
        'postcss_bin' => 'postcss'
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

        $pb->setInput($css);
        $pb->add('--use')->add('autoprefixer');

        $json = $this->prepareJsonConfig();
        $pb->add('-c')->add($json);

        $process = $pb->getProcess();

        echo $process->getCommandLine() . "\n";

        if (0 !== $process->run()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        // cleanup
        unlink($json);

        return $output;
    }

    /**
     * @return string
     */
    private function prepareJsonConfig()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'iless_autoprefix');
        $json = $tmp . '.json';
        // the cli does not like tmp extension, rename it to json so its happy :)
        rename($tmp, $json);

        file_put_contents($json, json_encode([
            'autoprefixer' => $this->getOptionsForAutoprefixer()
        ]));

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
