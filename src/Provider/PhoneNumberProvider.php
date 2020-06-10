<?php declare(strict_types=1);

namespace App\Provider;

use Sylius\Bundle\UserBundle\Provider\AbstractUserProvider;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PhoneNumberProvider extends AbstractUserProvider
{
    /**
     * @param string $supportedUserClass FQCN
     */
    public function __construct(
        string $supportedUserClass,
        UserRepositoryInterface $userRepository,
        CanonicalizerInterface $canonicalizer
    ) {
        $this->supportedUserClass = $supportedUserClass;
        $this->userRepository = $userRepository;
        $this->canonicalizer = $canonicalizer;
    }

    /**
     * {@inheritdoc}
     */
    protected function findUser(string $phoneNumber): ?UserInterface
    {
        return $this->userRepository->findOneBy(['usernameCanonical' => $phoneNumber]);
    }
}
