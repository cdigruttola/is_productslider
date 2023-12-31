<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite062736197fb77d6b573f0ff9999d113
{
    public static $prefixLengthsPsr4 = array (
        'O' => 
        array (
            'Oksydan\\IsProductSlider\\' => 24,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Oksydan\\IsProductSlider\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Is_productslider' => __DIR__ . '/../..' . '/is_productslider.php',
        'Oksydan\\IsProductSlider\\Configuration\\ProductSliderConfiguration' => __DIR__ . '/../..' . '/src/Configuration/ProductSliderConfiguration.php',
        'Oksydan\\IsProductSlider\\Controller\\ProductSliderController' => __DIR__ . '/../..' . '/src/Controller/ProductSliderController.php',
        'Oksydan\\IsProductSlider\\Entity\\ProductSlider' => __DIR__ . '/../..' . '/src/Entity/ProductSlider.php',
        'Oksydan\\IsProductSlider\\Entity\\ProductSliderLang' => __DIR__ . '/../..' . '/src/Entity/ProductSliderLang.php',
        'Oksydan\\IsProductSlider\\Exceptions\\DatabaseYamlFileNotExistsException' => __DIR__ . '/../..' . '/src/Exceptions/DatabaseYamlFileNotExistsException.php',
        'Oksydan\\IsProductSlider\\Filter\\ProductSliderFilters' => __DIR__ . '/../..' . '/src/Filter/ProductSliderFilters.php',
        'Oksydan\\IsProductSlider\\Form\\DataConfiguration\\ProductSliderDataConfiguration' => __DIR__ . '/../..' . '/src/Form/DataConfiguration/ProductSliderDataConfiguration.php',
        'Oksydan\\IsProductSlider\\Form\\DataHandler\\ProductSliderFormDataHandler' => __DIR__ . '/../..' . '/src/Form/DataHandler/ProductSliderFormDataHandler.php',
        'Oksydan\\IsProductSlider\\Form\\ProductSliderConfigurationType' => __DIR__ . '/../..' . '/src/Form/ProductSliderConfigurationType.php',
        'Oksydan\\IsProductSlider\\Form\\ProductSliderType' => __DIR__ . '/../..' . '/src/Form/ProductSliderType.php',
        'Oksydan\\IsProductSlider\\Form\\Provider\\ProductSliderConfigurationFormDataProvider' => __DIR__ . '/../..' . '/src/Form/Provider/ProductSliderConfigurationFormDataProvider.php',
        'Oksydan\\IsProductSlider\\Form\\Provider\\ProductSliderFormDataProvider' => __DIR__ . '/../..' . '/src/Form/Provider/ProductSliderFormDataProvider.php',
        'Oksydan\\IsProductSlider\\Grid\\Data\\Factory\\ProductSliderGridDataFactory' => __DIR__ . '/../..' . '/src/Grid/Data/Factory/ProductSliderGridDataFactory.php',
        'Oksydan\\IsProductSlider\\Grid\\Definition\\Factory\\ProductSliderGridDefinitionFactory' => __DIR__ . '/../..' . '/src/Grid/Definition/Factory/ProductSliderGridDefinitionFactory.php',
        'Oksydan\\IsProductSlider\\Grid\\Query\\ProductSliderQueryBuilder' => __DIR__ . '/../..' . '/src/Grid/Query/ProductSliderQueryBuilder.php',
        'Oksydan\\IsProductSlider\\Installer\\ActionDatabaseAbstract' => __DIR__ . '/../..' . '/src/Installer/ActionDatabaseAbstract.php',
        'Oksydan\\IsProductSlider\\Installer\\ActionDatabaseAddColumn' => __DIR__ . '/../..' . '/src/Installer/ActionDatabaseAddColumn.php',
        'Oksydan\\IsProductSlider\\Installer\\ActionDatabaseCrateTable' => __DIR__ . '/../..' . '/src/Installer/ActionDatabaseCrateTable.php',
        'Oksydan\\IsProductSlider\\Installer\\ActionDatabaseDropTable' => __DIR__ . '/../..' . '/src/Installer/ActionDatabaseDropTable.php',
        'Oksydan\\IsProductSlider\\Installer\\ActionDatabaseInterface' => __DIR__ . '/../..' . '/src/Installer/ActionDatabaseInterface.php',
        'Oksydan\\IsProductSlider\\Installer\\ActionDatabaseModifyColumn' => __DIR__ . '/../..' . '/src/Installer/ActionDatabaseModifyColumn.php',
        'Oksydan\\IsProductSlider\\Installer\\DatabaseYamlParser' => __DIR__ . '/../..' . '/src/Installer/DatabaseYamlParser.php',
        'Oksydan\\IsProductSlider\\Installer\\ProductSliderInstaller' => __DIR__ . '/../..' . '/src/Installer/ProductSliderInstaller.php',
        'Oksydan\\IsProductSlider\\Installer\\Provider\\DatabaseYamlProvider' => __DIR__ . '/../..' . '/src/Installer/Provider/DatabaseYamlProvider.php',
        'Oksydan\\IsProductSlider\\Presenter\\ProductSliderPresenter' => __DIR__ . '/../..' . '/src/Presenter/ProductSliderPresenter.php',
        'Oksydan\\IsProductSlider\\ProductSearchProvider\\ProductSliderProductSearchProvider' => __DIR__ . '/../..' . '/src/ProductSearchProvider/ProductSliderProductSearchProvider.php',
        'Oksydan\\IsProductSlider\\Repository\\ProductSliderRepository' => __DIR__ . '/../..' . '/src/Repository/ProductSliderRepository.php',
        'Oksydan\\IsProductSlider\\Translations\\TranslationDomains' => __DIR__ . '/../..' . '/src/Translations/TranslationDomains.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite062736197fb77d6b573f0ff9999d113::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite062736197fb77d6b573f0ff9999d113::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite062736197fb77d6b573f0ff9999d113::$classMap;

        }, null, ClassLoader::class);
    }
}
