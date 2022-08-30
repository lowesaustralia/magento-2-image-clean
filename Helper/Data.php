<?php

namespace Magecomp\Imageclean\Helper;

use Magecomp\Imageclean\Logger\Logger;
use Magecomp\Imageclean\Model\Imageclean;
use Magecomp\Imageclean\Model\ImagecleanFactory;
use Magecomp\Imageclean\Model\ResourceModel\Imagefolders\CollectionFactory as ImageFoldersCollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DB\Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Data extends AbstractHelper
{
    const XML_PATH_DEFAULT_MAGECOMP_IMAGE_CLEAN_MEDIA_PATH = 'magecomp/imageclean/media_path';

    protected array $subFolders = [];
    protected DateTime $dateTime;
    protected DirectoryList $directoryList;
    protected File $file;
    protected Filesystem $filesystem;
    protected Imageclean $imagecleanModel;
    protected ImagecleanFactory $_modelImagecleanFactory;
    protected ImageFoldersCollectionFactory $imageFoldersCollectionFactory;
    protected Logger $logger;

    protected $_mainTable;
    protected $mainPath = '';
    protected $productImagesPath = '';
    protected $result = [];
    public $valdir = [];

    /**
     * @param Context $context
     * @param DateTime $dateTime
     * @param DirectoryList $directoryList
     * @param File $file
     * @param Filesystem $filesystem
     * @param Imageclean $imagecleanModel
     * @param ImagecleanFactory $modelImagecleanFactory
     * @param ImageFoldersCollectionFactory $imageFoldersCollectionFactory
     * @param Logger $logger
     */
    public function __construct(
        Context                       $context,
        DateTime                      $dateTime,
        DirectoryList                 $directoryList,
        File                          $file,
        Filesystem                    $filesystem,
        Imageclean                    $imagecleanModel,
        ImagecleanFactory             $modelImagecleanFactory,
        ImageFoldersCollectionFactory $imageFoldersCollectionFactory,
        Logger                        $logger
    )
    {
        $this->_modelImagecleanFactory = $modelImagecleanFactory;
        $this->dateTime = $dateTime;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->filesystem = $filesystem;
        $this->imagecleanModel = $imagecleanModel;
        $this->imageFoldersCollectionFactory = $imageFoldersCollectionFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return mixed
     */
    public function getProductImagesPath()
    {
        if (empty($this->productImagesPath)) {
            $this->productImagesPath = $this->getSystemConfig(self::XML_PATH_DEFAULT_MAGECOMP_IMAGE_CLEAN_MEDIA_PATH);
        }
        return $this->productImagesPath;
    }

    /**
     * @param $path
     * @param $scopeCode
     * @return mixed
     */
    public function getSystemConfig($path = '', $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, 'store', $scopeCode);
    }

    public function findSubFolders($dirPath = '')
    {
        if (!empty($dirPath) && is_dir($dirPath)) {
            if ($handle = opendir($dirPath)) {
                while (($entry = readdir($handle)) !== false) {
                    if (preg_match('/^\./', $entry) != 1 && !in_array($entry, ['cache', 'watermark', 'placeholder', 'sftp_imports'])) {
                        $subFolder = $dirPath . DIRECTORY_SEPARATOR . $entry;
                        if (is_dir($subFolder)) {
                            $this->logger->debug($subFolder);

                            $this->subFolders[$subFolder] = ['folder_path' => $subFolder]; //$subFolder;
                            $this->findSubFolders($subFolder);
                        }
                    }
                }
                closedir($handle);
            }
        }
        return $this->subFolders;
    }

    /**
     * @param $path
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function scanImages($path)
    {
        $images = [];
        if (is_dir($path)) {
            if ($handle = opendir($path)) {
                while (($entry = readdir($handle)) !== false) {
                    if (preg_match('/^\./', $entry) != 1) {
                        if ($this->file->isFile($path . DIRECTORY_SEPARATOR . $entry)) {
                            $images[] = str_replace($this->getProductImagesPath(), '', $path . DIRECTORY_SEPARATOR . $entry);
                        }
                    }
                }
                closedir($handle);
            }
        }
        return $images;
    }

    /**
     * @return $this
     */
    public function scanFolderPaths()
    {
        /** @var \Magecomp\Imageclean\Model\ResourceModel\Imagefolders\Collection $collection */
        $collection = $this->imageFoldersCollectionFactory->create();
        $collection->addFieldToFilter('scaned', 0)
            ->setOrder('folder_id');

        /** @var \Magecomp\Imageclean\Model\Imagefolders $folder */
        foreach ($collection as $folder) {
            try {

                $path = $folder->getData('folder_path');

                $this->findUnusedImages($path);

                $folder->setData('scaned', 1);
                $folder->setData('last_scan_date', $this->dateTime->date());

                $folder->save();

            } catch (\Exception|LocalizedException $e) {
                $this->getLogger()->error($e);
            }
        }

        return $this;
    }

    public function findUnusedImages($path)
    {
        $imagesToCheck = $this->scanImages($path);
        $usedImages = $this->imagecleanModel->getUsedProductImages();

        $unUsedImages = [];
        foreach ($imagesToCheck as $imageToCheck) {
            try {
                $imageToCheck = strtr($imageToCheck, '\\', '/');

                if (!in_array($imageToCheck, $usedImages)) {
                    $unUsedImages[] = ['filename' => $imageToCheck];
                }
            } catch (\Exception|LocalizedException $e) {
                $this->getLogger()->error($e);
            }
        }

        if (!empty($unUsedImages)) {
            try {
                $this->imagecleanModel->saveUnusedImagesTable($unUsedImages);
            } catch (\Exception|LocalizedException $e) {
                $this->getLogger()->error($e);
            }
        }
    }

    public function listDirectories($path)
    {
        if ($this->mainPath == '') {
            $this->mainPath = $path;
        }
        if (is_dir($path)) {
            if ($dir = opendir($path)) {
                while (($entry = readdir($dir)) !== false) {
                    if (preg_match('/^\./', $entry) != 1) {
                        if (is_dir($path . DIRECTORY_SEPARATOR . $entry) && !in_array($entry, ['cache', 'watermark', 'placeholder', 'sftp_imports'])) {
                            $this->listDirectories($path . DIRECTORY_SEPARATOR . $entry);
                        } elseif (!in_array($entry, ['cache', 'watermark', 'sftp_imports']) && (strpos($entry, '.') != 0)) {
                            //$this->result[] = substr($path.DIRECTORY_SEPARATOR.$entry,25);
                            $this->result[] = str_replace($this->mainPath, '', $path . DIRECTORY_SEPARATOR . $entry);
                        }
                    }
                }
                closedir($dir);
            }
        }
        return $this->result;
    }

    /**
     * @param $dirPath
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function compareList($dirPath = '')
    {
        $valores = $this->_modelImagecleanFactory->create()->getCollection()->getImages();
        if (empty($dirPath)) {
            $dirPath = $this->getProductImagesPath();
        }
        $leer = $this->listDirectories($dirPath);
        $model = $this->_modelImagecleanFactory->create();
        foreach ($leer as $item) {
            try {
                $item = strtr($item, '\\', '/');

                if (!in_array($item, $valores)) {
                    $valdir[]['filename'] = $item;
                    $model->setData(['filename' => $item])->setId(null);
                    $model->save();
                }
            } catch (\Exception|LocalizedException $e) {
                $this->getLogger()->info($e->getMessage());
            }
        }
    }

    /**
     * @param string $oldPath
     * @param string $newPath
     * @return bool
     * @throws \Exception
     */
    public function renameFile($oldPath, $newPath)
    {
        if (empty($oldPath) || empty($newPath)) {
            return false;
        }

        try {
            $newDirPath = dirname($newPath);

            if ($this->file->isExists($newDirPath)) {
                $this->file->createDirectory($newDirPath);
            }

            if (!$this->file->isDirectory($newDirPath)) {
                $this->file->createDirectory($newDirPath);
            }

            rename($oldPath, $newPath);
        } catch (\Exception|\Magento\Framework\Exception\FileSystemException $e) {
            throw new \Exception($e->getMessage());
        }

        return true;
    }
}
