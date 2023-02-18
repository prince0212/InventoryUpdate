<?php
namespace Deloitte\InventoryUpdate\Block\Adminhtml\Inventoryexport;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Phrase;

/**
 * @api
 * @since 100.0.2
 */
class Edit extends Container
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->removeButton('back')->removeButton('reset')->removeButton('save');

        $this->_objectId = 'inventoryexport_id';
        $this->_blockGroup = 'Deloitte_InventoryUpdate';
        $this->_controller = 'adminhtml_inventoryexport';
    }

    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Export Inventory');
    }
}
