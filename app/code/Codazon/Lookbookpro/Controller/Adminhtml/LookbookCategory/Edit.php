<?php
/**
* Copyright © 2018 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\Lookbookpro\Controller\Adminhtml\LookbookCategory;

use Magento\Backend\App\Action;
use Magento\Framework\App\ObjectManager;

class Edit extends AbstractLookbookCategory
{
	/**
	* Core registry
	*
	* @var \Magento\Framework\Registry
	*/
	protected $_coreRegistry;
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;
	
	public function __construct(
		Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Registry $registry
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
		parent::__construct($context);
	}
    
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Codazon_Lookbookpro::cdzlookbook_category_edit');
    }
    
	protected function _initAction()
	{
		$resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Codazon_Lookbookpro::cdzlookbook_category');
		return $resultPage;
	}
    
	public function execute()
	{
		$id = $this->getRequest()->getParam($this->primary);
		$model = $this->_objectManager->create($this->modelClass);
		$storeId = $this->getRequest()->getParam('store', \Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $model->setData('store_id', $storeId);
		
        if (!$id) {
            $scopeCode = $storeId? $this->_objectManager->get('Magento\Store\Model\Store')->load($storeId)->getCode() : 'default';
            $scopeConfig = $this->_objectManager->get('Magento\Framework\App\Config');
            $id = $scopeConfig->getValue('codazon_lookbook/general/root_category', 'store', $scopeCode);
            if ($id) {
                $this->getRequest()->setParam($this->primary, $id);
            }
        }
        
        if ($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->messageManager->addError(__('This Lookbook Category no longer exists.'));
				/** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();
				return $resultRedirect->setPath('*/*/new');
			}
            
		} else {
            $parentId = (int)$this->getRequest()->getParam('parent');
            if ($parentId) {
                $model->setData('parent_id', $parentId);
                $model->setData('parent', $parentId);
            }
        }
	
		$data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
	
		$this->_coreRegistry->register('lookbookpro_cdzlookbook_category', $model);
	
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->_initAction();
		$resultPage->addBreadcrumb(
			__('Edit Lookbook Category'),
			__('Edit Lookbook Category')
		);
		$resultPage->getConfig()->getTitle()->prepend(__('Lookbook Category'));
		$resultPage->getConfig()->getTitle()
			->prepend($model->getId() ? $model->getData('name') : __('Edit Lookbook Category'));
        if (!$id) {
             $resultPage->getLayout()->unsetElement('store_switcher');
        }
		return $resultPage;
	}
}

