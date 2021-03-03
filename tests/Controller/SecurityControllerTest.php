<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $client = null;

    public function registeredUserAndPasswordProvider()
    {
        return [
            [
                [
                    'user' => 'admin1@pxl.be',
                    'password' => 'secret123',
                ]
            ],
            [
                [
                    'user' => 'mod1@pxl.be',
                    'password' => 'secret123',
                ]
            ],
            [
                [
                    'user' => 'custodian1@pxl.be',
                    'password' => 'secret123',
                ]
            ],
        ];
    }

    public function testLogin_AnonymousUser_Statuscode200()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $h1 = $crawler->filter('h1')->text();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Please, sign in.', $h1);
    }

    /**
     * @dataProvider registeredUserAndPasswordProvider
     */
    public function testLogin_AnonymousUserLogsInWithForm_Statuscode302AndRedirectToHomePage($userAndPassword)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $loginForm = $crawler->filter('form')->form();

        $loginForm['_username'] = $userAndPassword['user'];
        $loginForm['_password'] = $userAndPassword['password'];

        $client->submit($loginForm);

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider registeredUserAndPasswordProvider
    */
    public function testLogin_AuthorizedUser_Statuscode200($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $client->request('GET', '/login');


        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLogout_AnonymousUser_Statuscode302AndRedirectToHomePage()
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider registeredUserAndPasswordProvider
    */
    public function testLogout_AuthorizedUser_Statuscode302AndRedirectToHomePage($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $client->request('GET', '/logout');

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
