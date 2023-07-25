<?php

declare(strict_types=1);

namespace Oksydan\IsProductSlider\Form\Provider;

use Oksydan\IsProductSlider\Entity\ProductSlider;
use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;

class ProductSliderFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var LangRepository
     */
    private $langRepository;

    /**
     * @var Context
     */
    private $shopContext;

    /**
     * ProductSliderFormDataProvider constructor.
     *
     * @param EntityRepository $repository
     */
    public function __construct(
        EntityRepository $repository,
        LangRepository $langRepository,
        Context $shopContext
    ) {
        $this->repository = $repository;
        $this->shopContext = $shopContext;
        $this->langRepository = $langRepository;
    }

    /**
     * @param mixed $id
     *
     * @return array
     */
    public function getData($id): array
    {
        /** @var ProductSlider $slider */
        $slider = $this->repository->findOneBy(['id' => (int) $id]);

        $shopIds = [];
        $sliderData = [];

        foreach ($slider->getShops() as $shop) {
            $shopIds[] = $shop->getId();
        }

        $sliderData['shop_association'] = $shopIds;
        $sliderData['active'] = $slider->getActive();
        $sliderData['type_products_show'] = $slider->getTypeProductsShow();
        $sliderData['product_ids']['data'] = $slider->getProductIds();
        $sliderData['category_id'] = $slider->getCategoryId();

        foreach ($slider->getProductSliderLangs() as $sliderLang) {
            $sliderData['title'][$sliderLang->getLang()->getId()] = $sliderLang->getTitle();
            $sliderData['subtitle'][$sliderLang->getLang()->getId()] = $sliderLang->getSubtitle();
        }

        return $sliderData;
    }

    /**
     * @return array
     */
    public function getDefaultData(): array
    {
        return [
            'title' => [],
            'subtitle' => [],
            'type_products_show' => 'all',
            'product_ids' => null,
            'category_id' => null,
            'active' => false,
            'shop_association' => $this->shopContext->getContextListShopID(),
        ];
    }
}
