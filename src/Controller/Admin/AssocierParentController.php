<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Dto\AssociateUserTuteurDto;
use AcMarche\Mercredi\User\Form\AssociateParentType;
use AcMarche\Mercredi\User\Handler\AssociationHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * User controller.
 *
 * @Route("/security/associer/parent")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class AssocierParentController extends AbstractController
{
    /**
     * @var string
     */
    private const MERCREDI_ADMIN_USER_SHOW = 'mercredi_admin_user_show';
    /**
     * @var string
     */
    private const ID = 'id';
    /**
     * @var AssociationHandler
     */
    private $associationHandler;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;

    public function __construct(
        AssociationHandler $associationHandler,
        TuteurRepository $tuteurRepository
    ) {
        $this->associationHandler = $associationHandler;
        $this->tuteurRepository = $tuteurRepository;
    }

    /**
     * @Route("/{id}", name="mercredi_user_associate_parent", methods={"GET","POST"})
     */
    public function associate(Request $request, User $user)
    {
        if (! $user->isParent()) {
            $this->addFlash('danger', 'Le compte n\'a pas le rôle de parent');

            return $this->redirectToRoute(self::MERCREDI_ADMIN_USER_SHOW, [self::ID => $user->getId()]);
        }

        $associateUserTuteurDto = new AssociateUserTuteurDto($user);
        $this->associationHandler->suggestTuteur($user, $associateUserTuteurDto);

        $form = $this->createForm(AssociateParentType::class, $associateUserTuteurDto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->associationHandler->handleAssociateParent($associateUserTuteurDto);

            return $this->redirectToRoute(self::MERCREDI_ADMIN_USER_SHOW, [self::ID => $user->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/associer_parent.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_user_dissociate_parent", methods={"DELETE"})
     */
    public function dissociate(Request $request, User $user)
    {
        if ($this->isCsrfTokenValid('dissociate'.$user->getId(), $request->request->get('_token'))) {
            $tuteurId = (int) $request->request->get('tuteur');
            if (0 === $tuteurId) {
                $this->addFlash('danger', 'Le parent n\'a pas été trouvé');

                return $this->redirectToRoute(self::MERCREDI_ADMIN_USER_SHOW, [self::ID => $user->getId()]);
            }

            $tuteur = $this->tuteurRepository->find($tuteurId);
            $this->associationHandler->handleDissociateParent($user, $tuteur);

            return $this->redirectToRoute('mercredi_admin_tuteur_show', [self::ID => $tuteur->getId()]);
        }

        return $this->redirectToRoute(self::MERCREDI_ADMIN_USER_SHOW, [self::ID => $user->getId()]);
    }
}
