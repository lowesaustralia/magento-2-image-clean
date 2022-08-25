<?php
namespace Magecomp\Imageclean\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Imagefolders extends AbstractDb {

    protected function _construct()
	{
        $this->_init('imageclean_folders', 'folder_id');
    }

}
