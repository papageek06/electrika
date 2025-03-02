<?php
namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class SessionExtension extends AbstractExtension implements GlobalsInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getGlobals(): array
    {
        $session = $this->requestStack->getSession();

        return [
            'cart_session' => $session->get('cart'), // Ajoute la session en global
        ];
    }
}
