<?php

namespace hunomina\Routing\Auth\Firewall\SecurityContext;

use hunomina\Routing\RoutingException;

abstract class SecurityContext
{
    /**
     * @var Role[] $roles
     */
    protected $roles = [];

    /**
     * @var Rule[] $firewallRules
     */
    protected $firewallRules;

    /**
     * @var string $securityContextFile
     * File containing the security context
     */
    protected $securityContextFile;

    /**
     * SecurityContextInterface constructor.
     * @param string $security_context_file
     * @throws RoutingException
     */
    public function __construct(string $security_context_file)
    {
        if (!is_file($security_context_file)) {
            throw new RoutingException('The security context file does not exist');
        }

        $this->securityContextFile = $security_context_file;
    }

    /**
     * Load security context from file
     */
    abstract public function load(): void;

    /**
     * @param $securityContext
     * @return bool
     * Return true if is the security context configuration file is valid
     */
    abstract protected function isSecurityContextValid($securityContext): bool;

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return Rule[]
     */
    public function getFirewallRules(): array
    {
        return $this->firewallRules;
    }

    /**
     * @param string $name
     * @return Role|null
     */
    public function getRoleByName(string $name): ?Role
    {
        foreach ($this->roles as $role) {
            if ($role->getName() === $name) {
                return $role;
            }
        }
        return null;
    }

    /**
     * @param string $method
     * @param string $url
     * @return Role[]
     */
    public function getRolesByRule(string $method, string $url): array
    {
        foreach ($this->firewallRules as $rule) {
            if ($rule->match($method, $url)) {
                return $rule->getRoles();
            }
        }
        return [];
    }
}
