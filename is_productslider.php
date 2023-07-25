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
if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use Oksydan\IsProductSlider\Configuration\ProductSliderConfiguration;
use Oksydan\IsProductSlider\Entity\ProductSlider;
use Oksydan\IsProductSlider\Installer\DatabaseYamlParser;
use Oksydan\IsProductSlider\Installer\ProductSliderInstaller;
use Oksydan\IsProductSlider\Installer\Provider\DatabaseYamlProvider;
use Oksydan\IsProductSlider\Presenter\ProductSliderPresenter;
use Oksydan\IsProductSlider\Repository\ProductSliderRepository;
use Oksydan\IsProductSlider\Translations\TranslationDomains;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class Is_productslider extends Module implements WidgetInterface
{
    public $multistoreCompatibility = self::MULTISTORE_COMPATIBILITY_YES;
    private string $templateFile;

    public function __construct()
    {
        $this->name = 'is_productslider';
        $this->author = 'cdigruttola';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Product Slider', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
        $this->description = $this->trans('Products slider on Home page is an easy way to increase your sales.', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall this module?', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
        $this->templateFile = 'module:is_productslider/views/templates/hook/is_productslider.tpl';
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    public function install($reset = false)
    {
        $tableResult = true;
        if (!$reset) {
            $tableResult = $this->getInstaller()->createTables();
        }

        $this->_clearCache('*');

        return parent::install()
            && $tableResult
            && $this->registerHook('displayHome');
    }

    public function uninstall($reset = false)
    {
        $tableResult = true;
        if (!$reset) {
            $tableResult = $this->getInstaller()->dropTables();
        }

        return $tableResult && parent::uninstall();
    }

    public function reset()
    {
        return $this->uninstall(true) && $this->install(true);
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }

    public function getContent(): void
    {
        Tools::redirectAdmin(SymfonyContainer::getInstance()->get('router')->generate('productslider_controller'));
    }

    private function getInstaller(): ProductSliderInstaller
    {
        try {
            $installer = $this->getService('oksydan.is_productslider.product_slider_installer');
        } catch (Error $error) {
            $installer = null;
        }

        if (null === $installer) {
            $installer = new ProductSliderInstaller(
                $this->getService('doctrine.dbal.default_connection'),
                new DatabaseYamlParser(new DatabaseYamlProvider($this)),
                $this->context
            );
        }

        return $installer;
    }

    private function getSliderConfiguration(): ProductSliderConfiguration
    {
        try {
            $sliderConfig = $this->getService('oksydan.is_productslider.configuration.product_slider_configuration');
        } catch (Error $error) {
            $sliderConfig = null;
        }

        if (null === $sliderConfig) {
            $sliderConfig = new ProductSliderConfiguration();
        }

        return $sliderConfig;
    }

    private function getSliderRepository(): ProductSliderRepository
    {
        try {
            $sliderRepository = $this->getService('oksydan.is_productslider.repository.product_slider');
        } catch (Error $error) {
            $sliderRepository = null;
        }

        if (null === $sliderRepository) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->get('doctrine.orm.entity_manager');
            $sliderRepository = $entityManager->getRepository(ProductSlider::class);
        }

        return $sliderRepository;
    }

    private function getSliderPresenter(): ProductSliderPresenter
    {
        try {
            $sliderPresenter = $this->getService('oksydan.is_productslider.presenter.product_slider_presenter');
        } catch (Error $error) {
            $sliderPresenter = null;
        }

        if (null === $sliderPresenter) {
            $sliderPresenter = new ProductSliderPresenter($this->context);
        }

        return $sliderPresenter;
    }

    /**
     * @template T
     *
     * @param class-string<T>|string $serviceName
     *
     * @return T|object|null
     */
    public function getService($serviceName)
    {
        try {
            return $this->get($serviceName);
        } catch (ServiceNotFoundException|Exception $exception) {
            return null;
        }
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId('is_productslider'))) {
            $variables = $this->getWidgetVariables($hookName, $configuration);

            if (empty($variables)) {
                return false;
            }
            $this->smarty->assign($variables);
        }

        PrestaShopLogger::addLog(__METHOD__);
        return $this->fetch($this->templateFile, $this->getCacheId('is_productslider'));
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $sliders = $this->getSliderRepository()->getActiveProductSliderByLangAndStoreId($this->context->language->id, $this->context->shop->id);

        if (!empty($sliders)) {
            $slidersPresented = [];
            foreach ($sliders as $slider) {
                $slidersPresented[] = $this->getSliderPresenter()->present($slider);
            }

            return [
                'sliders' => $slidersPresented,
                'speed' => $this->getSliderConfiguration()->getProductSliderSpeed(),
                'pause' => $this->getSliderConfiguration()->getProductSliderPauseOnHover(),
                'wrap' => $this->getSliderConfiguration()->getProductSliderWrap(),
            ];
        }

        return false;
    }

    protected function getCacheId($name = null)
    {
        $cacheId = parent::getCacheId($name);
        if (!empty($this->context->customer->id)) {
            $cacheId .= '|' . $this->context->customer->id;
        }

        return $cacheId;
    }
}
