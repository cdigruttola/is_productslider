<?php
/**
 * Copyright since 2007 Carmine Di Gruttola
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    cdigruttola <c.digruttola@hotmail.it>
 * @copyright Copyright since 2007 Carmine Di Gruttola
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

declare(strict_types=1);

namespace Oksydan\IsProductSlider\ProductSearchProvider;

use Combination;
use Configuration;
use Db;
use DbQuery;
use FrontController;
use Group;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use Product;
use Shop;
use StockAvailable;
use Validate;

class ProductSliderProductSearchProvider implements ProductSearchProviderInterface
{
    /**
     * @var Db
     */
    private $db;

    /**
     * @var string
     */
    private $productIds;

    /**
     * @param string $productIds
     */
    public function __construct(string $productIds)
    {
        $this->db = Db::getInstance();
        $this->productIds = $productIds;
    }

    public function runQuery(ProductSearchContext $context, ProductSearchQuery $query)
    {
        $result = new ProductSearchResult();
        $result->setProducts($this->getProductsOrCount($context, $query));
        $result->setTotalProductsCount($this->getProductsOrCount($context, $query, 'count'));

        return $result;
    }

    private function getProductsOrCount(ProductSearchContext $context, ProductSearchQuery $query, $type = 'products')
    {
        $querySearch = new DbQuery();

        if ('products' === $type) {
            $querySearch->select('p.*');
            $querySearch->select('product_shop.*');
            $querySearch->select('stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity');
            $querySearch->select('pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`,
            pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`');
            $querySearch->select('image_shop.`id_image` AS id_image');
            $querySearch->select('il.`legend`');
            $querySearch->select('
            DATEDIFF(
                product_shop.`date_add`,
                DATE_SUB(
                    "' . date('Y-m-d') . ' 00:00:00",
                    INTERVAL ' . (0 <= (int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                )
            ) > 0 AS new'
            );

            if (Combination::isFeatureActive()) {
                $querySearch->select('product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) AS id_product_attribute');
            }
        } else {
            $querySearch->select('COUNT(DISTINCT p.id_product)');
        }

        $querySearch->from('product', 'p');
        $querySearch->join(Shop::addSqlAssociation('product', 'p'));
        $querySearch->leftJoin('category_product', 'cp', 'p.id_product = cp.id_product AND cp.id_category = product_shop.id_category_default');

        if (Combination::isFeatureActive()) {
            $querySearch->leftJoin('product_attribute_shop', 'product_attribute_shop', 'p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.id_shop=' . (int) $context->getIdShop());
        }

        if ('products' === $type) {
            $querySearch->leftJoin('stock_available', 'stock', 'stock.id_product = `p`.id_product ' . StockAvailable::addSqlShopRestriction(null, (int) $context->getIdShop(), 'stock'));
            $querySearch->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $context->getIdLang() . \Shop::addSqlRestrictionOnLang('pl'));
            $querySearch->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop = ' . (int) $context->getIdShop());
            $querySearch->leftJoin('image_lang', 'il', 'image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $context->getIdLang());
            $querySearch->leftJoin('category', 'ca', 'cp.`id_category` = ca.`id_category` AND ca.`active` = 1');
        }

        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sqlGroups = false === empty($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id;
            $querySearch->leftJoin('category_group', 'cg', 'cp.`id_category` = cg.`id_category` AND cg.`id_group`' . $sqlGroups);
        }

        $querySearch->where('p.id_product IN (' . $this->productIds . ')');
        $querySearch->where('product_shop.active = 1');
        $querySearch->where('product_shop.visibility IN ("both", "catalog")');

        if ('products' === $type) {
            $sortOrder = $query->getSortOrder()->toLegacyOrderBy(true);
            $sortWay = $query->getSortOrder()->toLegacyOrderWay();
            if (Validate::isOrderBy($sortOrder) && Validate::isOrderWay($sortWay)) {
                $querySearch->orderBy($sortOrder . ' ' . $sortWay);
            }
            $querySearch->limit((int) $query->getResultsPerPage(), ((int) $query->getPage() - 1) * (int) $query->getResultsPerPage());
            $querySearch->groupBy('p.id_product');

            $products = $this->db->executeS($querySearch);

            if (empty($products)) {
                return [];
            }

            return Product::getProductsProperties((int) $context->getIdLang(), $products);
        }

        return (int) $this->db->getValue($querySearch);
    }
}
