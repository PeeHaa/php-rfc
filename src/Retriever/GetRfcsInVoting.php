<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpRfcs\Retriever;

use Amp\Promise;
use AsyncBot\Core\Http\Client;
use AsyncBot\Plugin\PhpRfcs\Parser\Overview as OverviewParser;
use AsyncBot\Plugin\PhpRfcs\ValueObject\Links;
use function Amp\call;

final class GetRfcsInVoting
{
    private Client $httpClient;

    private OverviewParser $overviewParser;

    public function __construct(Client $httpClient, OverviewParser $overviewParser)
    {
        $this->httpClient     = $httpClient;
        $this->overviewParser = $overviewParser;
    }

    /**
     * @return Promise<Links>
     */
    public function retrieve(): Promise
    {
        return call(function () {
            /** @var \DOMDocument $dom */
            $dom = yield $this->httpClient->requestHtml('https://wiki.php.net/rfc');

            return $this->overviewParser->parse($dom);
        });
    }
}
