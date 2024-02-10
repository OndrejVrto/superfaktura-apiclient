<?php declare(strict_types=1);

namespace SuperFaktura\ApiClient\Test\UseCase\Other;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\HttpFactory;
use Fig\Http\Message\StatusCodeInterface;
use SuperFaktura\ApiClient\Test\TestCase;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversClass;
use SuperFaktura\ApiClient\Response\RateLimit;
use SuperFaktura\ApiClient\Request\RequestException;
use SuperFaktura\ApiClient\Response\ResponseFactory;
use SuperFaktura\ApiClient\UseCase\Other\UserCompaniesData;
use SuperFaktura\ApiClient\Request\CannotCreateRequestException;
use SuperFaktura\ApiClient\Contract\Other\CannotGetAllUserCompaniesDataException;
use SuperFaktura\ApiClient\Contract\Other\CannotGetAllUserCompaniesWithAccessException;

#[UsesClass(RateLimit::class)]
#[UsesClass(ResponseFactory::class)]
#[UsesClass(RequestException::class)]
#[CoversClass(UserCompaniesData::class)]
#[UsesClass(CannotCreateRequestException::class)]
#[CoversClass(CannotGetAllUserCompaniesDataException::class)]
#[UsesClass(\SuperFaktura\ApiClient\Response\Response::class)]
#[CoversClass(CannotGetAllUserCompaniesWithAccessException::class)]
final class UserCompaniesDataTest extends TestCase
{
    private const AUTHORIZATION_HEADER_VALUE = 'foo';

    private function getUserCompaniesData(Client $client): UserCompaniesData
    {
        return new UserCompaniesData(
            http_client               : $client,
            request_factory           : new HttpFactory(),
            response_factory          : new ResponseFactory(),
            base_uri                  : '',
            authorization_header_value: self::AUTHORIZATION_HEADER_VALUE,
        );
    }

    public function testGetAll(): void
    {
        $fixture = __DIR__ . '/fixtures/user-companies-data.json';

        $response = $this->getUserCompaniesData(
            $this->getHttpClientWithMockResponse(
                new Response(StatusCodeInterface::STATUS_OK, [], $this->jsonFromFixture($fixture)),
            ),
        )->getAll();

        self::assertSame($this->arrayFromFixture($fixture), $response->data);
    }

    public function testGetAllInternalServerError(): void
    {
        $this->expectException(CannotGetAllUserCompaniesDataException::class);
        $fixture = __DIR__ . '/../fixtures/unexpected-error.json';

        $this->getUserCompaniesData($this->getHttpClientWithMockResponse(
            new Response(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, [], $this->jsonFromFixture($fixture)),
        ))->getAll();
    }

    public function testGetAllRequestFailed(): void
    {
        $this->expectException(CannotGetAllUserCompaniesDataException::class);

        $this
            ->getUserCompaniesData($this->getHttpClientWithMockRequestException())
            ->getAll();
    }

    public function testGetAllResponseDecodeFailed(): void
    {
        $this->expectException(CannotGetAllUserCompaniesDataException::class);

        $this->getUserCompaniesData(
            $this->getHttpClientWithMockResponse(
                new Response(StatusCodeInterface::STATUS_OK, [], '{'),
            ),
        )->getAll();
    }

    public function getAllWithAccess(): void
    {
        $fixture = __DIR__ . '/fixtures/user-companies-data-with-access';

        $response = $this->getUserCompaniesData(
            $this->getHttpClientWithMockResponse(
                new Response(StatusCodeInterface::STATUS_OK, [], $this->jsonFromFixture($fixture)),
            ),
        )->getAllWithAccess();

        self::assertSame($this->arrayFromFixture($fixture), $response->data);
    }

    public function getAllWithAccessInternalServerError(): void
    {
        $this->expectException(CannotGetAllUserCompaniesWithAccessException::class);
        $fixture = __DIR__ . '/../fixtures/unexpected-error.json';

        $this->getUserCompaniesData($this->getHttpClientWithMockResponse(
            new Response(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, [], $this->jsonFromFixture($fixture)),
        ))->getAllWithAccess();
    }

    public function getAllWithAccessRequestFailed(): void
    {
        $this->expectException(CannotGetAllUserCompaniesWithAccessException::class);

        $this
            ->getUserCompaniesData($this->getHttpClientWithMockRequestException())
            ->getAllWithAccess();
    }

    public function getAllWithAccessResponseDecodeFailed(): void
    {
        $this->expectException(CannotGetAllUserCompaniesWithAccessException::class);

        $this->getUserCompaniesData(
            $this->getHttpClientWithMockResponse(
                new Response(StatusCodeInterface::STATUS_OK, [], '{'),
            ),
        )->getAllWithAccess();
    }
}
