<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpRfcs;

use Amp\Promise;
use AsyncBot\Core\Http\Client as HttpClient;
use AsyncBot\Plugin\PhpRfcs\Parser\Overview;
use AsyncBot\Plugin\PhpRfcs\Retriever\GetRfcsInVoting;
use AsyncBot\Plugin\PhpRfcs\ValueObject\Links;
use function Amp\call;

final class Plugin
{
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getRfcsInVoting(): Promise
    {
        return call(function () {
            /** @var Links $links */
            $links = yield (new GetRfcsInVoting($this->httpClient, new Overview()))->retrieve();

            return $links->filterByStatus(Links::STATUS_IN_VOTING);
        });
    }
}
