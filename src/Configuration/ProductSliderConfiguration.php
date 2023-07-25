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

namespace Oksydan\IsProductSlider\Configuration;

use Configuration;

class ProductSliderConfiguration
{
    public const PRODUCT_SLIDER_SPEED = 'PRODUCT_SLIDER_SPEED';
    public const PRODUCT_SLIDER_PAUSE_ON_HOVER = 'PRODUCT_SLIDER_PAUSE_ON_HOVER';
    public const PRODUCT_SLIDER_WRAP = 'PRODUCT_SLIDER_WRAP';
    public const PRODUCT_SLIDER_MAX_PRODUCT = 'PRODUCT_SLIDER_MAX_PRODUCT';

    public function getProductSliderSpeed()
    {
        return Configuration::get(self::PRODUCT_SLIDER_SPEED);
    }

    public function getProductSliderPauseOnHover()
    {
        return Configuration::get(self::PRODUCT_SLIDER_PAUSE_ON_HOVER);
    }

    public function getProductSliderWrap()
    {
        return Configuration::get(self::PRODUCT_SLIDER_WRAP);
    }

    public function getProductSliderMaxProduct()
    {
        return Configuration::get(self::PRODUCT_SLIDER_MAX_PRODUCT);
    }

}
