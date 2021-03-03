<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ModeratorControllerTest extends WebTestCase
{
    public function authorizedUserAndPasswordProvider()
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
                    'PHP_AUTH_USER' => 'mod2@pxl.be',
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
                    'PHP_AUTH_USER' => 'admin1@pxl.be',
                    'PHP_AUTH_PW' => 'secret123'
                ]
            ],
            [
                [
                    'PHP_AUTH_USER' => 'custodian2@pxl.be',
                    'PHP_AUTH_PW' => 'secret123'
                ]
            ],
        ];
    }

    public function testIndex_AnonymousUser_Statuscode302AndRedirectToLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/moderator');

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
    */
    public function testIndex_AuthorizedUser_Statuscode200($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $client->request('GET', '/moderator');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider unauthorizedUserAndPasswordProvider
     */
    public function testIndex_UnauthorizedUser_Statuscode403($unauthorizedUserAndPassword)
    {
        $client = static::createClient([], $unauthorizedUserAndPassword);
        $client->catchExceptions(false);
        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/moderator');

        $this->assertTrue($client->getResponse()->isForbidden());
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
