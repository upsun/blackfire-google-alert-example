<?php

namespace App\Services;


use App\Entity\Feed;
use App\Repository\FeedRepository;
use Doctrine\ORM\EntityManagerInterface;

class BlackfireGoogleAlerts
{
    public function __construct(
        private string                 $rssFeed,
        private EntityManagerInterface $entityManager,
        private FeedRepository         $feedRepository,
        private BlackfireService       $blackfireService
    )
    {
    }

    public function processRssFeed(): void
    {
        $rssFeeds = simplexml_load_file($this->rssFeed);
        foreach ($rssFeeds->entry as $entry) {
            $googleId = $entry->id;
            if ($this->feedRepository->findBy(['googleId' => $googleId]) == null) {
                $feed = $this->createFeed($entry);
                $this->blackfireService->addBlackfireMarker($feed);
            }
        }

        $this->entityManager->flush();
    }

    private function createFeed(\SimpleXMLElement $entry): Feed
    {
        $feed = new Feed();
        $feed->setGoogleId($entry->id);
        $feed->setTitle($this->blackfireService->sanitizeString($entry->title));
        $feed->setLink($this->extractFinalUrl($entry));
        $feed->setPublished(new \DateTime($entry->published));
        $feed->setUpdated(new \DateTime($entry->updated));
        $feed->setContent($entry->content);
        $feed->setAuthor($entry->author->name);

        $this->entityManager->persist($feed);

        return $feed;
    }

    private function extractFinalUrl($entry): string
    {
        $link = $entry->link['href'];
        $query_str = parse_url($link, PHP_URL_QUERY);
        parse_str($query_str, $query_params);
        return $query_params['url'];
    }

}