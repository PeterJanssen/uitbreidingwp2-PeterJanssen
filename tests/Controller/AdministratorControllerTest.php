<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdministratorControllerTest extends WebTestCase
{
    public function authorizedUserAndPasswordProvider()
    {
        return [
            [
                [
                    'PHP_AUTH_USER' => 'admin1@pxl.be',
                    'PHP_AUTH_PW' => 'secret123'
                ]
            ],
            [
                [
                    'PHP_AUTH_USER' => 'admin2@pxl.be',
                    'PHP_AUTH_PW' => 'secret123'
                ]
            ],
            [
                [
                    'PHP_AUTH_USER' => 'admin3@pxl.be',
                    'PHP_AUTH_PW' => 'secret123'
                ]
            ],
        ];
    }

    public function unauthorizedUserAndPasswordProvider()
    {
        return [
            [
                [
                    'PHP_AUTH_USER' => 'mod1@pxl.be',
                    'PHP_AUTH_PW' => 'secret123'
                ]
            ],
            [
                [
                    'PHP_AUTH_USER' => 'mod2@pxl.be',
                    'PHP_AUTH_PW' => 'secret123'
                ]
            ],
            [
                [
                    'PHP_AUTH_USER' => 'custodian1@pxl.be',
                    'PHP_AUTH_PW' => 'secret123'
                ]
            ],
            [
                [
                    'PHP_UNAUTH_USER' => 'custodian2@pxl.be',
                    'PHP_UNAUTH_PW' => 'secret123'
                ]
            ]
        ];
    }

    public function testIndex_AnonymousUser_Statuscode302AndRedirectToLoginPage()
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testIndex_AuthorizedUserLoggedIn_Statuscode200($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $client->request('GET', '/admin');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider unauthorizedUserAndPasswordProvider
     */
    public function testIndex_UnauthorizedUserLoggedIn_Statuscode403($unauthorizedUserAndPassword)
    {
        $client = static::createClient([], $unauthorizedUserAndPassword);
        $client->catchExceptions(false);
        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
