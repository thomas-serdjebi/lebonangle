<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/advert')]
class AdvertController extends AbstractController
{
    #[Route('/', name: 'app_advert_index', methods: ['GET'])]
    public function index(AdvertRepository $advertRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $adverts = $advertRepository->findAll();

        $advertsPages = $paginator->paginate($adverts, $request->query->getInt('page', 1), 30);

        return $this->render('advert/index.html.twig', [
            'advertsPages' => $advertsPages,
        ]);
    }


    #[Route('/new', name: 'app_advert_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $advert = new Advert();
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($advert);
            $entityManager->flush();

            return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('advert/new.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_advert_show', methods: ['GET'])]
    public function show(Advert $advert): Response
    {
        foreach ($advert as $ad )  {
            var_dump($ad);
        }
        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_advert_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Advert $advert, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_advert_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Advert $advert, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
            $entityManager->remove($advert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/publish', name: 'app_advert_publish', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function publish(Advert $advert, EntityManagerInterface $entityManager): Response
    {
        $advert->setState('published');
        $advert->setPublishedAt(new \DateTime());
        $entityManager->flush();

        return $this->redirectToRoute('app_advert_show', ['id' => $advert->getId()]);
    }   

    #[Route('/{id}/reject', name: 'app_advert_reject', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function reject(Advert $advert, EntityManagerInterface $entityManager): Response
    {
        $advert->setState('rejected');
        $advert->setPublishedAt(null);
        $entityManager->flush();

        return $this->redirectToRoute('app_advert_show', ['id' => $advert->getId()]);
    }

}

