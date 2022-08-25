<?php

namespace Magecomp\Imageclean\Logger;

/**
 * Class Logger
 * @package Magecomp\Imageclean\Logger
 */
class Logger extends \Monolog\Logger
{
    /**
     * Logger constructor.
     * @param string $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct($name = "logger", $handlers = array(), $processors = array())
    {
        parent::__construct($name, $handlers, $processors);
    }
}
