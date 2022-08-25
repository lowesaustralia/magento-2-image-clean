<?php

namespace Magecomp\Imageclean\Controller\Adminhtml\Folders;

use Magento\Backend\App\Action\Context;

class Delete extends \Magento\Backend\App\Action
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function execute() {
        if ($this->getRequest()->getParam('id') > 0)
        {
            // not yet done
        }
        $this->_redirect('*/*/');
    }
}
