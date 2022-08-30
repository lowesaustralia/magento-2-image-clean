<?php

namespace Magecomp\Imageclean\Controller\Adminhtml\Imageclean;

use Magecomp\Imageclean\Helper\Data as ImageCleanHelper;
use Magecomp\Imageclean\Model\ImagecleanFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Filesystem\DirectoryList;

class Move extends AbstractImageclean
{
    protected DirectoryList $directoryList;
    protected ImagecleanFactory $_modelImagecleanFactory;
    protected ImageCleanHelper $imageCleanHelper;

    /**
     * @param Context $context
     * @param DirectoryList $directoryList
     * @param ImagecleanFactory $modelImagecleanFactory
     * @param ImageCleanHelper $imageCleanHelper
     */
    public function __construct(
        Context           $context,
        DirectoryList     $directoryList,
        ImagecleanFactory $modelImagecleanFactory,
        ImageCleanHelper  $imageCleanHelper
    )
    {
        $this->_modelImagecleanFactory = $modelImagecleanFactory;
        $this->directoryList = $directoryList;
        $this->imageCleanHelper = $imageCleanHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $mediaPath = $this->imageCleanHelper->getProductImagesPath();
                $model = $this->_modelImagecleanFactory->create();
                $model->load($this->getRequest()->getParam('id'));
                $filename = $model->getFilename();
                $model->setId($this->getRequest()->getParam('id'))->delete();
                $oldPath = $mediaPath . $filename;
                $newPath = dirname($mediaPath) . DIRECTORY_SEPARATOR . 'tmp' . $filename;
                $this->imageCleanHelper->renameFile($oldPath, $newPath);
                $this->messageManager->addSuccess(__('Image was successfully moved'));

                $this->_redirect('*/*/');
            } catch (\Exception $e) {
                // $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        $this->_redirect('*/*/');
    }
}
