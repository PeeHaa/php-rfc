<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpRfcs\Parser;

use AsyncBot\Plugin\PhpRfcs\Exception\UnexpectedHtmlFormat;
use AsyncBot\Plugin\PhpRfcs\ValueObject\Link;
use AsyncBot\Plugin\PhpRfcs\ValueObject\Links;
use League\Uri\Http;
use Psr\Http\Message\UriInterface;

final class Overview
{
    private const BASE_URL = 'https://wiki.php.net';

    public function parse(\DOMDocument $dom): Links
    {
        $rfcs = array_merge(
            $this->getRfcsInVoting($dom),
            $this->getRfcsUnderDiscussion($dom),
            $this->getRfcsInDraft($dom),
        );

        return new Links(...$rfcs);
    }

    /**
     * @return array<Link>
     * @throws UnexpectedHtmlFormat
     */
    private function getRfcsInVoting(\DOMDocument $dom): array
    {
        if (!$dom->getElementById('in_voting_phase')) {
            throw new UnexpectedHtmlFormat('in voting phase heading');
        }

        return $this->getRfcsUnderSection($dom->getElementById('in_voting_phase'), Links::STATUS_IN_VOTING);
    }

    /**
     * @return array<Link>
     * @throws UnexpectedHtmlFormat
     */
    private function getRfcsUnderDiscussion(\DOMDocument $dom): array
    {
        if (!$dom->getElementById('under_discussion')) {
            throw new UnexpectedHtmlFormat('under discussion heading');
        }

        return $this->getRfcsUnderSection($dom->getElementById('under_discussion'), Links::STATUS_UNDER_DISCUSSION);
    }

    /**
     * @return array<Link>
     * @throws UnexpectedHtmlFormat
     */
    private function getRfcsInDraft(\DOMDocument $dom): array
    {
        if (!$dom->getElementById('in_draft')) {
            throw new UnexpectedHtmlFormat('in draft heading');
        }

        return $this->getRfcsUnderSection($dom->getElementById('in_draft'), Links::STATUS_IN_DRAFT);
    }

    /**
     * @return array<Link>
     * @throws UnexpectedHtmlFormat
     */
    private function getRfcsUnderSection(\DOMNode $headingNode, string $status): array
    {
        $rfcContainer = $headingNode->nextSibling->nextSibling;

        if ($rfcContainer->nodeName !== 'div') {
            throw new UnexpectedHtmlFormat('rfc container');
        }

        $rfcs = [];

        foreach ($rfcContainer->getElementsByTagName('li') as $listItem) {
            $hyperlinkNode = $listItem->getElementsByTagName('a')->item(0);

            if (!$hyperlinkNode) {
                throw new UnexpectedHtmlFormat('rfc a tag');
            }

            $rfcs[] = new Link(
                $status,
                $this->getTitle($hyperlinkNode),
                $this->getDescription($hyperlinkNode),
                $this->getCreatedAt($hyperlinkNode),
                $this->getUrl($hyperlinkNode),
            );
        }

        return $rfcs;
    }

    private function getTitle(\DOMNode $hyperlinkNode): string
    {
        return $hyperlinkNode->textContent;
    }

    private function getDescription(\DOMNode $hyperlinkNode): string
    {
        $descriptionContainer = $hyperlinkNode->parentNode;

        $descriptionParts = [];

        foreach ($descriptionContainer->childNodes as $index => $childNode) {
            if ($index === 0) {
                continue;
            }

            $descriptionParts[] = trim($childNode->textContent);
        }

        preg_match('~^(.+)(?:\(Created:? \d{4}-\d{2}-\d{2})?~', implode(' ', $descriptionParts), $matches);

        return trim($matches[1]);
    }

    private function getCreatedAt(\DOMNode $hyperlinkNode): \DateTimeImmutable
    {
        $descriptionContainer = $hyperlinkNode->parentNode;

        $descriptionParts = [];

        foreach ($descriptionContainer->childNodes as $index => $childNode) {
            if ($index === 0) {
                continue;
            }

            $descriptionParts[] = trim($childNode->textContent);
        }

        preg_match('~^.+\(Created:? (\d{4}-\d{2}-\d{2}).+$~', implode(' ', $descriptionParts), $matches);

        if (!isset($matches[1])) {
            return new \DateTimeImmutable('1970-01-01');
        }

        try {
            return new \DateTimeImmutable($matches[1]);
        } catch (\Throwable $e) {
            return new \DateTimeImmutable('1970-01-01');
        }
    }

    private function getUrl(\DOMNode $hyperlinkNode): UriInterface
    {
        return Http::createFromString(sprintf('%s%s', self::BASE_URL, $hyperlinkNode->getAttribute('href')));
    }
}
