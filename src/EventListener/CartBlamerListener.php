<?php declare(strict_types=1);

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

/**
 * This class use customer phone number as second argument for AssignCustomerToCart message.
 */
final class CartBlamerListener
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var MessageBusInterface */
    private $bus;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        MessageBusInterface $bus,
        RequestStack $requestStack
    ) {
        $this->cartRepository = $cartRepository;
        $this->bus = $bus;
        $this->requestStack = $requestStack;
    }

    public function onJwtLogin(JWTCreatedEvent $interactiveLoginEvent): void
    {
        $user = $interactiveLoginEvent->getUser();
        $request = $this->requestStack->getCurrentRequest();

        Assert::notNull($request);

        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $token = $request->request->get('token');

        if (!$token) {
            return;
        }

        $cart = $this->cartRepository->findOneBy(['tokenValue' => $token]);

        if (null === $cart) {
            return;
        }

        $this->bus->dispatch(new AssignCustomerToCart($token, $user->getCustomer()->getPhoneNumber()));
    }
}
