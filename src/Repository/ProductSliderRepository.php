<?php

declare(strict_types=1);

namespace Oksydan\IsProductSlider\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ProductSliderRepository extends EntityRepository
{
    public function getAllIds(): array
    {
        /** @var QueryBuilder $qb */
        $qb = $this
            ->createQueryBuilder('s')
            ->select('s.id')
        ;

        $slides = $qb->getQuery()->getScalarResult();

        return array_map(function ($slide) {
            return $slide['id'];
        }, $slides);
    }

    public function getHighestPosition(): int
    {
        $position = 0;
        $qb = $this
            ->createQueryBuilder('s')
            ->select('s.position')
            ->orderBy('s.position', 'ASC')
            ->setMaxResults(1)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        if ($result) {
            $position = $result['position'];
        }

        return $position;
    }

    public function getSimpleActiveProductSliderByStoreId(
        int $idStore,
        bool $activeOnly = true,
        int $limit = 0
    ): array {
        $qb = $this
            ->createQueryBuilder('s')
            ->select('s.id, s.position, s.active, s.type_products_show, s.product_ids, s.category_id')
            ->join('s.shops', 'ss')
            ->andWhere('ss.id = :storeId')
            ->orderBy('s.position')
            ->setParameter('storeId', (int) $idStore);

        if ($activeOnly) {
            $qb->andWhere('s.active = 1');
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getScalarResult();
    }

    public function getActiveProductSliderByLangAndStoreId(
        int $idLang,
        int $idStore,
        bool $activeOnly = true,
        int $limit = 0
    ): array {
        $qb = $this
            ->createQueryBuilder('s')
            ->select('sl.title, sl.subtitle, s.type_products_show, s.product_ids, s.category_id')
            ->join('s.productSliderLangs', 'sl')
            ->join('s.shops', 'ss')
            ->andWhere('sl.lang = :langId')
            ->andWhere('ss.id = :storeId')
            ->orderBy('s.position')
            ->setParameter('langId', (int) $idLang)
            ->setParameter('storeId', (int) $idStore);

        if ($activeOnly) {
            $qb->andWhere('s.active = 1');
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getScalarResult();
    }
}
