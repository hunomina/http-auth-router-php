<?php

namespace hunomina\Routing\Auth\Firewall\SecurityContext;

use hunomina\Routing\Auth\AuthRoutingException;

class Rule
{
    /** @var string $path */
    private $path;

    /** @var array $methods */
    private $methods = [];

    /** @var Role[] $roles */
    private $roles;

    /**
     * Rule constructor.
     * @param string $_path
     * @param array $methods
     * @param array $_roles
     * @throws AuthRoutingException
     */
    public function __construct(string $_path, array $methods, array $_roles)
    {
        $this->path = $_path;
        $this->setMethods($methods);
        $this->roles = $_roles;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     * @throws AuthRoutingException
     */
    public function setMethods(array $methods): void
    {
        foreach ($methods as $method) {
            if (!is_string($method)) {
                throw new AuthRoutingException('Methods for rule ' . $this->path . ' can only be string');
            }
            $this->methods[] = strtoupper($method);
        }
    }

    /**
     * @param string $method
     * @param string $url
     * @return bool
     */
    public function match(string $method, string $url): bool
    {
        if ((count($this->methods) > 0) && !in_array(strtoupper($method), $this->methods, true)) {
            return false;
        }
        return preg_match('/' . addcslashes($this->path, '/') . '/', $url);
    }
}
