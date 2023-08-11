<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\Article1Type;
use App\Repository\ArticleRepository;
use App\Service\PicturesService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->getAllArticle(),
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger, PicturesService $picturesService, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        // Récupérer l'utilisateur actuellement connecté
        $article->setUser($this->getUser());
        $form = $this->createForm(Article1Type::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();
            $article->setSlug('ICI LE SLUG');
            $article->setCreatedAt(new DateTimeImmutable());
            // $article->setUser();

            // this condition is needed because the 'brochure' field is not required

            // so the PDF file must be processed only when a file is uploaded
            if ($image) {


                $newFilename = $picturesService->add($image);
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $article->setImage($newFilename);
                // ... persist the $product variable or any other work

            }


            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        $user = $article->getUser();
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'user' => $user,
        ]);
    }
}
