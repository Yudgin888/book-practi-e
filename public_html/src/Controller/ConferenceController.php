<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    /**
     * @param ConferenceRepository $conferenceRepository
     * @return Response
     */
    #[Route('/', name: 'homepage')]
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        return $this->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findAll()
        ]);
    }

    /**
     * @param string $slug
     * @param Request $request
     * @param ConferenceRepository $conferenceRepository
     * @param CommentRepository $commentRepository
     * @return Response
     */
    #[Route('/conference/{slug}', name: 'conference')]
    public function show(string $slug, Request $request, ConferenceRepository $conferenceRepository, CommentRepository $commentRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $conference = $conferenceRepository->findOneBy(['slug' => $slug]);
        if (!$conference) {
            $this->redirectToRoute('homepage');
        }
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return $this->render('conference/show.html.twig', [
            'conference' => $conferenceRepository->findOneBy(['slug' => $slug]),
            /*'comments' => $commentRepository->findBy(['conference' => $id], ['createdAt' => 'DESC'])*/
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        ]);
    }
}
