<?php

namespace Magecomp\Imageclean\Cron;

use Magecomp\Imageclean\Helper\Data as ImageCleanHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magecomp\Imageclean\Model\ImagefoldersFactory as ImageFoldersFactory;

/**
 * Class ScanImages
 * @package Magecomp\Imageclean\Cron
 */
class ScanFolderPaths
{
    private ImageCleanHelper $imageCleanHelper;
    private Filesystem $filesystem;
    private DirectoryList $directoryList;
    private ImageFoldersFactory $imageFoldersFactory;

    /**
     * @param ImageCleanHelper $imageCleanHelper
     * @param Filesystem $filesystem
     * @param DirectoryList $directoryList
     */
    public function __construct(
        ImageCleanHelper $imageCleanHelper,
        Filesystem $filesystem,
        DirectoryList $directoryList,
        ImageFoldersFactory $imageFoldersFactory
    )
    {
        $this->imageCleanHelper = $imageCleanHelper;
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->imageFoldersFactory = $imageFoldersFactory;
    }

    /**
     * Function execute
     */
    public function execute()
    {
        try {
            $mediaCatalogProductPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(). DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'product';
            $subFolders = $this->imageCleanHelper->findSubFolders($mediaCatalogProductPath);
            $this->imageFoldersFactory->addFolders($subFolders);
        } catch (\Exception|\LocalizedException $e) {

        }
    }
}
