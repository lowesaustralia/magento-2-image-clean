<?php

namespace Magecomp\Imageclean\Cron;

use Magecomp\Imageclean\Helper\Data as ImageCleanHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magecomp\Imageclean\Model\Imagefolders;

/**
 * Class ScanImages
 * @package Magecomp\Imageclean\Cron
 */
class ScanFolderPaths
{
    private ImageCleanHelper $imageCleanHelper;
    private Filesystem $filesystem;
    private DirectoryList $directoryList;
    private Imagefolders $imageFoldersModel;

    /**
     * @param ImageCleanHelper $imageCleanHelper
     * @param Filesystem $filesystem
     * @param DirectoryList $directoryList
     */
    public function __construct(
        ImageCleanHelper    $imageCleanHelper,
        Filesystem          $filesystem,
        DirectoryList       $directoryList,
        Imagefolders $imageFoldersModel
    )
    {
        $this->imageCleanHelper = $imageCleanHelper;
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->imageFoldersModel = $imageFoldersModel;
    }

    /**
     * Function execute
     */
    public function execute()
    {
        try {
            $this->imageCleanHelper->getLogger()->info(__('cron %s started.', ['s' => __METHOD__]));

            $mediaCatalogProductPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'catalog' . DIRECTORY_SEPARATOR . 'product';
            $subFolders = $this->imageCleanHelper->findSubFolders($mediaCatalogProductPath);
            $this->imageFoldersModel->addFolders($subFolders);
        } catch (\Exception|\LocalizedException $e) {
            $this->imageCleanHelper->getLogger()->error($e);
        }
        $this->imageCleanHelper->getLogger()->info(__('cron %s finished.', ['s' => __METHOD__]));
    }
}
