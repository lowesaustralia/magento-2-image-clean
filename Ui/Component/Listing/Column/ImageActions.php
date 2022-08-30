<?php
namespace Magecomp\Imageclean\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class ImageActions extends Column
{
	const ROW_DELETE_URL = 'imageclean/imageclean/delete';
	const ROW_MOVE_URL = 'imageclean/imageclean/move';

	protected $_urlBuilder;
    /**
     * @var string
     */
    private $_editUrl;

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
        $editUrl = self::ROW_DELETE_URL
    )
    {
        $this->_urlBuilder = $urlBuilder;
        $this->_editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

	public function prepareDataSource(array $dataSource)
    {
		if (isset($dataSource['data']['items']))
		{
            foreach ($dataSource['data']['items'] as &$item)
			{
                $name = $this->getData('filename');
                if (isset($item['imageclean_id']))
				{
                    $item['actions']['delete'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            self::ROW_DELETE_URL,
                            ['id' => $item['imageclean_id']]
                        ),
                        'label' => __('Delete'),
						'confirm' => [
                            'title' => __('Delete'),
                            'message' => __('Are you sure you wan\'t to delete this Image?')
                        ]
                    ];

                    $item['actions']['move'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            self::ROW_MOVE_URL,
                            ['id' => $item['imageclean_id']]
                        ),
                        'label' => __('Move'),
                        'confirm' => [
                            'title' => __('Move'),
                            'message' => __('Are you sure you want to move this Image?')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
