<?php

namespace hunomina\Routing\Auth\Test\User;

use hunomina\Routing\Auth\UserInterface;

class SuperAdmin implements UserInterface
{
    /**
     * @return array
     * Return the user's roles array
     */
    public function getRoles(): array
    {
        return ['ROLE_SUPER_ADMIN'];
    }

    /**
     * @return string
     * Returns the username used to authenticate the user
     */
    public function getUsername(): string
    {
        return 'super_admin';
    }
}