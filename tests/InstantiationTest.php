<?php

use hunomina\Routing\Auth\AuthRouter;
use hunomina\Routing\Auth\AuthRoutingException;
use hunomina\Routing\Auth\Firewall\SecurityContext\Role;
use hunomina\Routing\Auth\Firewall\SecurityContext\Rule;
use hunomina\Routing\Auth\Test\TestAuthenticationChecker;
use hunomina\Routing\Route;
use hunomina\Routing\RoutingException;
use PHPUnit\Framework\TestCase;

class InstantiationTest extends TestCase
{
    private const YAML_ROUTE_FILE = __DIR__ . '/conf/routes.yml';
    private const YAML_SECURITY_FILE = __DIR__ . '/conf/security.yml';

    private const JSON_ROUTE_FILE = __DIR__ . '/conf/routes.json';
    private const JSON_SECURITY_FILE = __DIR__ . '/conf/security.json';

    /**
     * @throws AuthRoutingException
     * @throws ReflectionException
     * @throws RoutingException
     */
    public function testInstantiateYamlAuthRouter(): void
    {
        $router = new AuthRouter(self::YAML_ROUTE_FILE, new TestAuthenticationChecker());
        $router->loadSecurityContext(self::YAML_SECURITY_FILE);

        $this->assertInstanceOf(AuthRouter::class, $router);
        $this->assertContainsOnlyInstancesOf(Route::class, $router->getRouteManager()->getRoutes());
        $this->assertCount(11, $router->getRouteManager()->getRoutes());

        $this->assertContainsOnlyInstancesOf(Rule::class, $router->getSecurityContext()->getFirewallRules());
        $this->assertCount(10, $router->getSecurityContext()->getFirewallRules());

        $this->assertContainsOnlyInstancesOf(Role::class, $router->getSecurityContext()->getRoles());
        $this->assertCount(6, $router->getSecurityContext()->getRoles());
    }

    /**
     * @throws ReflectionException
     * @throws RoutingException
     * @throws AuthRoutingException
     */
    public function testInstantiateJsonAuthRouter(): void
    {
        $router = new AuthRouter(self::JSON_ROUTE_FILE, new TestAuthenticationChecker(), 'json');
        $router->loadSecurityContext(self::JSON_SECURITY_FILE);

        $this->assertInstanceOf(AuthRouter::class, $router);
        $this->assertContainsOnlyInstancesOf(Route::class, $router->getRouteManager()->getRoutes());
        $this->assertCount(11, $router->getRouteManager()->getRoutes());

        $this->assertContainsOnlyInstancesOf(Rule::class, $router->getSecurityContext()->getFirewallRules());
        $this->assertCount(10, $router->getSecurityContext()->getFirewallRules());

        $this->assertContainsOnlyInstancesOf(Role::class, $router->getSecurityContext()->getRoles());
        $this->assertCount(6, $router->getSecurityContext()->getRoles());
    }
}