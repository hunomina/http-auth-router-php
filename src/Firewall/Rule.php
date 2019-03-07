<?php

namespace hunomina\Routing\Auth\Firewall;

use hunomina\Routing\Auth\AuthRoutingException;

class Rule
{
    /** @var string $_path */
    protected $_path;

    /** @var array $_methods */
    protected $_methods = [];

    /** @var Role[] $_roles */
    protected $_roles;

    /**
     * Rule constructor.
     * @param string $_path
     * @param array $methods
     * @param array $_roles
     * @throws AuthRoutingException
     */
    public function __construct(string $_path, array $methods, array $_roles)
    {
        $this->_path = $_path;
        $this->setMethods($methods);
        $this->_roles = $_roles;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->_path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->_path = $path;
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->_roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->_roles = $roles;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->_methods;
    }

    /**
     * @param array $methods
     * @throws AuthRoutingException
     */
    public function setMethods(array $methods): void
    {
        foreach ($methods as $method) {
            if (!is_string($method)) {
                throw new AuthRoutingException('Methods for rule ' . $this->_path . ' can only be string');
            }
            $this->_methods[] = strtoupper($method);
        }
    }

    /**
     * @param string $method
     * @param string $url
     * @return bool
     */
    public function match(string $method, string $url): bool
    {
        if ((count($this->_methods) > 0) && !in_array(strtoupper($method), $this->_methods, true)) {
            return false;
        }
        return preg_match('/' . addcslashes($this->_path, '/') . '/', $url);
    }
}