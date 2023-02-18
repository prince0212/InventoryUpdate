<?php

namespace Deloitte\InventoryUpdate\Controller\Adminhtml\Inventoryexport;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\Framework\App\Response\Http\FileFactory;

class Exportfile extends Action
{
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var GetSourceItemsBySkuInterface
     */
    private $sourceItemsBySku;
    /**
     * @var FileFactory
     */
    private $_downloader;

    /**
     * Exportfile constructor.
     * @param Action\Context $context
     * @param Filesystem $filesystem
     * @param DirectoryList $directoryList
     * @param CollectionFactory $collectionFactory
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @throws FileSystemException
     */
    public function __construct(
        Action\Context $context,
        DirectoryList $directoryList,
        CollectionFactory $collectionFactory,
        FileFactory $fileFactory,
        GetSourceItemsBySkuInterface $sourceItemsBySku
    )
    {
        parent::__construct($context);
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->_downloader =  $fileFactory;
        $this->directoryList = $directoryList;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Index action.
     * @throws Exception
     */
    public function execute()
    {
        $fileDirectoryPath = $this->directoryList->getPath(DirectoryList::MEDIA);
        $fileName = 'inventory_'.date('YmdHis').'.csv';
        $filepath = $fileDirectoryPath . '/' . $fileName;
        $fp = fopen($filepath, 'w+');
        $columns = $this->getColumnHeader();
        $header = [];
        foreach ($columns as $column) :
            $header[] = $column;
        endforeach;

        try {
            $this->putCsvHeaders($fp, $header, ',', '"');
            fwrite($fp, "\r\n");
            $collection = $this->collectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter('type_id', Type::TYPE_SIMPLE);
            $data = [];
            foreach ($collection as $products) {
                $sourceItemList = $this->sourceItemsBySku->execute($products->getSku());
                foreach ($sourceItemList as $source) {
                    $data['source'] = $source->getSourceCode();
                    $data['sku'] = $source->getSku();
                    $data['available_inventory_stock'] = $source->getQuantity();
                    $this->putCsvHeaders($fp, $data, ',', '"');
                    fwrite($fp, "\r\n");
                }
            }
        } catch (Exception $exception) {
        }
        fclose($fp);
        $content = [];
        $content['type'] = 'filename';
        $content['value'] = DirectoryList::MEDIA."/".$fileName;
        $content['rm'] = '1'; //remove csv from var folder
        return $this->_downloader->create($fileName, $content, DirectoryList::PUB);

        /*return $this->_downloader->create(
            $fileName,
            @file_get_contents($filepath)
        );*/
    }

    /**
     * @return string[]
     */
    public function getColumnHeader()
    {
        return ['source', 'sku', 'available_inventory_stock'];
    }

    /**
     * @param $handle
     * @param $headers
     * @param $separator
     * @param $enclosure
     */
    public function putCsvHeaders($handle, $headers, $separator, $enclosure): void
    {
        array_walk($headers, function (&$item, $i, $enc): void {
            $item = $enc . $item . $enc; // wrap the header with the enclosure
        }, $enclosure);
        fputs($handle, implode($separator, $headers));
    }
}

?>