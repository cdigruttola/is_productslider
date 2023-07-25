<?php

declare(strict_types=1);

namespace Oksydan\IsProductSlider\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\DoctrineGridDataFactory;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class ProductSliderGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var DoctrineGridDataFactory
     */
    private $doctrineSliderDataFactory;

    /**
     * @param DoctrineGridDataFactory $doctrineSliderDataFactory
     */
    public function __construct(
        DoctrineGridDataFactory $doctrineSliderDataFactory
    ) {
        $this->doctrineSliderDataFactory = $doctrineSliderDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $languageData = $this->doctrineSliderDataFactory->getData($searchCriteria);

        return new GridData(
            new RecordCollection($languageData->getRecords()->all()),
            $languageData->getRecordsTotal(),
            $languageData->getQuery()
        );
    }
}
