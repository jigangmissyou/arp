<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceStarterTemplates\Symfony\Component\HttpClient;

use KadenceWP\KadenceStarterTemplates\Symfony\Component\HttpClient\Exception\TransportException;
use KadenceWP\KadenceStarterTemplates\Symfony\Component\HttpClient\Response\MockResponse;
use KadenceWP\KadenceStarterTemplates\Symfony\Component\HttpClient\Response\ResponseStream;
use KadenceWP\KadenceStarterTemplates\Symfony\Contracts\HttpClient\HttpClientInterface;
use KadenceWP\KadenceStarterTemplates\Symfony\Contracts\HttpClient\ResponseInterface;
use KadenceWP\KadenceStarterTemplates\Symfony\Contracts\HttpClient\ResponseStreamInterface;
use KadenceWP\KadenceStarterTemplates\Symfony\Contracts\Service\ResetInterface;

/**
 * A test-friendly HttpClient that doesn't make actual HTTP requests.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class MockHttpClient implements HttpClientInterface, ResetInterface
{
    use HttpClientTrait;

    private $responseFactory;
    private $requestsCount = 0;
    private $defaultOptions = [];

    /**
     * @param callable|callable[]|ResponseInterface|ResponseInterface[]|iterable|null $responseFactory
     */
    public function __construct($responseFactory = null, ?string $baseUri = 'https://example.com')
    {
        $this->setResponseFactory($responseFactory);
        $this->defaultOptions['base_uri'] = $baseUri;
    }

    /**
     * @param callable|callable[]|ResponseInterface|ResponseInterface[]|iterable|null $responseFactory
     */
    public function setResponseFactory($responseFactory): void
    {
        if ($responseFactory instanceof ResponseInterface) {
            $responseFactory = [$responseFactory];
        }

        if (!$responseFactory instanceof \Iterator && null !== $responseFactory && !\is_callable($responseFactory)) {
            $responseFactory = (static function () use ($responseFactory) {
                yield from $responseFactory;
            })();
        }

        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        [$url, $options] = $this->prepareRequest($method, $url, $options, $this->defaultOptions, true);
        $url = implode('', $url);

        if (null === $this->responseFactory) {
            $response = new MockResponse();
        } elseif (\is_callable($this->responseFactory)) {
            $response = ($this->responseFactory)($method, $url, $options);
        } elseif (!$this->responseFactory->valid()) {
            throw new TransportException('The response factory iterator passed to MockHttpClient is empty.');
        } else {
            $responseFactory = $this->responseFactory->current();
            $response = \is_callable($responseFactory) ? $responseFactory($method, $url, $options) : $responseFactory;
            $this->responseFactory->next();
        }
        ++$this->requestsCount;

        if (!$response instanceof ResponseInterface) {
            throw new TransportException(sprintf('The response factory passed to MockHttpClient must return/yield an instance of ResponseInterface, "%s" given.', \is_object($response) ? \get_class($response) : \gettype($response)));
        }

        return MockResponse::fromRequest($method, $url, $options, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function stream($responses, ?float $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof ResponseInterface) {
            $responses = [$responses];
        } elseif (!is_iterable($responses)) {
            throw new \TypeError(sprintf('"%s()" expects parameter 1 to be an iterable of MockResponse objects, "%s" given.', __METHOD__, get_debug_type($responses)));
        }

        return new ResponseStream(MockResponse::stream($responses, $timeout));
    }

    public function getRequestsCount(): int
    {
        return $this->requestsCount;
    }

    /**
     * {@inheritdoc}
     */
    public function withOptions(array $options): self
    {
        $clone = clone $this;
        $clone->defaultOptions = self::mergeDefaultOptions($options, $this->defaultOptions, true);

        return $clone;
    }

    public function reset()
    {
        $this->requestsCount = 0;
    }
}
