<?php

namespace AcMarche\Mercredi\Sante\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Sante\Repository\SanteReponseRepository;

class SanteChecker
{
    /**
     * @var SanteQuestionRepository
     */
    private $santeQuestionRepository;
    /**
     * @var SanteReponseRepository
     */
    private $santeReponseRepository;

    public function __construct(
        SanteQuestionRepository $santeQuestionRepository,
        SanteReponseRepository $santeReponseRepository
    ) {
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->santeReponseRepository = $santeReponseRepository;
    }

    public function identiteEnfantIsComplete(Enfant $enfant)
    {
        if (!$enfant->getNom()) {
            return false;
        }

        if (!$enfant->getPrenom()) {
            return false;
        }

        if (!$enfant->getEcole()) {
            return false;
        }

        if (!$enfant->getAnneeScolaire()) {
            return false;
        }

        return true;
    }

    public function isComplete(SanteFiche $santeFiche): bool
    {
        $reponses = $this->santeReponseRepository->findBySanteFiche($santeFiche);
        $questions = $this->santeQuestionRepository->findAll();

        if (count($reponses) < count($questions)) {
            return false;
        }

        foreach ($reponses as $reponse) {
            $question = $reponse->getQuestion();
            if (!$this->checkQuestionOk($question)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function checkQuestionOk(SanteQuestion $question)
    {
        if ($question->getComplement()) {
            if ($question->getReponseTxt()) {
                if (trim('' == $question->getRemarque())) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param Enfant[] $enfants
     */
    public function isCompleteForEnfants(array $enfants)
    {
        foreach ($enfants as $enfant) {
            if ($this->isComplete($enfant)) {
                $enfant->setSanteFicheComplete(true);
            }
        }
    }
}