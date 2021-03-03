<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function userAndPasswordProvider()
    {
        return [
            [
                ['PHP_AUTH_USER' => 'admin1@pxl.be',
                    'PHP_AUTH_PW' => 'secret123']
            ],
            [
                ['PHP_AUTH_USER' => 'mod1@pxl.be',
                    'PHP_AUTH_PW' => 'secret123']
            ],
            [
                ['PHP_AUTH_USER' => 'custodian1@pxl.be',
                    'PHP_AUTH_PW' => 'secret123']
            ],
        ];
    }

    public function testIndex_AnonymousUser_Statuscode200AndH1EqualsStringLiteral()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $h1 = $crawler->filter('h1')->text();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Welcome to the PXL Asset Management tool!', $h1);
    }

    /**
     * @dataProvider userAndPasswordProvider
     */
    public function testIndex_UserLoggedIn_Statuscode200AndH1ContainsUserEmail($userAndPassword)
    {
        $client = static::createClient([], $userAndPassword);
        $crawler = $client->request('GET', '/');
        $h1 = $crawler->filter('h1')->text();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains($userAndPassword['PHP_AUTH_USER'], $h1);
    }

    public function testIndex_AnonymousUserClicksOnAnchorWithTextHome_Statuscode200()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $HomeAnchorLink = $crawler->filter('a:contains("Home")')->link();
        $client->click($HomeAnchorLink);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testIndex_AnonymousUserClicksOnAnchorWithTextLogin_Statuscode200()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $loginAnchorLink = $crawler->filter('a:contains("Login")')->link();
        $client->click($loginAnchorLink);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testIndex_AnonymousUser_TableWithAssetsContainsSevenRowsInTableBody()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $tableBodyRowCount = $crawler->filter('tbody')->children()->count();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(7, $tableBodyRowCount);
    }

    /**
     * @dataProvider userAndPasswordProvider
     */
    public function testIndex_UserLoggedIn_TableWithAssetsContainsEightRowsInTableBody($userAndPassword)
    {
        $client = static::createClient([], $userAndPassword);
        $crawler = $client->request('GET', '/');
        $tableBodyRowCount = $crawler->filter('tbody')->children()->count();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(8, $tableBodyRowCount);
    }
}
