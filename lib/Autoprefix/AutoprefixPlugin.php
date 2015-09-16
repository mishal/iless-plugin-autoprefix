<?php

/*
 * This file is part of the ILess Autoprefix Plugin
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Plugin\Autoprefix;

use ILess\Parser;
use ILess\Plugin\Plugin;

/**
 * Autoprefix plugin
 *
 * @package ILess\Plugin\Autoprefix
 */
class AutoprefixPlugin extends Plugin
{
    /**
     * Default options
     *
     * @var array
     */
    protected $defaultOptions = [
        'browsers' => ['last 2 versions'],
        'remove' => true,
        'add' => true
    ];

    /**
     * @inheritdoc
     */
    public function install(Parser $parser)
    {
        $parser->getPluginManager()->addPostProcessor(new AutoprefixProcessor($this->getOptions()));
    }

}
