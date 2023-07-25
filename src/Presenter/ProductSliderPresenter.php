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

namespace Oksydan\IsProductSlider\Presenter;

use Category;
use Oksydan\IsProductSlider\ProductSearchProvider\ProductSliderProductSearchProvider;
use Context;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use ProductAssembler;
use ProductPresenterFactory;

class ProductSliderPresenter
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @param $slider
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     * @throws \ReflectionException
     */
    public function present($slider): array
    {
        if ($slider['type_products_show'] === 'all') {
            $searchProvider = new CategoryProductSearchProvider($this->context->getTranslator(), Category::getRootCategory());
        } elseif ($slider['type_products_show'] === 'category') {
            $searchProvider = new CategoryProductSearchProvider($this->context->getTranslator(), new Category($slider['category_id']));
        } else {
            $searchProvider = new ProductSliderProductSearchProvider($slider['product_ids']);
        }

        $context = new ProductSearchContext($this->context);
        $query = new ProductSearchQuery();
        $result = $searchProvider->runQuery($context, $query);

        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = $presenterFactory->getPresenter();

        $products_for_template = [];

        foreach ($result->getProducts() as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }

        return
            [
                'title' => $slider['title'],
                'subtitle' => $slider['subtitle'],
                'products' => $products_for_template,
            ];
    }
}
