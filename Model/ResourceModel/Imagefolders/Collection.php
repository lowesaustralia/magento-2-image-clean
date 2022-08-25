<?php
namespace Magecomp\Imageclean\Model\ResourceModel\Imagefolders;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'folder_id';

    public function _construct()
    {
        $this->_init('Magecomp\Imageclean\Model\Imagefolders','Magecomp\Imageclean\Model\ResourceModel\Imagefolders');
    }
}
