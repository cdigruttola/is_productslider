<?php

declare(strict_types=1);

namespace Oksydan\IsProductSlider\Form\Provider;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class ProductSliderConfigurationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $sliderConfigurationDataConfiguration;

    /**
     * @param DataConfigurationInterface $sliderConfigurationDataConfiguration
     */
    public function __construct(DataConfigurationInterface $sliderConfigurationDataConfiguration)
    {
        $this->sliderConfigurationDataConfiguration = $sliderConfigurationDataConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return $this->sliderConfigurationDataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data): array
    {
        return $this->sliderConfigurationDataConfiguration->updateConfiguration($data);
    }
}
