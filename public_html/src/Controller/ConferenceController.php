<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use App\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ConferenceController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
     * @param SpamChecker $spamChecker
     * @param string $photoDir
     * @return Response
     */
    #[Route('/conference/{slug}', name: 'conference')]
    public function show(string            $slug, Request $request, ConferenceRepository $conferenceRepository,
                         CommentRepository $commentRepository, SpamChecker $spamChecker, string $photoDir): Response
    {
        $conference = $conferenceRepository->findOneBy(['slug' => $slug]);
        if (!$conference) {
            $this->redirectToRoute('homepage');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);
            if ($photo = $form['photo']->getData()) {
                try {
                    $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                    $photo->move($photoDir, $filename);
                    $comment->setPhotoFilename($filename);
                } catch (Exception $e) {
                    $this->addFlash(
                        'error',
                        'Unable to upload the photo:' . $e->getMessage()
                    );
                }
            }

            $this->entityManager->persist($comment);

            try {
                // проверка на спам
                $context = [
                    'user_ip' => $request->getClientIp(),
                    'user_agent' => $request->headers->get('user-agent'),
                    'referrer' => $request->headers->get('referer'),
                    'permalink' => $request->getUri(),
                ];
                if (2 === $spamChecker->getSpamScore($comment, $context)) {
                    throw new \RuntimeException('Blatant spam, go away!');
                }
            } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }

            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Comment was saved'
            );
            return $this->redirectToRoute('conference', ['slug' => $slug]);
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return $this->render('conference/show.html.twig', [
            'conference' => $conferenceRepository->findOneBy(['slug' => $slug]),
            /*'comments' => $commentRepository->findBy(['conference' => $id], ['createdAt' => 'DESC'])*/
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'comment_form' => $form->createView(),
        ]);
    }
}
