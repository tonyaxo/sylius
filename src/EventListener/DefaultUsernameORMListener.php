<?php declare(strict_types=1);

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * Keeps user's username synchronized with customer.phoneNumber.
 * 
 * Service ID: sylius.listener.default_username 
 */
final class DefaultUsernameORMListener
{
    public function onFlush(OnFlushEventArgs $onFlushEventArgs)
    {
        $entityManager = $onFlushEventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        $this->processEntities($unitOfWork->getScheduledEntityInsertions(), $entityManager, $unitOfWork);
        $this->processEntities($unitOfWork->getScheduledEntityUpdates(), $entityManager, $unitOfWork);
    }

    private function processEntities(array $entities, EntityManagerInterface $entityManager, UnitOfWork $unitOfWork): void
    {
        /** @var CustomerInterface $customer */
        foreach ($entities as $customer) {
            if (!$customer instanceof CustomerInterface) {
                continue;
            }

            $user = $customer->getUser();
            if (null === $user) {
                continue;
            }

            // TODO uncomment after implementing canonical.
            if ($customer->getPhoneNumber() === $user->getUsername() /*&& $customer->getPhoneNumberCanonical() === $user->getUsernameCanonical()*/) {
                continue;
            }

            $user->setUsername($customer->getPhoneNumber());
            $user->setUsernameCanonical($customer->getPhoneNumber());

            /** @var ClassMetadata $userMetadata */
            $userMetadata = $entityManager->getClassMetadata(get_class($user));
            $unitOfWork->recomputeSingleEntityChangeSet($userMetadata, $user);
        }
    }
}
