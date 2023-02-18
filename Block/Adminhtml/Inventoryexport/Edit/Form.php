<?php
namespace Deloitte\InventoryUpdate\Block\Adminhtml\Inventoryexport\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\ImportExport\Model\Source\Export\EntityFactory;
use Magento\ImportExport\Model\Source\Export\FormatFactory;

class Form extends Generic
{
    /**
     * @var EntityFactory
     */
    protected $_entityFactory;

    /**
     * @var FormatFactory
     */
    protected $_formatFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param EntityFactory $entityFactory
     * @param FormatFactory $formatFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        EntityFactory $entityFactory,
        FormatFactory $formatFactory,
        array $data = []
    )
    {
        $this->_entityFactory = $entityFactory;
        $this->_formatFactory = $formatFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form before rendering HTML.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('adminhtml/*/Exportfile'),
                    'method' => 'post',
                ],
            ]
        );

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Click on below button to Export the inventory and stock information.')]);
        $fieldset->addField(
            'submit',
            'note',
            [
                'text' => $this->getLayout()->createBlock(
                    Button::class
                )->setData(
                    ['label' => __('Export File'), 'onclick' => 'this.form.submit();', 'class' => 'add']
                )->toHtml()
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
