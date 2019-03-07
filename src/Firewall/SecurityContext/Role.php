<?php

namespace hunomina\Routing\Auth\Firewall\SecurityContext;

class Role
{
    /** @var string $_name */
    protected $_name;

    /** @var Role[] $_children */
    protected $_children = [];

    public function __construct(string $name)
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->_name = $name;
    }

    /**
     * @return Role[]
     */
    public function getChildren(): array
    {
        return $this->_children;
    }

    /**
     * @param array $children
     */
    public function setChildren(array $children): void
    {
        $this->_children = $children;
    }

    public function contains(Role $role): bool
    {
        $roleName = $role->getName();

        if ($this->_name === $roleName) {
            return true;
        }

        foreach ($this->_children as $child) {
            if ($child->getName() === $roleName) {
                return true;
            }
        }

        return false;
    }
}