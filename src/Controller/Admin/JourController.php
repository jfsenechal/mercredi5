<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Form\JourType;
use AcMarche\Mercredi\Jour\Message\JourCreated;
use AcMarche\Mercredi\Jour\Message\JourDeleted;
use AcMarche\Mercredi\Jour\Message\JourUpdated;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Jour\Tarification\Form\TarificationFormGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/jour")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class JourController extends AbstractController
{
    /**
     * @var string
     */
    private const ID = 'id';
    /**
     * @var string
     */
    private const JOUR = 'jour';
    /**
     * @var string
     */
    private const FORM = 'form';
    /**
     * @var JourRepository
     */
    private $jourRepository;
    /**
     * @var TarificationFormGeneratorInterface
     */
    private $tarificationFormGenerator;

    public function __construct(
        JourRepository $jourRepository,
        TarificationFormGeneratorInterface $tarificationFormGenerator
    ) {
        $this->jourRepository = $jourRepository;
        $this->tarificationFormGenerator = $tarificationFormGenerator;
    }

    /**
     * @Route("/", name="mercredi_admin_jour_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/jour/index.html.twig',
            [
                'jours' => $this->jourRepository->findNotArchived(),
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_admin_jour_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $jour = new Jour();
        $form = $this->createForm(JourType::class, $jour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->jourRepository->persist($jour);
            $this->jourRepository->flush();

            $this->dispatchMessage(new JourCreated($jour->getId()));

            return $this->redirectToRoute('mercredi_admin_jour_tarif', [self::ID => $jour->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/jour/new.html.twig',
            [
                self::JOUR => $jour,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/tarif/{id}", name="mercredi_admin_jour_tarif", methods={"GET","POST"})
     */
    public function tarif(Request $request, Jour $jour): Response
    {
        $form = $this->tarificationFormGenerator->generateForm($jour);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->jourRepository->persist($jour);
            $this->jourRepository->flush();

            $this->dispatchMessage(new JourCreated($jour->getId()));

            return $this->redirectToRoute('mercredi_admin_jour_show', [self::ID => $jour->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/jour/tarif.html.twig',
            [
                self::JOUR => $jour,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_jour_show", methods={"GET"})
     */
    public function show(Jour $jour): Response
    {
        $tarifs = $this->tarificationFormGenerator->generateTarifsHtml($jour);

        return $this->render(
            '@AcMarcheMercrediAdmin/jour/show.html.twig',
            [
                self::JOUR => $jour,
                'tarifs' => $tarifs,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_jour_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Jour $jour): Response
    {
        $form = $this->createForm(JourType::class, $jour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->jourRepository->flush();
            //todo switch pedagogique

            $this->dispatchMessage(new JourUpdated($jour->getId()));

            return $this->redirectToRoute('mercredi_admin_jour_show', [self::ID => $jour->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/jour/edit.html.twig',
            [
                self::JOUR => $jour,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_jour_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Jour $jour): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jour->getId(), $request->request->get('_token'))) {
            $jourId = $jour->getId();
            $this->jourRepository->remove($jour);
            $this->jourRepository->flush();
            $this->dispatchMessage(new JourDeleted($jourId));
        }

        return $this->redirectToRoute('mercredi_admin_jour_index');
    }
}
