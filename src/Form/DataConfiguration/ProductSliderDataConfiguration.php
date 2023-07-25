<?php

declare(strict_types=1);

namespace Oksydan\IsProductSlider\Form\DataConfiguration;

use Oksydan\IsProductSlider\Configuration\ProductSliderConfiguration;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Handles configuration data for demo multistore configuration options.
 */
final class ProductSliderDataConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'speed',
        'pause',
        'wrap',
        'max_product',
    ];

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('speed', 'string')
            ->setAllowedTypes('pause', 'bool')
            ->setAllowedTypes('wrap', 'bool')
            ->setAllowedTypes('max_product', 'int');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        $return = [];
        $shopConstraint = $this->getShopConstraint();

        $return['speed'] = $this->configuration->get(ProductSliderConfiguration::PRODUCT_SLIDER_SPEED, null, $shopConstraint);
        $return['pause'] = $this->configuration->get(ProductSliderConfiguration::PRODUCT_SLIDER_PAUSE_ON_HOVER, null, $shopConstraint);
        $return['wrap'] = $this->configuration->get(ProductSliderConfiguration::PRODUCT_SLIDER_WRAP, null, $shopConstraint);
        $return['max_product'] = $this->configuration->get(ProductSliderConfiguration::PRODUCT_SLIDER_MAX_PRODUCT, null, $shopConstraint);

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration): array
    {
        $shopConstraint = $this->getShopConstraint();
        $this->updateConfigurationValue(ProductSliderConfiguration::PRODUCT_SLIDER_SPEED, 'speed', $configuration, $shopConstraint);
        $this->updateConfigurationValue(ProductSliderConfiguration::PRODUCT_SLIDER_PAUSE_ON_HOVER, 'pause', $configuration, $shopConstraint);
        $this->updateConfigurationValue(ProductSliderConfiguration::PRODUCT_SLIDER_WRAP, 'wrap', $configuration, $shopConstraint);
        $this->updateConfigurationValue(ProductSliderConfiguration::PRODUCT_SLIDER_MAX_PRODUCT, 'max_product', $configuration, $shopConstraint);

        return [];
    }
}
