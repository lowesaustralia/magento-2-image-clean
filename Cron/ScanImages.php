<?php

namespace Magecomp\Imageclean\Cron;

use Magecomp\Imageclean\Helper\Data as ImageCleanHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;

/**
 * Class ScanImages
 * @package Magecomp\Imageclean\Cron
 */
class ScanImages
{
    private ImageCleanHelper $imageCleanHelper;
    private Filesystem $filesystem;
    private DirectoryList $directoryList;

    /**
     * @param ImageCleanHelper $imageCleanHelper
     * @param Filesystem $filesystem
     * @param DirectoryList $directoryList
     */
    public function __construct(
        ImageCleanHelper $imageCleanHelper,
        Filesystem $filesystem,
        DirectoryList $directoryList
    )
    {
        $this->imageCleanHelper = $imageCleanHelper;
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
    }

    /**
     * Function execute
     */
    public function execute()
    {
        try {
            $mediaCatalogProductPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(). DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'product';
            $subFolders = $this->imageCleanHelper->findSubFolders($mediaCatalogProductPath);

        } catch (\Exception|\LocalizedException $e) {

        }
    }
}
