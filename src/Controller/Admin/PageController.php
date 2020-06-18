<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Page;
use AcMarche\Mercredi\Page\Form\PageType;
use AcMarche\Mercredi\Page\Message\PageCreated;
use AcMarche\Mercredi\Page\Message\PageDeleted;
use AcMarche\Mercredi\Page\Message\PageUpdated;
use AcMarche\Mercredi\Page\Repository\PageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/page")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class PageController extends AbstractController
{
    /**
     * @var PageRepository
     */
    private $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * @Route("/", name="mercredi_admin_page_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/page/index.html.twig',
            [
                'pages' => $this->pageRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_admin_page_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->pageRepository->persist($page);
            $this->pageRepository->flush();

            $this->dispatchMessage(new PageCreated($page->getId()));

            return $this->redirectToRoute('mercredi_admin_page_show', ['id' => $page->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/page/new.html.twig',
            [
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_page_show", methods={"GET"})
     */
    public function show(Page $page): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/page/show.html.twig',
            [
                'page' => $page,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_page_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Page $page): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->pageRepository->flush();

            $this->dispatchMessage(new PageUpdated($page->getId()));

            return $this->redirectToRoute('mercredi_admin_page_show', ['id' => $page->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/page/edit.html.twig',
            [
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_page_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Page $page): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            if ($page->getSystem()) {
                $this->addFlash('danger', 'Cette page ne peut pas être supprimée');

                return $this->redirectToRoute('mercredi_admin_page_index');
            }
            $pageId = $page->getId();
            $this->pageRepository->remove($page);
            $this->pageRepository->flush();
            $this->dispatchMessage(new PageDeleted($pageId));
        }

        return $this->redirectToRoute('mercredi_admin_page_index');
    }

    /**
     * @Route("/s/sort", name="mercredi_admin_page_sort", methods={"GET","POST"})
     */
    public function trier(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $pages = $request->request->get('pages');
            if (is_array($pages)) {
                foreach ($pages as $position => $pageId) {
                    $santeQuestion = $this->pageRepository->find($pageId);
                    if ($santeQuestion) {
                        $santeQuestion->setPosition($position);
                    }
                }
                $this->pageRepository->flush();

                return new Response('<div class="alert alert-success">Tri enregistré</div>');
            }

            return new Response('<div class="alert alert-error">Erreur</div>');
        }

        $pages = $this->pageRepository->findAll();

        return $this->render(
            '@AcMarcheMercrediAdmin/page/sort.html.twig',
            [
                'pages' => $pages,
            ]
        );
    }
}
