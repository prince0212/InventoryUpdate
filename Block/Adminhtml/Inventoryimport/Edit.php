<?php

namespace Deloitte\InventoryUpdate\Block\Adminhtml\Inventoryimport;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Phrase;

/**
 * @api
 * @since 100.0.2
 */
class Edit extends Container
{
    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Import Inventory');
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->removeButton('back')->removeButton('reset')->removeButton('save');

        $this->_objectId = 'inventoryimport_id';
        $this->_blockGroup = 'Deloitte_InventoryUpdate';
        $this->_controller = 'adminhtml_inventoryimport';
    }
}
