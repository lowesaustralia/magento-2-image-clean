<?php

namespace Magecomp\Imageclean\Helper;

use Magecomp\Imageclean\Model\ImagecleanFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DB\Exception;
use Magento\Framework\Filesystem\DirectoryList;

class Data extends AbstractHelper
{
    /**
     * @var ImagecleanFactory
     */
    protected $_modelImagecleanFactory;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var string
     */
    protected $mainPath = '';

    protected array $subFolders = [];

    /**
     * @param Context $context
     * @param ImagecleanFactory $modelImagecleanFactory
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context           $context,
        ImagecleanFactory $modelImagecleanFactory,
        DirectoryList     $directoryList
    )
    {
        $this->_modelImagecleanFactory = $modelImagecleanFactory;
        $this->directoryList = $directoryList;
        parent::__construct($context);
    }


    protected $result = [];
    protected $_mainTable;
    public $valdir = [];

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

    public function findSubFolders($dirPath = '')
    {
        if (is_dir($dirPath)) {
            if ($dir = opendir($dirPath)) {
                while (($entry = readdir($dir)) !== false) {
                    if (preg_match('/^\./', $entry) != 1 && !in_array($entry, ['cache', 'watermark', 'placeholder', 'sftp_imports'])) {
                        $subFolder = $dirPath . DIRECTORY_SEPARATOR . $entry;
                        if (is_dir($subFolder)) {
                            $this->subFolders[$subFolder] = ['folder_path' => $subFolder]; //$subFolder;
                            $this->findSubFolders($subFolder);
                        }
                    }
                }
                closedir($dir);
            }
        }
        return $this->subFolders;
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
            $dirPath = $this->directoryList->getPath('pub') . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'product';
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
            } catch (\Exception $e) {
                $om = \Magento\Framework\App\ObjectManager::getInstance();
                $storeManager = $om->get('Psr\Log\LoggerInterface');
                $storeManager->info($e->getMessage());
            }
        }
    }

}
