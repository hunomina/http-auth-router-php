<?php


namespace hunomina\Routing\Auth;

interface UserInterface
{
    /**
     * @return string[]
     * Return the user's roles array
     */
    public function getRoles(): array;

    /**
     * @return string
     * Returns the username used to authenticate the user
     */
    public function getUsername(): string;
}