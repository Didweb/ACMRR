<?php
namespace App\Repository\Product;

use App\Entity\ProductTitle;
use App\Exception\BusinessException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductTitle::class);
    }

    public function getComplet(int $id): ?ProductTitle
    {
        $qb = $this->createQueryBuilder('pt')
        ->leftJoin('pt.editions', 'ed')
        ->addSelect('ed')
        ->leftJoin('ed.images', 'img')
        ->addSelect('img')
        ->where('pt.id = :id')
        ->setParameter('id', $id);

        $productTitle = $qb->getQuery()->getOneOrNullResult();

        if (!$productTitle) {
            throw new BusinessException('Error ProductTitle no encontrado.');
        }
        
        return $productTitle;

    }
}