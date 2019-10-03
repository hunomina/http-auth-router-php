<?php

namespace hunomina\Routing\Auth\Firewall\SecurityContext;

class Role
{
    /** @var string $name */
    private $name;

    /** @var Role[] $children */
    private $children = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Role[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    /**
     * @param Role $role
     * @return bool
     */
    public function contains(Role $role): bool
    {
        $roleName = $role->getName();

        if ($this->name === $roleName) {
            return true;
        }

        foreach ($this->children as $child) {
            if ($child->getName() === $roleName) {
                return true;
            }
        }

        return false;
    }
}
