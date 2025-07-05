<?php
namespace App\Service\Product;

use App\Entity\ProductTitle;
use App\DTO\Product\ProductTitleDto;
use App\Exception\BusinessException;
use App\DTO\Product\ProductFilterDto;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\Product\ProductRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ProductCrudService
{
    public function __construct(
        private ProductRepository $productRepository,
        private PaginatorInterface $paginator,
        private EntityManagerInterface $em,
        private CsrfTokenManagerInterface $csrfTokenManager) 
    {}
    
    public function getPaginated(ProductFilterDto $filter): PaginationInterface
    {
        $queryBuilder = $this->productRepository->createQueryBuilder('pt')->orderBy('pt.id', 'ASC');

        $pagination = $this->paginator->paginate(
            $queryBuilder, 
            $filter->page, 
            $filter->limit
        );

        $items = array_map(fn(ProductTitle $u) => new ProductTitleDto(
                            $u->getId(),
                            $u->getName(),
                            null,
                            null
                        ), (array) $pagination->getItems());

        $pagination->setItems($items);   
         
        return $pagination;
    }

    public function create(ProductTitleDto $productTitleDtto): ProductTitleDto
    {

        if ($this->productRepository->findOneBy(['name' => $productTitleDtto->name])) {
            throw new BusinessException('Error al crear Producto Título. Nombre Duplicado, ya existente en la base de datos.');
        }

        try {
            $productTitle = new ProductTitle();
            $productTitle->setName($productTitleDtto->name);

            $this->em->persist($productTitle);
            $this->em->flush();

        } catch(\Exception $e) {
            throw new BusinessException('Error al crear Producto Título. Error en la presistencia.');
        }

        return new ProductTitleDto(
                $productTitle->getId(),
                $productTitle->getName(),
                null,
                null
        );
    }

    public function getProductTitle(int $id, $returnEntity = false): ProductTitleDto|ProductTitle
    {
        
        $productTitle = $this->productRepository->find($id);

        if(!$productTitle) {
            throw new BusinessException('Error ProductTitle no encontrado.');
        }

        if($returnEntity) {
            return $productTitle;
        }              
        return ProductTitleDto::fromEntity($productTitle);
    }

    public function getProductTitleComplete(int $id): ProductTitle
    {
        
        $productTitle = $this->productRepository->getComplet($id);

        if(!$productTitle) {
            throw new BusinessException('Error ProductTitle no encontrado.');
        }

        return $productTitle;
                 
    }

    public function saveProductTitle(ProductTitleDto $productTitleDtto): void
    {
        $productTitle = $this->productRepository->findOneBy(['id' => $productTitleDtto->id]);

        if(!$productTitle) {
            throw new BusinessException('Error ProductTitle no encontrado.');
        }

        try{
            $this->em->persist($productTitle);
            $this->em->flush();

        } catch(\Exception $e) {
            throw new BusinessException('Error al grabar Producto Título. Error en la presistencia.');
        }
    }

    public function deleteProductTitle(int $id): void
    {
        $productTitle = $this->productRepository->getComplet($id);

         if(!$productTitle) {
            throw new BusinessException('Error ProductTitle no encontrado.');
        }

        try{
            $this->em->remove($productTitle);
            $this->em->flush();

        } catch(\Exception $e) {
            throw new BusinessException('Error al eleminar ProductTitle.'.$e->getMessage());
        }
    }
}