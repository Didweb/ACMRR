<?php
namespace App\Service\Artist;

use App\Entity\Artist;
use App\DTO\Artist\ArtistDto;
use App\DTO\Artist\ArtistFilterDto;
use App\Exception\BusinessException;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Artist\ArtistRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ArtistCrudService
{
    public function __construct(
        private ArtistRepository $artistRepository,
        private PaginatorInterface $paginator,
        private EntityManagerInterface $em,
        private CsrfTokenManagerInterface $csrfTokenManager) {}

    public function getPaginated(ArtistFilterDto $filter): PaginationInterface
    {
        $queryBuilder = $this->artistRepository->createQueryBuilder('a')->orderBy('a.id', 'ASC');

        $pagination = $this->paginator->paginate(
                            $queryBuilder, 
                            $filter->page, 
                            $filter->limit
                        );

        $items = array_map(fn(Artist $artist) => new ArtistDto(
                            $artist->getId(),
                            $artist->getName()
                        ), (array) $pagination->getItems());

        $pagination->setItems($items);   
         
        return $pagination;
    }

    public function create(ArtistDto $artistDto): ArtistDto
    {
        $artistExist = $this->artistRepository->findOneBy(['name' => $artistDto->name]);

        if ($artistExist) {
            throw new BusinessException('Error al crear artista. Nombre Duplicado, ya existente en la base de datos.');
        }

        try {
            $artist = new Artist();
            $artist->setName($artistDto->name);
            
            $this->em->persist($artist);
            $this->em->flush();

        } catch(\Exception $e) {
            throw new BusinessException('Error al crear artista. Error en la presistencia. Messages: '.$e->getMessage());
        }

        return new ArtistDto(
                id: $artist->getId(),
                name: $artist->getName()
        );
    }

    public function save(ArtistDto $artistDto): ArtistDto
    {
        $artist = $this->artistRepository->find($artistDto->id);

        if (!$artist) {
            throw new BusinessException('No existe el artista.');
        }

        try {
            $artist->setName($artistDto->name);
            $this->em->flush();

        } catch(\Exception $e) {
            throw new BusinessException('Error al actualizar artista. Error en la presistencia. Message:'.$e->getMessage());
        }

        return ArtistDto::fromEntity($artist);
    }

    public function delete(int $id): void
    {
        $artist = $this->artistRepository->find($id);

        if (!$artist) {
            throw new BusinessException('No existe el artista.');
        }

        try {
            $this->em->remove($artist);
            $this->em->flush();

        } catch(\Exception $e) {
            throw new BusinessException('Error al eliminar artista. Error en la presistencia. Message:'.$e->getMessage());
        }
    }
}