<?php

namespace hunomina\Routing\Auth\Firewall\Checker;

use hunomina\Routing\Auth\Firewall\SecurityContext\SecurityContext;
use hunomina\Routing\Auth\UserInterface;

interface AuthenticationCheckerInterface
{
    /**
     * @return bool
     * Return true if an user is authenticated
     */
    public function isAuthenticated(): bool;

    /**
     * @return UserInterface
     * Return the authenticated UserInterface
     * Implement to retrieve an user from your application (cookies, sessions, ...)
     */
    public function getAuthenticatedUser(): ?UserInterface;

    /**
     * @param UserInterface|null $user
     * @param SecurityContext $securityContext
     * @param string $method
     * @param string $url
     * @return bool
     * Return true if the user has access to the url
     */
    public function checkAuthorization(?UserInterface $user, SecurityContext $securityContext, string $method, string $url): bool;
}