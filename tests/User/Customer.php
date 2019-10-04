<?php

namespace hunomina\Routing\Auth\Test\User;

use hunomina\Routing\Auth\UserInterface;

class Customer implements UserInterface
{
    /**
     * @return array
     * Return the user's roles array
     */
    public function getRoles(): array
    {
        return ['ROLE_CUSTOMER'];
    }

    /**
     * @return string
     * Returns the username used to authenticate the user
     */
    public function getUsername(): string
    {
        return 'customer';
    }
}