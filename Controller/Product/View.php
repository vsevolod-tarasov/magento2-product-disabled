<?php
namespace MediaLounge\DisabledProductRedirect\Controller\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class View
 * @package MediaLounge\DisabledProductRedirect\Controller\Product
 */
class View extends \Magento\Catalog\Controller\Product\View
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * View constructor.
     * @param \Magento\Framework\Registry $registry
     * @param Context $context
     * @param \Magento\Catalog\Helper\Product\View $viewHelper
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        Context $context,
        \Magento\Catalog\Helper\Product\View $viewHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory
    ) {
        $this->registry = $registry;
        parent::__construct($context, $viewHelper, $resultForwardFactory, $resultPageFactory);
    }

    /**
     * Product view action
     *
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $product = $this->_initProduct();
        if (!$product) {
            if ($category = $this->registry->registry('redirect_to_category')) {
                $this->messageManager->addNotice(
                    __('The product you tried to view is not available but here are some other options instead.')
                );
                if (!$this->getResponse()->isRedirect()) {
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setUrl($category->getUrl());
                }
            }

            return $this->noProductRedirect();
        } else {
            $this->registry->unregister('current_product');
            $this->registry->unregister('product');
            return parent::execute();
        }
    }
}
