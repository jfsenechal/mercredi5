<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Form\RelationType;
use AcMarche\Mercredi\Relation\Message\RelationCreated;
use AcMarche\Mercredi\Relation\Message\RelationDeleted;
use AcMarche\Mercredi\Relation\Message\RelationUpdated;
use AcMarche\Mercredi\Relation\RelationHandler;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/relation")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class RelationController extends AbstractController
{
    /**
     * @var string
     */
    private const DANGER = 'danger';
    /**
     * @var string
     */
    private const ID = 'id';
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var RelationHandler
     */
    private $relationHandler;

    public function __construct(RelationRepository $relationRepository, RelationHandler $relationHandler)
    {
        $this->relationRepository = $relationRepository;
        $this->relationHandler = $relationHandler;
    }

    /**
     * @Route("/attach/enfant/{id}", name="mercredi_admin_relation_attach_enfant", methods={"POST"})
     */
    public function attachEnfant(Request $request, Tuteur $tuteur): Response
    {
        if ($this->isCsrfTokenValid('attachEnfant'.$tuteur->getId(), $request->request->get('_token'))) {
            $enfantId = (int) $request->request->get('enfantId');

            try {
                $relation = $this->relationHandler->handleAttachEnfant($tuteur, $enfantId);
                $this->dispatchMessage(new RelationCreated($relation->getId()));
            } catch (Exception $e) {
                $this->addFlash(self::DANGER, $e->getMessage());

                return $this->redirectToRoute('mercredi_admin_tuteur_show', [self::ID => $tuteur->getId()]);
            }
        } else {
            $this->addFlash(self::DANGER, 'Formulaire non valide');
        }

        return $this->redirectToRoute('mercredi_admin_tuteur_show', [self::ID => $tuteur->getId()]);
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_relation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Relation $relation): Response
    {
        $form = $this->createForm(RelationType::class, $relation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->relationRepository->flush();

            $this->dispatchMessage(new RelationUpdated($relation->getId()));

            return $this->redirectToRoute('mercredi_admin_enfant_show', [self::ID => $relation->getEnfant()->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/relation/edit.html.twig',
            [
                'relation' => $relation,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/", name="mercredi_admin_relation_delete", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        $relationId = $request->request->get('relationid');

        if (! $relationId) {
            $this->addFlash(self::DANGER, 'Relation non trouvée');

            return $this->redirectToRoute('mercredi_admin_home');
        }
        $relation = $this->relationRepository->find($relationId);
        if (null === $relation) {
            $this->addFlash(self::DANGER, 'Relation non trouvée');

            return $this->redirectToRoute('mercredi_admin_home');
        }

        $tuteur = $relation->getTuteur();

        if ($this->isCsrfTokenValid('delete'.$relation->getId(), $request->request->get('_token'))) {
            $this->relationRepository->remove($relation);
            $this->relationRepository->flush();
            $this->dispatchMessage(new RelationDeleted($relationId));
        }

        return $this->redirectToRoute('mercredi_admin_tuteur_show', [self::ID => $tuteur->getId()]);
    }
}
