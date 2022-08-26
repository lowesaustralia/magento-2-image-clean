<?php

namespace Magecomp\Imageclean\Cron;

use Magecomp\Imageclean\Helper\Data as ImageCleanHelper;

/**
 * Class ScanImages
 * @package Magecomp\Imageclean\Cron
 */
class ScanImages
{
    private ImageCleanHelper $imageCleanHelper;

    /**
     * @param ImageCleanHelper $imageCleanHelper
     */
    public function __construct(
        ImageCleanHelper $imageCleanHelper
    )
    {
        $this->imageCleanHelper = $imageCleanHelper;
    }

    /**
     * Function execute
     */
    public function execute()
    {
        $this->imageCleanHelper->scanFolderPaths();
    }
}
