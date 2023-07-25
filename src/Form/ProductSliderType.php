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

namespace Oksydan\IsProductSlider\Form;

use Oksydan\IsProductSlider\Configuration\ProductSliderConfiguration;
use Oksydan\IsProductSlider\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Form\Admin\Type\CategoryChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductSliderType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isMultistoreUsed;
    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @var ProductSliderConfiguration
     */
    private $sliderConfiguration;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param RouterInterface $router
     * @param string $employeeIsoCode
     * @param bool $isMultistoreUsed
     * @param LegacyContext $context
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $isMultistoreUsed,
        LegacyContext $context,
        ProductSliderConfiguration $sliderConfiguration
    ) {
        parent::__construct($translator, $locales);

        $this->isMultistoreUsed = $isMultistoreUsed;
        $this->context = $context;
        $this->sliderConfiguration = $sliderConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Title', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => true,
                'options' => [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ],
            ])
            ->add('subtitle', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Sub Title', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'locales' => $this->locales,
                'required' => false,
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Active', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'required' => true,
            ])
            ->add('type_products_show', ChoiceType::class, [
                'choices' => ['all' => 'all', 'product' => 'product', 'category' => 'category'],
                'attr' => [
                    'data-toggle' => 'select2',
                    'data-minimumResultsForSearch' => '7',
                ],
                'label' => $this->trans('Type of products to show', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
            ])
            ->add('category_id', CategoryChoiceTreeType::class, [
                'label' => $this->trans('Categories', 'Admin.Global'),
                'required' => false,
            ])
            ->add('product_ids', TypeaheadProductCollectionType::class, [
                'remote_url' => $this->context->getLegacyAdminLink('AdminProducts', true, ['ajax' => 1, 'action' => 'productsList', 'forceJson' => 1, 'disableCombination' => 1, 'exclude_packs' => 0, 'excludeVirtuals' => 0, 'limit' => 20]) . '&q=%QUERY',
                'mapping_value' => 'id',
                'mapping_name' => 'name',
                'placeholder' => $this->trans('Search product', 'Admin.Catalog.Help'),
                'template_collection' => '<span class="label">%s</span><i class="material-icons delete">clear</i>',
                'required' => false,
                'label' => $this->trans('Products', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'limit' => $this->sliderConfiguration->getProductSliderMaxProduct(),
            ]);

        if ($this->isMultistoreUsed) {
            $builder->add(
                'shop_association',
                ShopChoiceTreeType::class,
                [
                    'label' => $this->trans('Shop associations', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'You have to select at least one shop to associate this item with',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ]
            );
        }
    }
}
