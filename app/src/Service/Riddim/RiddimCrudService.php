<?php
namespace App\Service\Riddim;

use App\Entity\Riddim;
use App\DTO\Riddim\RiddimDto;
use App\DTO\Riddim\RiddimFilterDto;
use App\Exception\BusinessException;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Riddim\RiddimRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class RiddimCrudService
{
    public function __construct(
        private RiddimRepository $riddimRepository,
        private PaginatorInterface $paginator,
        private EntityManagerInterface $em,
        private CsrfTokenManagerInterface $csrfTokenManager) {}

    public function getPaginated(RiddimFilterDto $filter): PaginationInterface
    {
        $queryBuilder = $this->riddimRepository->createQueryBuilder('r')->orderBy('r.id', 'ASC');

        $pagination = $this->paginator->paginate(
                            $queryBuilder, 
                            $filter->page, 
                            $filter->limit
                        );

        $items = array_map(fn(Riddim $riddim) => new RiddimDto(
                            $riddim->getId(),
                            $riddim->getName(),
                            $riddim->getTracks()->toArray()
                        ), (array) $pagination->getItems());

        $pagination->setItems($items);   
         
        return $pagination;
    }

    public function create(RiddimDto $riddimDto): RiddimDto
    {
        $artistExist = $this->riddimRepository->findOneBy(['name' => $riddimDto->name]);

        if ($artistExist) {
            throw new BusinessException('Error al crear Riddim. Nombre Duplicado, ya existente en la base de datos.');
        }

        try {
            $riddim = new Riddim();
            $riddim->setName($riddimDto->name);
            
            $this->em->persist($riddim);
            $this->em->flush();

        } catch(\Exception $e) {
            throw new BusinessException('Error al crear artista. Error en la presistencia. Messages: '.$e->getMessage());
        }

        return new RiddimDto(
                id: $riddim->getId(),
                name: $riddim->getName(),
                tracks: $riddim->getTracks()->toArray()
        );
    }
}