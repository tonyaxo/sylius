<?php declare(strict_types=1);

namespace App\Service;

interface OneTimePasswordServiceInterface
{
    /**
     * Return new generated password.
     *
     * @return string
     */
    public function generate(): string;
}
