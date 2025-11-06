<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');

        // Enregistre l'email dans la session (pour pré-remplir le champ en cas d’erreur)
        $request->getSession()->set('last_username', $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                // ✅ Protection contre les attaques CSRF
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): Response
    {
        // ✅ Redirection après connexion réussie
        return new RedirectResponse($this->urlGenerator->generate('app_commande_index'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        // ✅ Retour à la page login avec un message d'erreur
        $request->getSession()->getFlashBag()->add('error', 'Email ou mot de passe incorrect.');
        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
