<?php

namespace App\Services;


use App\Entity\Feed;
use App\Repository\FeedRepository;
use Doctrine\ORM\EntityManagerInterface;

class BlackfireGoogleAlert
{
    public function __construct(
        private string                 $rssFeed,
        private EntityManagerInterface $entityManager,
        private FeedRepository         $feedRepository,
        private BlackfireService       $blackfireService
    )
    {
    }

    public function processRssFeed(): int
    {
        $rssFeeds = simplexml_load_file($this->rssFeed);
        foreach ($rssFeeds->entry as $entry) {
            $googleId = $entry->id;
            if ($this->feedRepository->findBy(['googleId' => $googleId]) == null) {
                $feed = $this->createFeed($entry);
                $this->blackfireService->addBlackfireMarker($feed);
                $this->setFeedAsSpoted($feed);
            }
        }

        $this->entityManager->flush();
        
        return count($rssFeeds->entry);
    }

    /**
     * Function to create new Feed from Google Alert entry
     * @param \SimpleXMLElement $entry
     * @return Feed
     * @throws \Exception
     */
    private function createFeed(\SimpleXMLElement $entry): Feed
    {
        $url = $this->extractUrl($entry);
        $domainName = $this->extractDomain($url);
        
        $feed = new Feed();
        $feed->setGoogleId($entry->id);
        $feed->setTitle($this->blackfireService->sanitizeString($domainName.'|'.$entry->title));
        $feed->setLink($url);
        $feed->setPublished(new \DateTime($entry->published));
        $feed->setUpdated(new \DateTime($entry->updated));
        $feed->setContent($entry->content);
        $feed->setAuthor($entry->author->name);

        $this->entityManager->persist($feed);
        
        return $feed;
    }

    /**
     * @param Feed $feed
     * @return Feed
     */
    private function setFeedAsSpoted(Feed $feed): Feed
    {
        $feed->setMarkerDone(true);
        $this->entityManager->persist($feed);

        return $feed;
    }

    /**
     * Extract corresponding url (GET parameter) from Google Alert entry.link
     * it looks like: https://www.google.com/url?rct=j&sa=t&url=https://www.journaldemontreal.com/2024/10/03/langue-francaise-macron-et-trudeau-ont-insulte-le-quebec
     * --> https://www.journaldemontreal.com/2024/10/03/langue-francaise-macron-et-trudeau-ont-insulte-le-quebec
     * @param $entry
     * @return string
     */
    private function extractUrl($entry): string
    {
        $link = $entry->link['href'];
        $query_str = parse_url($link, PHP_URL_QUERY);
        parse_str($query_str, $query_params);
        return $query_params['url'];
    }

    /**
     * Extract corresponding domain (GET parameter) from Google Alert entry.link.url
     * it looks like: https://www.journaldemontreal.com/2024/10/03/langue-francaise-macron-et-trudeau-ont-insulte-le-quebec
     * --> return "journaldemontreal.com"
     * @param $url
     * @return string
     */
    private function extractDomain($url): string
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            $domain = $regs['domain'];
        }

        return $domain;
    }
}