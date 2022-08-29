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
    private Imagefolders $imageFoldersModel;

    /**
     * @param ImageCleanHelper $imageCleanHelper
     * @param Imagefolders $imageFoldersModel
     */
    public function __construct(
        ImageCleanHelper $imageCleanHelper,
        Imagefolders     $imageFoldersModel
    )
    {
        $this->imageCleanHelper = $imageCleanHelper;
        $this->imageFoldersModel = $imageFoldersModel;
    }

    /**
     * Function execute
     */
    public function execute()
    {
        try {
            $this->imageCleanHelper->getLogger()->info(__('ScanFolderPaths cron %s started.', ['s' => __METHOD__]));

            $mediaCatalogProductPath = $this->imageCleanHelper->getProductImagesPath();
            $subFolders = $this->imageCleanHelper->findSubFolders($mediaCatalogProductPath);

            if (!empty($subFolders)) {
                $this->imageFoldersModel->addFolders($subFolders);
            }
        } catch (\Exception|\LocalizedException $e) {
            $this->imageCleanHelper->getLogger()->error($e);
        }
        $this->imageCleanHelper->getLogger()->info(__('ScanFolderPaths cron %s finished.', ['s' => __METHOD__]));
    }
}
