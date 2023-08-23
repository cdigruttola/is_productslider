<?php

declare(strict_types=1);

namespace Oksydan\IsProductSlider\Form\DataHandler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Oksydan\IsImageslider\Entity\ImageSliderLang;
use Oksydan\IsProductSlider\Entity\ProductSlider;
use Oksydan\IsProductSlider\Entity\ProductSliderLang;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Entity\Shop;

class ProductSliderFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var EntityRepository
     */
    private $sliderRepository;

    /**
     * @var LangRepository
     */
    private $langRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array
     */
    private $languages;

    public function __construct(
        EntityRepository $sliderRepository,
        LangRepository $langRepository,
        EntityManagerInterface $entityManager,
        array $languages
    ) {
        $this->sliderRepository = $sliderRepository;
        $this->langRepository = $langRepository;
        $this->entityManager = $entityManager;
        $this->languages = $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        $slider = new ProductSlider();

        $slider->setActive($data['active']);
        $slider->setTypeProductsShow($data['type_products_show'] ?? 'all');
        $slider->setCategoryId($data['category_id'] ?? null);
        $slider->setProductIds($data['product_ids']['data'] ?? null);
        $slider->setPosition($this->sliderRepository->getHighestPosition() + 1);
        $this->addAssociatedShops($slider, $data['shop_association'] ?? null);

        foreach ($this->languages as $language) {
            $langId = (int) $language['id_lang'];
            $lang = $this->langRepository->findOneBy(['id' => $langId]);
            $sliderLang = new ProductSliderLang();

            $sliderLang
                ->setLang($lang)
                ->setTitle($data['title'][$langId] ?? '')
                ->setSubtitle($data['subtitle'][$langId] ?? '');

            $slider->addProductSliderLang($sliderLang);
        }

        $this->entityManager->persist($slider);
        $this->entityManager->flush();

        return $slider->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): int
    {
        /** @var ProductSlider $slider */
        $slider = $this->entityManager->getRepository(ProductSlider::class)->find($id);

        $slider->setActive($data['active']);
        $slider->setTypeProductsShow($data['type_products_show'] ?? 'all');
        $slider->setCategoryId($data['category_id'] ?? null);
        $slider->setProductIds($data['product_ids']['data'] ?? null);
        $this->addAssociatedShops($slider, $data['shop_association'] ?? null);

        foreach ($this->languages as $language) {
            $langId = (int) $language['id_lang'];
            $productSliderLang = $slider->getProductSliderLangByLangId($langId);

            $newEntity = false;
            if (null === $productSliderLang) {
                $productSliderLang = new ProductSliderLang();
                $lang = $this->langRepository->findOneById($langId);
                $productSliderLang->setLang($lang);
                $newEntity = true;

            }

            $productSliderLang
                ->setTitle($data['title'][$langId] ?? '')
                ->setSubtitle($data['subtitle'][$langId] ?? '');

            if($newEntity) {
                $slider->addProductSliderLang($productSliderLang);
            }

        }

        $this->entityManager->flush();

        return $slider->getId();
    }

    /**
     * @param ProductSlider $slider
     * @param array|null $shopIdList
     */
    private function addAssociatedShops(ProductSlider & $slider, array $shopIdList = null): void
    {
        $slider->clearShops();

        if (empty($shopIdList)) {
            return;
        }

        foreach ($shopIdList as $shopId) {
            $shop = $this->entityManager->getRepository(Shop::class)->find($shopId);
            $slider->addShop($shop);
        }
    }
}
