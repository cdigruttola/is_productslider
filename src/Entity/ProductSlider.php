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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Shop;

/**
 * @ORM\Entity(repositoryClass="Oksydan\IsProductSlider\Repository\ProductSliderRepository")
 *
 * @ORM\Table()
 */
class ProductSlider
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_product_slider", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="type_products_show", type="string")
     */
    private $type_products_show;
    /**
     * @var string
     *
     * @ORM\Column(name="product_ids", type="string")
     */
    private $product_ids;

    /**
     * @var string
     *
     * @ORM\Column(name="category_id", type="string")
     */
    private $category_id;

    /**
     * @ORM\OneToMany(targetEntity="Oksydan\IsProductSlider\Entity\ProductSliderLang", cascade={"persist", "remove"}, mappedBy="productSlider")
     */
    private $productSliderLangs;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Shop", cascade={"persist"})
     *
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="id_product_slider", referencedColumnName="id_product_slider")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", onDelete="CASCADE")}
     * )
     */
    private $shops;

    public function __construct()
    {
        $this->shops = new ArrayCollection();
        $this->productSliderLangs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return ProductSlider $this
     */
    public function setActive(bool $active): ProductSlider
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return ProductSlider $this
     */
    public function setPosition(int $position): ProductSlider
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string
     */
    public function getTypeProductsShow(): string
    {
        return $this->type_products_show;
    }

    /**
     * @param string $type_products_show
     *
     * @return ProductSlider $this
     */
    public function setTypeProductsShow(string $type_products_show): ProductSlider
    {
        $this->type_products_show = $type_products_show;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getProductIds(): ?array
    {
        if (!empty($this->product_ids)) {
            return explode(',', $this->product_ids);
        }

        return null;
    }

    /**
     * @param array|null $product_ids
     *
     * @return ProductSlider $this
     */
    public function setProductIds(?array $product_ids): ProductSlider
    {
        if (!empty($product_ids)) {
            $this->product_ids = implode(',', $product_ids);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCategoryId(): ?string
    {
        return $this->category_id;
    }

    /**
     * @param string|null $category_id
     *
     * @return ProductSlider $this
     */
    public function setCategoryId(?string $category_id): ProductSlider
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * @param Shop $shop
     *
     * @return ProductSlider $this
     */
    public function addShop(Shop $shop): ProductSlider
    {
        $this->shops[] = $shop;

        return $this;
    }

    /**
     * @param Shop $shop
     *
     * @return ProductSlider $this
     */
    public function removeShop(Shop $shop): ProductSlider
    {
        $this->shops->removeElement($shop);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    /**
     * @return ProductSlider $this
     */
    public function clearShops(): ProductSlider
    {
        $this->shops->clear();

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getProductSliderLangs()
    {
        return $this->productSliderLangs;
    }

    /**
     * @param int $langId
     *
     * @return ProductSliderLang|null
     */
    public function getProductSliderLangByLangId(int $langId): ?ProductSliderLang
    {
        foreach ($this->productSliderLangs as $sliderLang) {
            if ($langId === $sliderLang->getLang()->getId()) {
                return $sliderLang;
            }
        }

        return null;
    }

    /**
     * @param ProductSliderLang $sliderLang
     *
     * @return ProductSlider $this
     */
    public function addProductSliderLang(ProductSliderLang $sliderLang): ProductSlider
    {
        $sliderLang->setProductSlider($this);
        $this->productSliderLangs->add($sliderLang);

        return $this;
    }
}
