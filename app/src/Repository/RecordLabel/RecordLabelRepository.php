<?php
namespace App\Repository\RecordLabel;

use App\Entity\RecordLabel;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class RecordLabelRepository extends ServiceEntityRepository 
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecordLabel::class);
    }
}