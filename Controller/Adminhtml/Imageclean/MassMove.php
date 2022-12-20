<?php

namespace Magecomp\Imageclean\Controller\Adminhtml\Imageclean;

use Magecomp\Imageclean\Helper\Data as ImageCleanHelper;
use Magecomp\Imageclean\Model\ImagecleanFactory;
use Magecomp\Imageclean\Model\ResourceModel\Imageclean\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;

class MassMove extends \Magento\Backend\App\Action
{
    protected CollectionFactory $collectionFactory;
    protected DirectoryList $directoryList;
    protected Filter $filter;
    protected ImagecleanFactory $_modelImagecleanFactory;
    protected ImageCleanHelper $imageCleanHelper;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Context $context
     * @param DirectoryList $directoryList
     * @param Filter $filter
     * @param ImagecleanFactory $modelImagecleanFactory
     * @param ImageCleanHelper $imageCleanHelper
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Context           $context,
        DirectoryList     $directoryList,
        Filter            $filter,
        ImagecleanFactory $modelImagecleanFactory,
        ImageCleanHelper  $imageCleanHelper
    )
    {
        $this->_modelImagecleanFactory = $modelImagecleanFactory;
        $this->collectionFactory = $collectionFactory;
        $this->directoryList = $directoryList;
        $this->filter = $filter;
        $this->imageCleanHelper = $imageCleanHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        $mediaPath = $this->imageCleanHelper->getProductImagesPath();
        $i = 0;
        foreach ($collection as $item) {
            try {
                $model = $this->_modelImagecleanFactory->create();
                $model->load($item->getImagecleanId());
                $filename = $model->getFilename();
                $item->delete();

                $oldPath = $mediaPath . $filename;
                $newPath = dirname($mediaPath) . DIRECTORY_SEPARATOR . 'tmp' . $filename;
                $this->imageCleanHelper->renameFile($oldPath, $newPath);
            } catch (\Exception $e) {
                if (!$i++) {
                    $this->messageManager->addError($e->getMessage());
                }
                $this->imageCleanHelper->getLogger()->error($e);
            }
        }

        $this->messageManager->addSuccess(__('A total of %1 image(s) have been moved.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/index');
    }
}