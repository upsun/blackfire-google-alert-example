<?php

namespace App\Controller\Admin;

use App\Entity\Marker;
use App\Entity\User;
use App\Form\MarkerType;
use App\Repository\FeedRepository;
use App\Repository\MarkerRepository;
use App\Repository\RssFeedRepository;
use App\Services\BlackfireService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller used to add markers on your Blackfire timeline from the backend.
 *
 * Please note that this application backend is just to give you an example on how to interact with your Blackfire timeline
 * And is not meant to be maintained or supported by Platform.sh
 * 
 * Please also note that as soon as a marker is added on your Blackfire timeline,
 * there is no way to delete them using Blackfire API
 * 
 * @author Florent HUCK <flovntp@gmail.com>
 */
#[Route('/admin/marker')]
#[IsGranted(User::ROLE_ADMIN)]
final class MarkerController extends AbstractController
{
    /**
     * Lists all (manual) Blackfire Markers entities.
     */
    #[Route('/', name: 'admin_index', methods: ['GET'])]
    #[Route('/', name: 'admin_marker_index', methods: ['GET'])]
    public function index(
        MarkerRepository $markers,
        FeedRepository $feeds,
        RssFeedRepository $rssFeedRepository,
    ): Response {
        $markers = $markers->findBy([], ['date' => 'DESC']);
        
        $rssFeeds = $rssFeedRepository->findBy(['active' => true]);
        
        return $this->render('admin/marker/index.html.twig', ['markers' => $markers, 'rssFeeds' => $rssFeeds]);
    }

    /**
     * Creates a new Marker entity.
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    #[Route('/new', name: 'admin_marker_new', methods: ['GET', 'POST'])]
    public function new(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        BlackfireService $blackfireService,
    ): Response {
        $marker = new Marker();
        $marker->setAuthor($user->getUsername());

        // See https://symfony.com/doc/current/form/multiple_buttons.html
        $form = $this->createForm(MarkerType::class, $marker)
            ->add('saveAndCreateNew', SubmitType::class)
        ;

        $form->handleRequest($request);

        // The isSubmitted() call is mandatory because the isValid() method
        // throws an exception if the form has not been submitted.
        // See https://symfony.com/doc/current/forms.html#processing-forms
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($marker);
            $entityManager->flush();

            // Add corresponding Blackfire marker
            $blackfireService->addBlackfireMarker($marker);
            
            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/controller.html#flash-messages
            $this->addFlash('success', 'marker.created_successfully');

            /** @var SubmitButton $submit */
            $submit = $form->get('saveAndCreateNew');

            if ($submit->isClicked()) {
                return $this->redirectToRoute('admin_marker_new', [], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('admin_marker_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/marker/new.html.twig', [
            'marker' => $marker,
            'form' => $form,
        ]);
    }
}
