<?php
namespace App\Service\Product;

use App\Entity\ProductTag;
use App\DTO\Product\ProductTagDto;
use App\Exception\BusinessException;
use App\DTO\Product\ProductTagFilterDto;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\Product\ProductTagRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;

class ProductTagCrudService
{
    public function __construct(
        private ProductTagRepository $productRepository,
        private PaginatorInterface $paginator,
        private EntityManagerInterface $em) 
    {}

    public function getPaginated(ProductTagFilterDto $filter): PaginationInterface
    {
        $queryBuilder = $this->productRepository->createQueryBuilder('pt')->orderBy('pt.id', 'ASC');

        $pagination = $this->paginator->paginate(
            $queryBuilder, 
            $filter->page, 
            $filter->limit
        );

        $items = array_map(fn(ProductTag $pt) => new ProductTagDto(
                            $pt->getId(),
                            $pt->getName()
                        ), (array) $pagination->getItems());

        $pagination->setItems($items);   
         
        return $pagination;
    }

    public function create(ProductTagDto $productTagDtto): ProductTagDto
    {

        if ($this->productRepository->findOneBy(['name' => $productTagDtto->name])) {
            throw new BusinessException('Error al crear el Tag. Nombre Duplicado, ya existente en la base de datos.');
        }

        try {
            $productTag = new ProductTag();
            $productTag->setName($productTagDtto->name);

            $this->em->persist($productTag);
            $this->em->flush();

        } catch(\Exception $e) {
            throw new BusinessException('Error al crear Producto Título. Error en la presistencia.');
        }

        return new ProductTagDto(
                $productTag->getId(),
                $productTag->getName()
        );
    }
}