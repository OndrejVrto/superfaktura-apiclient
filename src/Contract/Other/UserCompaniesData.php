<?php declare(strict_types=1);

namespace SuperFaktura\ApiClient\Contract\Other;

use SuperFaktura\ApiClient\Response\Response;

interface UserCompaniesData
{
    /**
     * @throws CannotGetAllUserCompaniesDataException
     */
    public function getAll(): Response;

    /**
     * @throws CannotGetAllUserCompaniesWithAccessException
     */
    public function getAllWithAccess(): Response;
}
