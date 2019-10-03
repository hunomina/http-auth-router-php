<?php

namespace hunomina\Routing\Auth;

use hunomina\Http\Response\HtmlResponse;
use hunomina\Http\Response\Response;
use hunomina\Routing\Auth\Firewall\Checker\AuthenticationCheckerInterface;
use hunomina\Routing\Auth\Firewall\SecurityContext\JsonSecurityContext;
use hunomina\Routing\Auth\Firewall\SecurityContext\SecurityContext;
use hunomina\Routing\Auth\Firewall\SecurityContext\YamlSecurityContext;
use hunomina\Routing\Router;
use hunomina\Routing\RoutingException;
use ReflectionException;

class AuthRouter extends Router
{
    /** @var AuthenticationCheckerInterface $autheticationChecker */
    protected $authenticationChecker;

    /** @var SecurityContext $securityContext */
    protected $securityContext;

    /** @var string $unauthorizedUrl */
    protected $unauthorizedUrl = '/401';

    /**
     * AuthRouter constructor.
     * @param string $route_file
     * @param AuthenticationCheckerInterface $checker
     * @param string $type
     * @throws ReflectionException
     * @throws RoutingException
     */
    public function __construct(string $route_file, AuthenticationCheckerInterface $checker, string $type = 'yaml')
    {
        $this->authenticationChecker = $checker;
        parent::__construct($route_file, $type);
    }

    /**
     * @param string $security_context_file
     * @return AuthRouter
     * @throws AuthRoutingException
     * @throws Firewall\SecurityContext\SecurityContextException
     * @throws RoutingException
     */
    public function loadSecurityContext(string $security_context_file): AuthRouter
    {
        if ($this->_type === 'json') {
            $this->setJsonSecurityContext($security_context_file);
        } else {
            $this->setYamlSecurityContext($security_context_file);
        }
        return $this;
    }

    /**
     * @param string $security_context_file
     * @return AuthRouter
     * @throws AuthRoutingException
     * @throws Firewall\SecurityContext\SecurityContextException
     * @throws RoutingException
     */
    public function setYamlSecurityContext(string $security_context_file): AuthRouter
    {
        $securityContext = new YamlSecurityContext($security_context_file);
        $securityContext->load();
        $this->securityContext = $securityContext;

        return $this;
    }

    /**
     * @param string $security_context_file
     * @return AuthRouter
     * @throws AuthRoutingException
     * @throws Firewall\SecurityContext\SecurityContextException
     * @throws RoutingException
     */
    public function setJsonSecurityContext(string $security_context_file): AuthRouter
    {
        $securityContext = new JsonSecurityContext($security_context_file);
        $securityContext->load();
        $this->securityContext = $securityContext;

        return $this;
    }

    public function setUnauthorizedUrl(string $url): AuthRouter
    {
        $this->unauthorizedUrl = $url;
        return $this;
    }

    public function getSecurityContext(): SecurityContext
    {
        return $this->securityContext;
    }

    /**
     * @param string $method
     * @param string $url
     * @return Response
     * @throws RoutingException
     */
    public function request(string $method, string $url): Response
    {
        if ($this->securityContext instanceof SecurityContext) {

            $user = $this->authenticationChecker->getAuthenticatedUser();
            if ($this->authenticationChecker->checkAuthorization($user, $this->securityContext, $method, $url)) {
                return parent::request($method, $url);
            }

            $response = new HtmlResponse('401 Unauthorized');
            $response->setHttpCode(302); // 401 is not redirected by web browsers
            $response->addHeader('Cache-Control: no-cache');
            $response->addHeader('Location: ' . $this->unauthorizedUrl);

            return $response;
        }

        throw new RoutingException('Security Context not loaded');
    }
}