<?php
namespace MediaLounge\DisabledProductRedirect\Plugin;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Class ProductHelper
 * @package MediaLounge\DisabledProductRedirect\Plugin
 */
class ProductHelper
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * ProductHelper constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Checks if the product is disabled and assigned to category, if so
     * - then the category is loaded and added to registry
     * @param $subject
     * @param callable $proceed
     * @param $product
     * @param string $where
     * @return mixed
     */
    public function aroundCanShow($subject, callable $proceed, $product, $where = 'catalog')
    {
        $result = $proceed($product, $where);
        if (!$result) {
            if (!is_int($product) && $product->getId() && ($product->getStatus() == Status::STATUS_DISABLED)) {
                $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
                foreach ($product->getCategoryIds() as $categoryId) {
                    try {
                        $category = $this->categoryRepository->get($categoryId);
                        if ($category->getIsActive() && ($category->getId() != $rootCategoryId)) {
                            if ($this->registry->registry('redirect_to_category')) {
                                $this->registry->unregister('redirect_to_category');
                            }
                            $this->registry->register('redirect_to_category', $category);
                            break;
                        }
                    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                        //No action required in that case just continue foreach
                    }
                }
            }
        }
        return $result;
    }
}
