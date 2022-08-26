<?php

namespace Magecomp\Imageclean\Model;

use Magento\Framework\Model\AbstractModel;

class Imageclean extends AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{

    const CACHE_TAG = 'iimageclean_id';

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magecomp\Imageclean\Model\ResourceModel\Imageclean');
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     * @throws \Zend_Db_Statement_Exception
     */
    public function getUsedProductImages()
    {
        $images = [];
        $table = $this->getResourceCollection()->getTable('catalog_product_entity_media_gallery');
        $query = "SELECT distinct value FROM $table";

        $stmt = $this->getResourceCollection()->getConnection()->query($query);
        while ($row = $stmt->fetch()) {
            $images[] = $row['value'];
        }
        return $images;
    }

    /**
     * @param $images
     * @return $this
     */
    public function saveUnusedImagesTable($images = [])
    {
        $table = $this->getResourceCollection()->getTable('imageclean');
        $this->getResourceCollection()->getConnection()->insertOnDuplicate($table, $images);

        return $this;
    }
}
