<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Entity\User;
use AcMarche\Mercredi\Utilisateur\Form\UtilisateurEditType;
use AcMarche\Mercredi\Utilisateur\Form\UtilisateurType;
use AcMarche\Mercredi\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/utilisateur")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class UtilisateurController extends AbstractController
{
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Lists all Utilisateur entities.
     *
     * @Route("/", name="admin_mercredi_utilisateur_index", methods={"GET"})
     *
     */
    public function index()
    {
        $users = $this->userRepository->findBy([], ['nom' => 'ASC']);

        return $this->render(
            '@AcMarcheMercrediAdmin/utilisateur/index.html.twig',
            array(
                'users' => $users,
            )
        );
    }

    /**
     * Displays a form to create a new Utilisateur utilisateur.
     *
     * @Route("/new", name="admin_mercredi_utilisateur_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request)
    {
        $utilisateur = new User();

        $form = $this->createForm(UtilisateurType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setPassword(
                $this->passwordEncoder->encodePassword($utilisateur, $form->getData()->getPlainPassword())
            );
            $this->userRepository->insert($utilisateur);

            $this->addFlash("success", "L'utilisateur a bien été ajouté");

            return $this->redirectToRoute('admin_mercredi_utilisateur');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/utilisateur/new.html.twig',
            array(
                'utilisateur' => $utilisateur,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Utilisateur utilisateur.
     *
     * @Route("/{id}", name="admin_mercredi_utilisateur_show", methods={"GET"})
     *
     */
    public function show(User $utilisateur)
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/utilisateur/show.html.twig',
            array(
                'utilisateur' => $utilisateur,
            )
        );
    }

    /**
     * Displays a form to edit an existing Utilisateur utilisateur.
     *
     * @Route("/{id}/edit", name="admin_mercredi_utilisateur_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, User $utilisateur)
    {
        $editForm = $this->createForm(UtilisateurEditType::class, $utilisateur);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->userRepository->flush();
            $this->addFlash("success", "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('admin_mercredi_utilisateur');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/utilisateur/edit.html.twig',
            array(
                'utilisateur' => $utilisateur,
                'form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Utilisateur utilisateur.
     *
     * @Route("/{id}", name="admin_mercredi_utilisateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $this->userRepository->remove($user);
            $this->userRepository->flush();
            $this->addFlash('success', 'L\'utilisateur a été supprimé');
        }

        return $this->redirectToRoute('admin_mercredi_utilisateur');
    }

}
