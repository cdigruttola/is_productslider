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

namespace Oksydan\IsProductSlider\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Lang;

/**
 * @ORM\Table()
 *
 * @ORM\Entity
 */
class ProductSliderLang
{
    /**
     * @var ProductSlider
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="Oksydan\IsProductSlider\Entity\ProductSlider", inversedBy="productSliderLang")
     *
     * @ORM\JoinColumn(name="id_product_slider", referencedColumnName="id_product_slider", nullable=false)
     */
    private $productSlider;

    /**
     * @var Lang
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     *
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title;
    /**
     * @var string
     *
     * @ORM\Column(name="subtitle", type="text")
     */
    private $subtitle;

    /**
     * @return ProductSlider
     */
    public function getProductSlider(): ProductSlider
    {
        return $this->productSlider;
    }

    /**
     * @param ProductSlider $slider
     *
     * @return ProductSliderLang $this
     */
    public function setProductSlider(ProductSlider $slider): ProductSliderLang
    {
        $this->productSlider = $slider;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return ProductSliderLang $this
     */
    public function setTitle(string $title): ProductSliderLang
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     *
     * @return ProductSliderLang $this
     */
    public function setSubtitle(string $subtitle): ProductSliderLang
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * @return Lang
     */
    public function getLang(): Lang
    {
        return $this->lang;
    }

    /**
     * @param Lang $lang
     *
     * @return ProductSliderLang $this
     */
    public function setLang(Lang $lang): ProductSliderLang
    {
        $this->lang = $lang;

        return $this;
    }
}
