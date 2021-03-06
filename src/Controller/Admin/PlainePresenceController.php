<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Plaine\Calculator\PlaineCalculatorInterface;
use AcMarche\Mercredi\Plaine\Dto\PlainePresencesDto;
use AcMarche\Mercredi\Plaine\Form\PlainePresenceEditType;
use AcMarche\Mercredi\Plaine\Form\PlainePresencesEditType;
use AcMarche\Mercredi\Plaine\Handler\PlainePresenceHandler;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Utils\PlaineUtils;
use AcMarche\Mercredi\Presence\Message\PresenceUpdated;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Search\Form\SearchNameType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plaine/presence")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class PlainePresenceController extends AbstractController
{
    /**
     * @var string
     */
    private const DANGER = 'danger';
    /**
     * @var string
     */
    private const PLAINE = 'plaine';
    /**
     * @var string
     */
    private const FORM = 'form';
    /**
     * @var string
     */
    private const ENFANT = 'enfant';
    /**
     * @var string
     */
    private const MERCREDI_ADMIN_PLAINE_PRESENCE_SHOW = 'mercredi_admin_plaine_presence_show';
    /**
     * @var string
     */
    private const SUCCESS = 'success';
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var PlainePresenceHandler
     */
    private $plainePresenceHandler;
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var PlainePresenceRepository
     */
    private $plainePresenceRepository;
    /**
     * @var PlaineCalculatorInterface
     */
    private $plaineCalculator;

    public function __construct(
        PlainePresenceHandler $plainePresenceHandler,
        EnfantRepository $enfantRepository,
        RelationRepository $relationRepository,
        PlainePresenceRepository $plainePresenceRepository,
        PlaineCalculatorInterface $plaineCalculator
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->plainePresenceHandler = $plainePresenceHandler;
        $this->relationRepository = $relationRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->plaineCalculator = $plaineCalculator;
    }

    /**
     * @Route("/new/{id}", name="mercredi_admin_plaine_presence_new", methods={"GET","POST"})
     */
    public function new(Request $request, Plaine $plaine): Response
    {
        if (0 === count($plaine->getPlaineJours())) {
            $this->addFlash(self::DANGER, 'La plaine n\'a aucune date');

            return $this->redirectToRoute('mercredi_admin_plaine_show', ['id' => $plaine->getId()]);
        }

        $nom = null;
        $form = $this->createForm(SearchNameType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $form->get('nom')->getData();
        }

        $enfants = $nom ? $this->enfantRepository->findByName($nom) : $this->enfantRepository->findAllActif();

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_presence/new.html.twig',
            [
                'enfants' => $enfants,
                self::PLAINE => $plaine,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/select/tuteur/{plaine}/{enfant}", name="mercredi_admin_plaine_presence_select_tuteur", methods={"GET","POST"})
     *
     * @Entity("plaine", expr="repository.find(plaine)")
     * @Entity("enfant", expr="repository.find(enfant)")
     */
    public function selectTuteur(Plaine $plaine, Enfant $enfant): Response
    {
        $tuteurs = $this->relationRepository->findTuteursByEnfant($enfant);
        if (1 === count($tuteurs)) {
            $tuteur = $tuteurs[0];

            return $this->redirectToRoute(
                'mercredi_admin_plaine_presence_confirmation',
                [
                    self::PLAINE => $plaine->getId(),
                    'tuteur' => $tuteur->getId(),
                    self::ENFANT => $enfant->getId(),
                ]
            );
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_presence/select_tuteur.html.twig',
            [
                self::ENFANT => $enfant,
                self::PLAINE => $plaine,
                'tuteurs' => $tuteurs,
            ]
        );
    }

    /**
     * @Route("/confirmation/{plaine}/{tuteur}/{enfant}", name="mercredi_admin_plaine_presence_confirmation", methods={"GET","POST"})
     *
     * @Entity("tuteur", expr="repository.find(tuteur)")
     * @Entity("plaine", expr="repository.find(plaine)")
     * @Entity("enfant", expr="repository.find(enfant)")
     */
    public function confirmation(Plaine $plaine, Tuteur $tuteur, Enfant $enfant): Response
    {
        $this->plainePresenceHandler->handleAddEnfant($plaine, $tuteur, $enfant);

        $this->addFlash('success', "L'enfant a bien été ajouté");

        return $this->redirectToRoute(
            self::MERCREDI_ADMIN_PLAINE_PRESENCE_SHOW,
            [
                self::PLAINE => $plaine->getId(),
                self::ENFANT => $enfant->getId(),
            ]
        );
    }

    /**
     * @Route("/{plaine}/{enfant}", name="mercredi_admin_plaine_presence_show", methods={"GET"})
     */
    public function show(Plaine $plaine, Enfant $enfant): Response
    {
        $presences = $this->plainePresenceRepository->findPrecencesByPlaineAndEnfant($plaine, $enfant);
        $cout = $this->plaineCalculator->calculate($plaine, $enfant);

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_presence/show.html.twig',
            [
                self::PLAINE => $plaine,
                self::ENFANT => $enfant,
                'presences' => $presences,
                'cout' => $cout,
            ]
        );
    }

    /**
     * @Route("/{plaine}/{presence}/edit", name="mercredi_admin_plaine_presence_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Plaine $plaine, Presence $presence): Response
    {
        $enfant = $presence->getEnfant();
        $form = $this->createForm(PlainePresenceEditType::class, $presence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->plainePresenceHandler->handleEditPresence();

            $this->dispatchMessage(new PresenceUpdated($presence->getId()));

            return $this->redirectToRoute(
                self::MERCREDI_ADMIN_PLAINE_PRESENCE_SHOW,
                [
                    self::PLAINE => $plaine->getId(),
                    self::ENFANT => $enfant->getId(),
                ]
            );
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_presence/edit.html.twig',
            [
                self::PLAINE => $plaine,
                'presence' => $presence,
                self::ENFANT => $enfant,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{plaine}/{enfant}/jours", name="mercredi_admin_plaine_presence_jours", methods={"GET","POST"})
     */
    public function jours(Request $request, Plaine $plaine, Enfant $enfant): Response
    {
        $jours = PlaineUtils::extractJoursFromPlaine($plaine);
        $plainePresencesDto = new PlainePresencesDto($plaine, $enfant, $jours);

        $presences = $this->plainePresenceHandler->findPresencesByPlaineEnfant($plaine, $enfant);
        $currentJours = PresenceUtils::extractJours($presences);
        $plainePresencesDto->setJours($currentJours);

        $form = $this->createForm(PlainePresencesEditType::class, $plainePresencesDto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new = $plainePresencesDto->getJours();
            if (0 === count($presences)) {
                $tuteurs = $this->relationRepository->findTuteursByEnfant($enfant);
                $tuteur = $tuteurs[0];
            } else {
                //todo bad
                $tuteur = $presences[0]->getTuteur();
            }

            $this->plainePresenceHandler->handleEditPresences($tuteur, $enfant, $currentJours, $new);
            $this->addFlash(self::SUCCESS, 'Les présences ont bien été modifiées');

            return $this->redirectToRoute(
                self::MERCREDI_ADMIN_PLAINE_PRESENCE_SHOW,
                [
                    self::PLAINE => $plaine->getId(),
                    self::ENFANT => $enfant->getId(),
                ]
            );
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_presence/edit_presences.html.twig',
            [
                self::PLAINE => $plaine,
                self::ENFANT => $enfant,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_plaine_presence_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Plaine $plaine): Response
    {
        if ($this->isCsrfTokenValid('deletePresence'.$plaine->getId(), $request->request->get('_token'))) {
            $presenceId = (int) $request->request->get('presence');
            if (0 === $presenceId) {
                $this->addFlash(self::DANGER, 'Référence à la présence non trouvée');

                return $this->redirectToRoute('mercredi_admin_plaine_index');
            }
            $presence = $this->plainePresenceHandler->findPresence($presenceId);
            if (null === $presence) {
                $this->addFlash(self::DANGER, 'Présence non trouvée');

                return $this->redirectToRoute('mercredi_admin_plaine_index');
            }
            $enfant = $presence->getEnfant();
            $this->plainePresenceHandler->remove($presence);

            $this->addFlash(self::SUCCESS, 'La présence à bien été supprimée');
        }

        return $this->redirectToRoute(
            self::MERCREDI_ADMIN_PLAINE_PRESENCE_SHOW,
            [self::PLAINE => $plaine->getId(), self::ENFANT => $enfant->getId()]
        );
    }

    /**
     * @Route("/{plaine}/{enfant}", name="mercredi_admin_plaine_presence_remove_enfant", methods={"DELETE"})
     */
    public function remove(Request $request, Plaine $plaine, Enfant $enfant): Response
    {
        if ($this->isCsrfTokenValid('remove'.$plaine->getId(), $request->request->get('_token'))) {
            $this->plainePresenceHandler->removeEnfant($plaine, $enfant);
            $this->addFlash(self::SUCCESS, 'L\'enfant a été retiré de la plaine');
        }

        return $this->redirectToRoute('mercredi_admin_plaine_show', ['id' => $plaine->getId()]);
    }
}
