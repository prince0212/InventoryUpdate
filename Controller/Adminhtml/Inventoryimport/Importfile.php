<?php

namespace Deloitte\InventoryUpdate\Controller\Adminhtml\Inventoryimport;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Importfile extends Action
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var UploaderFactory
     */
    private $_fileUploaderFactory;
    /**
     * @var SourceItemInterfaceFactory
     */
    private $sourceItemFactory;
    /**
     * @var SourceItemsSaveInterface
     */
    private $sourceItemsSaveInterface;

    /**
     * Importfile constructor.
     * @param Action\Context $context
     * @param Filesystem $filesystem
     * @param DirectoryList $directoryList
     * @param UploaderFactory $fileUploaderFactory
     * @param SourceItemsSaveInterface $sourceItemsSaveInterface
     * @param SourceItemInterfaceFactory $sourceItemFactory
     */
    public function __construct(
        Action\Context $context,
        Filesystem $filesystem,
        DirectoryList $directoryList,
        UploaderFactory $fileUploaderFactory,
        SourceItemsSaveInterface $sourceItemsSaveInterface,
        SourceItemInterfaceFactory $sourceItemFactory
    )
    {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->sourceItemFactory = $sourceItemFactory;
        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
    }

    /**
     * Index action.
     *
     * @return Page
     */
    public function execute()
    {
        $inventoryImportFile = $this->getRequest()->getFiles('importfile');

        $fileName = ($inventoryImportFile && array_key_exists('name', $inventoryImportFile)) ? $inventoryImportFile['name'] : null;
        if ($inventoryImportFile && $fileName) {
            try {
                $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                $target = $mediaDirectory->getAbsolutePath('');
                $uploader = $this->_fileUploaderFactory->create(['fileId' => 'importfile']);
                $uploader->setAllowedExtensions(['csv']);
                $uploader->setAllowRenameFiles(true);
                $result = $uploader->save($target);
                if ($result['file']) {
                    $count = 0;
                    $path = $this->directoryList->getPath('media') . "/" . $fileName;
                    $handle = fopen($path, "r");
                    if (empty($handle) === false) {
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            $count++;
                            if ($count === 1) {
                                continue;
                            }
                            $source = $data[0];
                            $sku = $data[1];
                            $inventory = (int)$data[2];
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($source);
                            $sourceItem->setSku($sku);
                            $sourceItem->setQuantity($inventory);
                            $sourceItem->setStatus(1);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        fclose($handle);
                    }
                    $this->messageManager->addSuccessMessage(__('Inventory has been imported successfully.'));
                }
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please upload the File.'));
        }
        $this->_redirect($this->_redirect->getRefererUrl());
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