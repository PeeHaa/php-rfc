<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpRfcs\ValueObject;

final class Links implements \Iterator, \Countable
{
    public const STATUS_IN_VOTING        = 'In voting';
    public const STATUS_UNDER_DISCUSSION = 'Under discussion';
    public const STATUS_IN_DRAFT         = 'In draft';

    /** @var array<Link> */
    private array $links = [];

    public function __construct(Link ...$links)
    {
        $this->links = $links;
    }

    public function filterByStatus(string $status): self
    {
        $links = array_filter($this->links, fn (Link $link) => $link->getStatus() === $status);

        return new self(...$links);
    }

    public function current(): Link
    {
        return current($this->links);
    }

    public function next(): void
    {
        next($this->links);
    }

    public function key(): ?int
    {
        return key($this->links);
    }

    public function valid(): bool
    {
        return $this->key() !== null;
    }

    public function rewind(): void
    {
        reset($this->links);
    }

    public function count(): int
    {
        return count($this->links);
    }
}
