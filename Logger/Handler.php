<?php

namespace Magecomp\Imageclean\Logger;

/**
 * Class Handler
 * @package Magecomp\Imageclean\Logger
 */
class Handler extends \Magento\Framework\Logger\Handler\Base
{

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/imageclean.log';

    /**
     * @param string $filename
     * @return $this
     */
    public function setFileName($filename = 'imageclean.log'): self
    {
        $this->fileName = '/var/log/' . $filename;

        return $this;
    }
}
