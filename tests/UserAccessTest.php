<?php

use hunomina\Http\Response\HtmlResponse;
use hunomina\Routing\Auth\AuthRouter;
use hunomina\Routing\Auth\AuthRoutingException;
use hunomina\Routing\Auth\Test\TestAuthenticationChecker;
use hunomina\Routing\Auth\Test\User\User;
use hunomina\Routing\RoutingException;
use PHPUnit\Framework\TestCase;

class UserAccessTest extends TestCase
{
    private const YAML_ROUTE_FILE = __DIR__ . '/conf/routes.yml';
    private const YAML_SECURITY_FILE = __DIR__ . '/conf/security.yml';

    /** @var AuthRouter $router */
    private $router;

    /**
     * UserAccessTest constructor.
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
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('GET', '/');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(200, $response->getHttpCode());
    }

    /**
     * @throws RoutingException
     */
    public function testAccessToAnonymousPage(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('GET', '/anonymous');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(200, $response->getHttpCode());
    }

    /**
     * @throws RoutingException
     */
    public function testAccessToUsersPage(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('GET', '/users');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(200, $response->getHttpCode());
    }

    /**
     * @throws RoutingException
     */
    public function testRejectedFromCustomersPage(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('GET', '/customers');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(401, $response->getHttpCode());
    }

    /**
     * @throws RoutingException
     */
    public function testRejectedFromModeratorsPage(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('GET', '/moderation');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(401, $response->getHttpCode());
    }

    /**
     * @throws RoutingException
     */
    public function testRejectedFromAdminPage(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('GET', '/admin');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(401, $response->getHttpCode());
    }

    /**
     * @throws RoutingException
     */
    public function testRejectedFromSuperAdminPage(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('GET', '/superadmin');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(401, $response->getHttpCode());
    }

    ////////// Test Variables Urls //////////

    /**
     * @throws RoutingException
     */
    public function testRejectedFromUserPage(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('GET', '/users/1');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(401, $response->getHttpCode());
    }

    ////////// Test Methods //////////

    /**
     * @throws RoutingException
     */
    public function testPutOnlyWithGet(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('GET', '/putOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(404, $response->getHttpCode()); // auth ok but route does not exist => 404
    }

    /**
     * @throws RoutingException
     */
    public function testPutOnlyWithPut(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('PUT', '/putOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(200, $response->getHttpCode()); // auth ok and route ok => 200
    }

    /**
     * @throws RoutingException
     */
    public function testGetAndPostOnlyWithGet(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('GET', '/getAndPostOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(200, $response->getHttpCode()); // no auth and route ok => 200
    }

    /**
     * @throws RoutingException
     */
    public function testGetAndPostOnlyWithPost(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('POST', '/getAndPostOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(401, $response->getHttpCode()); // auth not ok and route ok => 401
    }

    /**
     * @throws RoutingException
     */
    public function testGetPostAndDeleteOnlyWithGet(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('GET', '/getPostAndDeleteOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(200, $response->getHttpCode()); // no auth and route ok => 200
    }

    /**
     * @throws RoutingException
     */
    public function testGetPostAndDeleteOnlyWithPost(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('POST', '/getPostAndDeleteOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(401, $response->getHttpCode()); // auth not ok and route ok => 401
    }

    /**
     * @throws RoutingException
     */
    public function testGetPostAndDeleteOnlyWithDelete(): void
    {
        // login
        $user = new User();
        $_SESSION['auth'] = serialize($user);

        $response = $this->router->request('DELETE', '/getPostAndDeleteOnly');
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(401, $response->getHttpCode()); // auth not ok and route ok => 401
    }
}