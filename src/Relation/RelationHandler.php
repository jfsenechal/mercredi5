<?php


namespace AcMarche\Mercredi\Relation;


use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;

class RelationHandler
{
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;

    public function __construct(RelationRepository $relationRepository, EnfantRepository $enfantRepository)
    {
        $this->relationRepository = $relationRepository;
        $this->enfantRepository = $enfantRepository;
    }

    /**
     * @param Tuteur $tuteur
     * @param int|null $enfantId
     * @throws \Exception
     */
    public function handleAttachEnfant(Tuteur $tuteur, ?int $enfantId): ?Relation
    {
        if (!$enfantId) {
            throw new \Exception('Enfant non trouvé');
        }

        $enfant = $this->enfantRepository->find($enfantId);
        if (!$enfant) {
            throw new \Exception('Enfant non trouvé');
        }

        if ($this->relationRepository->checkExist($tuteur, $enfant)) {
            throw new \Exception('L\'enfant est déjà lié à cet enfant');
        }

        $relation = new Relation($tuteur, $enfant);
        $this->relationRepository->persist($relation);
        $this->relationRepository->flush();

        return $relation;
    }
}