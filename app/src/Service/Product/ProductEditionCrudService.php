<?php
namespace App\Service\Product;

use App\Entity\Track;
use App\Entity\Artist;
use App\Entity\Riddim;
use App\Entity\RecordLabel;
use App\Entity\ProductEdition;
use App\Exception\BusinessException;
use App\DTO\Product\ProductEditionDto;
use App\Repository\Artist\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\ValueObject\Product\ProductFormat;
use App\ValueObject\Product\ProductBarcode;
use App\Repository\Product\ProductRepository;
use App\Repository\Product\ProductEditionRepository;
use App\Repository\RecordLabel\RecordLabelRepository;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ProductEditionCrudService
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductEditionRepository $productEditionRepository,
        private RecordLabelRepository $recordLabelRepository,
        private ArtistRepository $artistRepository,
        private EntityManagerInterface $em,
        private CsrfTokenManagerInterface $csrfTokenManager) 
    {}

    public function create(ProductEditionDto $productEditionDto): ProductEditionDto
    {
   
        try {
            $productEdition = $this->generateEntity($productEditionDto);

            $this->em->persist($productEdition);
            $this->em->flush();

        } catch(\Exception $e) {
            throw new BusinessException('[create] Error al crear Producto Edition. Error en la presistencia: '.$e->getMessage().json_encode($productEditionDto));
        }

        return  ProductEditionDto::fromEntity($productEdition);
    }

    public function save(ProductEditionDto $productEditionDto): ProductEditionDto
    {
        $productEdition = $this->generateEntity($productEditionDto);

        try {
            $this->em->persist($productEdition);
            $this->em->flush();

        } catch(\Exception $e) {
                    throw new BusinessException('[save] Error al crear Producto Edition. Error en la presistencia.'.$e->getMessage());
                }
        return  ProductEditionDto::fromEntity($productEdition);
    }

    public function generateEntity($productEditionDto): ProductEdition
    {

        $productTitle = $this->productRepository->find(['id' => $productEditionDto->title['id']]);

        if(!$productTitle) {
            throw new BusinessException('Error al generera entidad. Product Title no se encuentra.');
        }

        $label = $this->recordLabelRepository->find($productEditionDto->label);
        if (!$label) {
            throw new BusinessException('Sello no encontrado con ID: ' . $productEditionDto->label);
        }

        $barcode = ProductBarcode::generate();

        $artists = [];
        foreach ($productEditionDto->artists as $artistDto) {
            $artist = $this->artistRepository->find($artistDto['id']);
            if (!$artist) {
                throw new BusinessException('Artista no encontrado con ID: ' . $artistDto['id']);
            }
            $artists[] = $artist;
        }

        $tracks = [];
        foreach($productEditionDto->tracks as $track) {
            $tracks[] = new Track(
                            $track['id'],
                            $track['title'],
                            new Riddim($track['riddim'])
                        );
        }

        $productEdition = new ProductEdition(); 
        $productEdition->setTitle($productTitle);
        $productEdition->setLabel($label);
        $productEdition->setYear($productEditionDto->year);
        $productEdition->setFormat(new ProductFormat($productEditionDto->format));
        $productEdition->setBarcode($barcode);
        $productEdition->setStockNew($productEditionDto->stockNew);
        $productEdition->setPriceNew($productEditionDto->priceNew);

        foreach($artists as $artist) {
            $productEdition->addArtist($artist);
        }
        
        foreach($tracks as $track) {
            $productEdition->addTrack($track);
        }

        return $productEdition;
    }
}