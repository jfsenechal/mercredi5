<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Ecole\Form\EcoleType;
use AcMarche\Mercredi\Ecole\Message\EcoleCreated;
use AcMarche\Mercredi\Ecole\Message\EcoleDeleted;
use AcMarche\Mercredi\Ecole\Message\EcoleUpdated;
use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Ecole;
use function count;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ecole")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class EcoleController extends AbstractController
{
    /**
     * @var string
     */
    private const MERCREDI_ADMIN_ECOLE_SHOW = 'mercredi_admin_ecole_show';
    /**
     * @var string
     */
    private const ID = 'id';
    /**
     * @var string
     */
    private const ECOLE = 'ecole';
    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;

    public function __construct(EcoleRepository $ecoleRepository, EnfantRepository $enfantRepository)
    {
        $this->ecoleRepository = $ecoleRepository;
        $this->enfantRepository = $enfantRepository;
    }

    /**
     * @Route("/", name="mercredi_admin_ecole_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/ecole/index.html.twig',
            [
                'ecoles' => $this->ecoleRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_admin_ecole_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ecole = new Ecole();
        $form = $this->createForm(EcoleType::class, $ecole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ecoleRepository->persist($ecole);
            $this->ecoleRepository->flush();

            $this->dispatchMessage(new EcoleCreated($ecole->getId()));

            return $this->redirectToRoute(self::MERCREDI_ADMIN_ECOLE_SHOW, [self::ID => $ecole->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/ecole/new.html.twig',
            [
                self::ECOLE => $ecole,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_ecole_show", methods={"GET"})
     */
    public function show(Ecole $ecole): Response
    {
        $enfants = $this->enfantRepository->findByEcoles([$ecole]);

        return $this->render(
            '@AcMarcheMercrediAdmin/ecole/show.html.twig',
            [
                self::ECOLE => $ecole,
                'enfants' => $enfants,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_ecole_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ecole $ecole): Response
    {
        $form = $this->createForm(EcoleType::class, $ecole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ecoleRepository->flush();

            $this->dispatchMessage(new EcoleUpdated($ecole->getId()));

            return $this->redirectToRoute(self::MERCREDI_ADMIN_ECOLE_SHOW, [self::ID => $ecole->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/ecole/edit.html.twig',
            [
                self::ECOLE => $ecole,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_ecole_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ecole $ecole): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ecole->getId(), $request->request->get('_token'))) {
            if (count($this->enfantRepository->findByEcoles([$ecole])) > 0) {
                $this->addFlash('danger', 'L\'école contient des enfants et ne peut être supprimée');

                return $this->redirectToRoute(self::MERCREDI_ADMIN_ECOLE_SHOW, [self::ID => $ecole->getId()]);
            }
            $ecoleId = $ecole->getId();
            $this->ecoleRepository->remove($ecole);
            $this->ecoleRepository->flush();
            $this->dispatchMessage(new EcoleDeleted($ecoleId));
        }

        return $this->redirectToRoute('mercredi_admin_ecole_index');
    }
}
