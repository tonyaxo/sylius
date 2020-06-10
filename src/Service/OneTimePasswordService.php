<?php declare(strict_types=1);

namespace App\Service;

class OneTimePasswordService implements OneTimePasswordServiceInterface
{
    /**
     * @inheritDoc
     */
    public function generate(): string
    {
        $value = rand(1000, 9999);
        return "$value";
    }
}
