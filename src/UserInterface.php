<?php


namespace hunomina\Routing\Auth;

interface UserInterface
{
    /**
     * @return string[]
     * Return the user's roles array
     */
    public function getRoles(): array;
}