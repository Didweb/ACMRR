<?php
namespace App\Service\RecordLabel;

use App\Entity\RecordLabel;

use App\Exception\BusinessException;
use App\DTO\RecordLabel\RecordLabelDto;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\DTO\RecordLabel\RecordLabelFilterDto;
use App\Repository\RecordLabel\RecordLabelRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;

class RecordLabelCrudService
{
    public function __construct(
        private RecordLabelRepository $recordLabelRepository,
        private PaginatorInterface $paginator,
        private EntityManagerInterface $em)
    {}

    public function getPaginated(RecordLabelFilterDto $filter): PaginationInterface
    {
        $queryBuilder = $this->recordLabelRepository->createQueryBuilder('rl')->orderBy('rl.id', 'ASC');

        $pagination = $this->paginator->paginate(
            $queryBuilder, 
            $filter->page, 
            $filter->limit
        );

        $items = array_map(fn(RecordLabel $rl) => new RecordLabelDto(
                            $rl->getId(),
                            $rl->getName()
                        ), (array) $pagination->getItems());

        $pagination->setItems($items);   
         
        return $pagination;
    }

    public function create(RecordLabelDto $recordLabelDto): RecordLabelDto
    {
        if ($this->recordLabelRepository->findOneBy(['name' => $recordLabelDto->name])) {
            throw new BusinessException('Error al crear Sello. Nombre Duplicado, ['.$recordLabelDto->name.'] ya existente en la base de datos.');
        }

        try {
            $recordLabel = new RecordLabel();
            $recordLabel->setName($recordLabelDto->name);
            
            $this->em->persist($recordLabel);
            $this->em->flush();

        } catch(\Exception $e) {
            throw new BusinessException('Error al crear Sello. Error en la presistencia.');
        }
       
        return new RecordLabelDto(
                id: $recordLabel->getId(),
                name: $recordLabel->getName()
        );
    }

    public function save(RecordLabelDto $recordLabelDto): void
    {
        $recordLabel = $this->recordLabelRepository->findOneBy(['id' => $recordLabelDto->id]);

        if(!$recordLabel) {
                throw new BusinessException('Error al grabar Sello. No existe sello.');
        }

        try {
            $this->em->persist($recordLabel);
            $this->em->flush();

        } catch(\Exception $e) {
             throw new BusinessException('Error al grabar Sello. Error en la presistencia.');
        }
    }

    public function delete(int $id): void
    {
         $recordLabel = $this->recordLabelRepository->findOneBy(['id' => $id]);

        if(!$recordLabel) {
                throw new BusinessException('Error al grabar Sello. No existe sello.');
        }

        try{

            $this->em->remove($recordLabel);
            $this->em->flush();

        } catch(\Exception $e) {
            throw new BusinessException('Error al eliminar Sello. Error en la presistencia.');
        }
    }
}