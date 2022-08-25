<?php declare(strict_types=1);

namespace Magecomp\Imageclean\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Imagefolders extends AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'imageclean_folders_id';

    protected DateTime $dateTime;
    protected ResourceConnection $resource;
    protected AdapterInterface $connection;


    public function __construct(
        DateTime                                                $dateTime,
        ResourceConnection                                      $resourceConnection,
        \Magento\Framework\Model\Context                        $context,
        \Magento\Framework\Registry                             $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection = null,
        array                                                   $data = []
    )
    {
        $this->dateTime = $dateTime;
        $this->resource = $resourceConnection;
        $this->connection = $resourceConnection->getConnection();

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magecomp\Imageclean\Model\ResourceModel\Imagefolders');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param $folders
     * @return $this
     */
    public function addFolders($folders = [])
    {
        if (!empty($folders)) {
            $table = $this->resource->getTableName('imageclean_folders');
            $this->connection->insertOnDuplicate($table, $folders);
        }
        return $this;
    }
}
