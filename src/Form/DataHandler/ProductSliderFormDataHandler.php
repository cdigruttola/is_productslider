<?php

declare(strict_types=1);

namespace Oksydan\IsProductSlider\Form\DataHandler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
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
        $slider = $this->entityManager->getRepository(ProductSlider::class)->find($id);

        $slider->setActive($data['active']);
        $slider->setTypeProductsShow($data['type_products_show'] ?? 'all');
        $slider->setCategoryId($data['category_id'] ?? null);
        $slider->setProductIds($data['product_ids']['data'] ?? null);
        $this->addAssociatedShops($slider, $data['shop_association'] ?? null);

        foreach ($this->languages as $language) {
            $langId = (int) $language['id_lang'];
            $imageSliderLang = $slider->getSliderLangByLangId($langId);

            if (null === $imageSliderLang) {
                continue;
            }

            $imageSliderLang
                ->setTitle($data['title'][$langId] ?? '')
                ->setSubtitle($data['subtitle'][$langId] ?? '');
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
