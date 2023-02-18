<?php
namespace Deloitte\InventoryUpdate\Block\Adminhtml\Inventoryimport\Edit;

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
                    'action' => $this->getUrl('adminhtml/*/Importfile'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Upload the CSV file and click on Import File button to update the inventory in the system.')]);

        $fieldset->addField(
            'importfile',
            'file',
            [
                'name' => 'importfile',
                'label' => __('File: '),
                'title' => __('File'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'submit',
            'note',
            [
                'text' => $this->getLayout()->createBlock(
                    Button::class
                )->setData(
                    ['label' => __('Import File'), 'onclick' => 'this.form.submit();', 'class' => 'add']
                )->toHtml()
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
