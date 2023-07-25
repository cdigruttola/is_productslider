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
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Range;

class ProductSliderConfigurationType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $minTime = 1000;
        $maxTime = 60000;
        $rangeInvalidMessage = $this->trans(
            'This field value have to be between %min%ms and %max%ms.',
            TranslationDomains::TRANSLATION_DOMAIN_ADMIN,
            [
                '%min%' => $minTime,
                '%max%' => $maxTime,
            ]
        );

        $builder
            ->add('speed', TextType::class, [
                'attr' => ['class' => 'col-md-4 col-lg-2'],
                'required' => true,
                'label' => $this->trans('Speed', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'help' => $this->trans('The duration of the transition between two slides.', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'multistore_configuration_key' => ProductSliderConfiguration::PRODUCT_SLIDER_SPEED,
                'constraints' => [
                    new Range([
                        'min' => $minTime,
                        'max' => $maxTime,
                        'invalidMessage' => $rangeInvalidMessage,
                        'maxMessage' => $rangeInvalidMessage,
                        'minMessage' => $rangeInvalidMessage,
                    ]),
                ],
            ])
            ->add('pause', SwitchType::class, [
                'label' => $this->trans('Pause on hover', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'help' => $this->trans('Stop sliding when the mouse cursor is over the slideshow.', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'multistore_configuration_key' => ProductSliderConfiguration::PRODUCT_SLIDER_PAUSE_ON_HOVER,
            ])
            ->add('wrap', SwitchType::class, [
                'label' => $this->trans('Wrap', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'help' => $this->trans('Loop or stop after the last slide.', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'multistore_configuration_key' => ProductSliderConfiguration::PRODUCT_SLIDER_WRAP,
            ])
            ->add('max_product', NumberType::class, [
                'label' => $this->trans('Max Product on Slider', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'help' => $this->trans('Maximum product on slider when slider type is product.', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
                'multistore_configuration_key' => ProductSliderConfiguration::PRODUCT_SLIDER_MAX_PRODUCT,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 4,
                        'message' => $this->trans('%s is invalid.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
