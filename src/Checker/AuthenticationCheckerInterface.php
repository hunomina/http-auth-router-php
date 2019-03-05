<?php

namespace hunomina\Router\Auth;

interface AuthenticationCheckerInterface
{
    /**
     * @return bool
     * Return true if an UserInterface is authenticated
     */
    public static function isAuthenticated(): bool;

    /**
     * @return UserInterface
     * Return the authenticated UserInterface
     */
    public static function getAuthenticatedUser(): UserInterface;
}