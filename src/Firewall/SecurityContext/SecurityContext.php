<?php

namespace hunomina\Routing\Auth\Firewall\SecurityContext;

use hunomina\Routing\RoutingException;

abstract class SecurityContext
{
    /**
     * @var Role[] $_roles
     */
    protected $_roles = [];

    /**
     * @var Rule[] $_firewall
     */
    protected $_firewallRules;

    /**
     * @var string $_securityContextFile
     * File containing the security context
     */
    protected $_securityContextFile;

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

        $this->_securityContextFile = $security_context_file;
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
        return $this->_roles;
    }

    /**
     * @return Rule[]
     */
    public function getFirewallRules(): array
    {
        return $this->_firewallRules;
    }

    /**
     * @param string $name
     * @return Role|null
     */
    public function getRoleByName(string $name): ?Role
    {
        foreach ($this->_roles as $role) {
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
        foreach ($this->_firewallRules as $rule) {
            if ($rule->match($method, $url)) {
                return $rule->getRoles();
            }
        }
        return [];
    }
}