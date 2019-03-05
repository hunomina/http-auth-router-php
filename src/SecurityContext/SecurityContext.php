<?php

namespace hunomina\Routing\Auth\SecurityContext;

use hunomina\Routing\RoutingException;

abstract class SecurityContextInterface
{
    /**
     * @var array $_roles
     */
    protected $_roles;

    /**
     * @var array $_firewall
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

    public function getRoles(): array
    {
        return $this->_roles;
    }

    public function getFirewallRules(): array
    {
        return $this->_firewallRules;
    }
}