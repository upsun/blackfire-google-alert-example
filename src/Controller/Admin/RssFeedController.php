<?php

namespace App\Controller\Admin;

use App\Entity\RssFeed;
use App\Entity\User;
use App\Form\RssFeedType;
use App\Repository\RssFeedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller used to manage RSS Feeds in the backend.
 *
 * @author Florent HUCK <flovntp@gmail.com>
 */
#[Route('/admin/rss')]
#[IsGranted(User::ROLE_ADMIN)]
final class RssFeedController extends AbstractController
{
    /**
     * Lists all RssFEed entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'admin_rss_feed_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *   * 'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     */
    #[Route('/', name: 'admin_rss_feed_index', methods: ['GET'])]
    public function index(
        #[CurrentUser] User $user,
        RssFeedRepository   $RSSFeedRepository,
    ): Response {
        $rssFeeds = $RSSFeedRepository->findAll();

        return $this->render('admin/rssfeed/index.html.twig', ['rss_feeds' => $rssFeeds]);
    }

    /**
     * Creates a new RssFeed entity.
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    #[Route('/new', name: 'admin_rss_feed_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $rssFeed = new RssFeed();

        // See https://symfony.com/doc/current/form/multiple_buttons.html
        $form = $this->createForm(RssFeedType::class, $rssFeed)
            ->add('saveAndCreateNew', SubmitType::class)
        ;

        $form->handleRequest($request);

        // The isSubmitted() call is mandatory because the isValid() method
        // throws an exception if the form has not been submitted.
        // See https://symfony.com/doc/current/forms.html#processing-forms
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rssFeed);
            $entityManager->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/controller.html#flash-messages
            $this->addFlash('success', 'rss_feed.created_successfully');

            /** @var SubmitButton $submit */
            $submit = $form->get('saveAndCreateNew');

            if ($submit->isClicked()) {
                return $this->redirectToRoute('admin_rss_feed_new', [], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('admin_rss_feed_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/rssfeed/new.html.twig', [
            'rss_feed' => $rssFeed,
            'form' => $form,
        ]);
    }

    /**
     * Displays a form to edit an existing RssFeed entity.
     */
    #[Route('/{id:rssFeed}/edit', name: 'admin_rss_feed_edit', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['GET', 'POST'])]
    public function edit(Request $request, RssFeed $rssFeed, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RssFeedType::class, $rssFeed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'rss_feed.updated_successfully');

            return $this->redirectToRoute('admin_rss_feed_edit', ['id' => $rssFeed->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/rssfeed/edit.html.twig', [
            'rss_feed' => $rssFeed,
            'form' => $form,
        ]);
    }

    /**
     * Deletes a RSS Feed entity.
     */
    #[Route('/{id:rssFeed}/delete', name: 'admin_rss_feed_delete', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['POST'])]
    public function delete(Request $request, RssFeed $rssFeed, EntityManagerInterface $entityManager): Response
    {
        /** @var string|null $token */
        $token = $request->getPayload()->get('token');

        if (!$this->isCsrfTokenValid('delete', $token)) {
            return $this->redirectToRoute('admin_rss_feed_index', [], Response::HTTP_SEE_OTHER);
        }
        
        $entityManager->remove($rssFeed);
        $entityManager->flush();

        $this->addFlash('success', 'rss_feed.deleted_successfully');

        return $this->redirectToRoute('admin_rss_feed_index', [], Response::HTTP_SEE_OTHER);
    }
}
