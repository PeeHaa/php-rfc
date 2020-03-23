<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpRfcs\ValueObject;

use Psr\Http\Message\UriInterface;

final class Link
{
    private string $status;

    private string $title;

    private string $description;

    private \DateTimeImmutable $createdAt;

    private UriInterface $uri;

    public function __construct(
        string $status,
        string $title,
        string $description,
        \DateTimeImmutable $createdAt,
        UriInterface $uri
    ) {
        $this->status      = $status;
        $this->title       = $title;
        $this->description = $description;
        $this->createdAt   = $createdAt;
        $this->uri         = $uri;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }
}
