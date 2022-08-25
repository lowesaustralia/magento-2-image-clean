<?php
namespace Magecomp\Imageclean\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Deletefolder extends Column
{
	const ROW_EDIT_URL = 'imageclean/folders/delete';

	protected $_urlBuilder;
    /**
     * @var string
     */
    private $_deleteUrl;

    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     * @param string             $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::ROW_EDIT_URL
    )
    {
        $this->_urlBuilder = $urlBuilder;
        $this->_deleteUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

	public function prepareDataSource(array $dataSource)
    {
		if (isset($dataSource['data']['items']))
		{
            foreach ($dataSource['data']['items'] as &$item)
			{
                $name = $this->getData('folder_path');
                if (isset($item['folder_id']))
				{
                    $item['actions']['delete'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            $this->_deleteUrl,
                            ['id' => $item['folder_id']]
                        ),
                        'label' => __('Delete'),
						'confirm' => [
                            'title' => __('Delete'),
                            'message' => __('Are you sure you wan\'t to delete this folder?')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
