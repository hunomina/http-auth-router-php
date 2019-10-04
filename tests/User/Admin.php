<?php

namespace hunomina\Routing\Auth\Test\User;

use hunomina\Routing\Auth\UserInterface;

class Admin implements UserInterface
{
    /**
     * @return array
     * Return the user's roles array
     */
    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    /**
     * @return string
     * Returns the username used to authenticate the user
     */
    public function getUsername(): string
    {
        return 'admin';
    }
}