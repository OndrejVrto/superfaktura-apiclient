<?php

declare(strict_types=1);

namespace SuperFaktura\ApiClient\UseCase\Other;

use Psr\Http\Client\ClientInterface;
use SuperFaktura\ApiClient\Contract;
use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use SuperFaktura\ApiClient\Response\Response;
use SuperFaktura\ApiClient\Response\ResponseFactoryInterface;
use SuperFaktura\ApiClient\Contract\Other\CannotGetAllUserCompaniesDataException;
use SuperFaktura\ApiClient\Contract\Other\CannotGetAllUserCompaniesWithAccessException;

final readonly class UserCompaniesData implements Contract\Other\UserCompaniesData
{
    public function __construct(
        private ClientInterface $http_client,
        private RequestFactoryInterface $request_factory,
        private ResponseFactoryInterface $response_factory,
        private string $base_uri,
        private string $authorization_header_value,
    ) {
    }

    public function getAll(): Response
    {
        $request = $this->request_factory
            ->createRequest(
                RequestMethodInterface::METHOD_GET,
                $this->base_uri . '/users/getUserCompaniesData',
            )
            ->withHeader('Authorization', $this->authorization_header_value);

        try {
            $response = $this->response_factory->createFromJsonResponse(
                $this->http_client->sendRequest($request),
            );

            if ($response->isError()) {
                throw new CannotGetAllUserCompaniesDataException($request, $response->data['message'] ?? '');
            }

            return $response;
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw new CannotGetAllUserCompaniesDataException($request, $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getAllWithAccess(): Response
    {
        $request = $this->request_factory
            ->createRequest(
                RequestMethodInterface::METHOD_GET,
                $this->base_uri . '/users/getUserCompaniesData/1',
            )
            ->withHeader('Authorization', $this->authorization_header_value);

        try {
            $response = $this->response_factory->createFromJsonResponse(
                $this->http_client->sendRequest($request),
            );

            if ($response->isError()) {
                throw new CannotGetAllUserCompaniesWithAccessException($request, $response->data['message'] ?? '');
            }

            return $response;
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw new CannotGetAllUserCompaniesWithAccessException($request, $e->getMessage(), $e->getCode(), $e);
        }
    }
}
