<?php

use hunomina\Http\Response\HtmlResponse;
use hunomina\Routing\Auth\AuthRouter;
use hunomina\Routing\Auth\AuthRoutingException;
use hunomina\Routing\Auth\Test\TestAuthenticationChecker;
use hunomina\Routing\RoutingException;
use PHPUnit\Framework\TestCase;

class DisconnectedAccessTest extends TestCase
{
    private const YAML_ROUTE_FILE = __DIR__ . '/conf/routes.yml';
    private const YAML_SECURITY_FILE = __DIR__ . '/conf/security.yml';

    /** @var AuthRouter $router */
    private $router;

    /**
     * DisconnectedAccessTest constructor.
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     * @throws ReflectionException
     * @throws RoutingException
     * @throws AuthRoutingException
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        $this->router = new AuthRouter(self::YAML_ROUTE_FILE, new TestAuthenticationChecker());
        $this->router->loadSecurityContext(self::YAML_SECURITY_FILE);
        parent::__construct($name, $data, $dataName);
    }

    /**
     * @throws RoutingException
     */
    public function testAccessToIndex(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('GET', '/');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(200, $response->getHttpCode()); // no auth and route ok => 200
    }

    /**
     * @throws RoutingException
     */
    public function testAccessToAnonymousPage(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('GET', '/anonymous');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(302, $response->getHttpCode()); // auth not ok and route ok => 401
    }

    /**
     * @throws RoutingException
     */
    public function testRejectedFromUsersPage(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('GET', '/users');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(302, $response->getHttpCode()); // auth not ok and route ok => 401
    }

    /**
     * @throws RoutingException
     */
    public function testAccessToCustomersPage(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('GET', '/customers');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(302, $response->getHttpCode()); // auth not ok and route ok => 401
    }

    /**
     * @throws RoutingException
     */
    public function testRejectedFromModeratorsPage(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('GET', '/moderation');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(302, $response->getHttpCode()); // auth not ok and route ok => 401
    }

    /**
     * @throws RoutingException
     */
    public function testRejectedFromAdminPage(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('GET', '/admin');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(302, $response->getHttpCode()); // auth not ok and route ok => 401
    }

    /**
     * @throws RoutingException
     */
    public function testRejectedFromSuperAdminPage(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('GET', '/superadmin');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(302, $response->getHttpCode()); // auth not ok and route ok => 401
    }

    ////////// Test Variables Urls //////////

    /**
     * @throws RoutingException
     */
    public function testRejectedFromUserPage(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('GET', '/users/1');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(302, $response->getHttpCode()); // auth not ok and route ok => 401
    }

    ////////// Test Methods //////////

    /**
     * @throws RoutingException
     */
    public function testPutOnlyWithGet(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('GET', '/putOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(404, $response->getHttpCode()); // no auth and route does not exist => 404
    }

    /**
     * @throws RoutingException
     */
    public function testPutOnlyWithPut(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('PUT', '/putOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(302, $response->getHttpCode()); // auth not ok and route ok => 401
    }

    /**
     * @throws RoutingException
     */
    public function testGetAndPostOnlyWithGet(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('GET', '/getAndPostOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(200, $response->getHttpCode()); // not auth on GET ok and route ok => 200
    }

    /**
     * @throws RoutingException
     */
    public function testGetAndPostOnlyWithPost(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('POST', '/getAndPostOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(302, $response->getHttpCode()); // auth not ok and route ok => 401
    }

    /**
     * @throws RoutingException
     */
    public function testGetPostAndDeleteOnlyWithGet(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('GET', '/getPostAndDeleteOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(200, $response->getHttpCode()); // no auth on GET and route ok => 200
    }

    /**
     * @throws RoutingException
     */
    public function testGetPostAndDeleteOnlyWithPost(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('POST', '/getPostAndDeleteOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(302, $response->getHttpCode()); // auth not ok and route ok => 401
    }

    /**
     * @throws RoutingException
     */
    public function testGetPostAndDeleteOnlyWithDelete(): void
    {
        // login
        $_SESSION['auth'] = null;

        $response = $this->router->request('DELETE', '/getPostAndDeleteOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(302, $response->getHttpCode()); // auth not ok and route ok => 401
    }
}