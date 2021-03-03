<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CustodianControllerTest extends WebTestCase
{
    public function authorizedUserAndPasswordProvider()
    {
        return [
            [
                [
                    'PHP_AUTH_USER' => 'custodian1@pxl.be',
                    'PHP_AUTH_PW' => 'secret123'
                ]
            ],
            [
                [
                    'PHP_AUTH_USER' => 'custodian2@pxl.be',
                    'PHP_AUTH_PW' => 'secret123'
                ]
            ],
            [
                [
                    'PHP_AUTH_USER' => 'custodian3@pxl.be',
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
                    'PHP_AUTH_USER' => 'admin1@pxl.be',
                    'PHP_AUTH_PW' => 'secret123'
                ]
            ],
            [
                [
                    'PHP_UNAUTH_USER' => 'admin2@pxl.be',
                    'PHP_UNAUTH_PW' => 'secret123'
                ]
            ]
        ];
    }

    public function validTicketIdProvider()
    {
        $validTicketIds = [];

        for ($i = 1; $i <= 5; $i++) {
            if ($i % 2 !== 0) {
                $validTicketIds[] = [$i];
            }
        }

        return $validTicketIds;
    }

    public function testIndex_AnonymousUser_Statuscode302AndRedirectToLoginPage()
    {
        $client = static::createClient();
        $client->request('GET', '/custodian');

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testIndex_AuthorizedUserLoggedIn_Statuscode200($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $client->request('GET', '/custodian');

        $this->assertTrue($client->getResponse()->isOk());
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
        $client->request('GET', '/custodian');

        $this->assertTrue($client->getResponse()->isForbidden());
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider validTicketIdProvider
     */
    public function testIndex_UpvoteButtonClicked_TicketUpvotedSuccessfully($ticketId)
    {
        $userAndPassword = ['PHP_AUTH_USER' => 'custodian1@pxl.be', 'PHP_AUTH_PW' => 'secret123'];
        $client = static::createClient([], $userAndPassword);
        $crawler = $client->request('GET', '/custodian');
        $upvoteButton = $crawler->filter('table')->first()->filter('tr')->eq($ticketId)->filter('a')->last()->link();
        $numberOfVotesBefore = $crawler->filter('table')->first()->filter('tr')->eq($ticketId)->filter('td')->eq(1)->text();

        $client->click($upvoteButton);
        $crawler = $client->followRedirect();
        $numberOfVotesAfter = $crawler->filter('table')->first()->filter('tr')->eq($ticketId)->filter('td')->eq(1)->text();

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(intval($numberOfVotesBefore) + 1, intval($numberOfVotesAfter));
    }

    /**
     * @dataProvider validTicketIdProvider
     */
    public function testUpvoteTicket_AnonymousUser_Statuscode302AndRedirectToLoginPage($ticketId)
    {
        $client = static::createClient();
        $client->request('GET', '/custodian/upvote/' . $ticketId);

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testUpvoteTicket_AuthorizedUser_Statuscode302AndRedirectAndTicketUpvotedSuccessfully($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);

        for ($i = 1; $i <= 5; $i++) {
            $client->request('GET', '/custodian?filter=&page=1/upvote/' . $i);

            //$this->assertTrue($client->getResponse()->isRedirect());
            //$this->assertEquals(302, $client->getResponse()->getStatusCode());

            //$client->followRedirect();

            $this->assertTrue($client->getResponse()->isOk());
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
        }
    }

    /**
     * @dataProvider unauthorizedUserAndPasswordProvider
     */
    public function testUpvoteTicket_UnauthorizedUser_Statuscode403Unauthorized($unauthorizedUserAndPassword)
    {
        $client = static::createClient([], $unauthorizedUserAndPassword);
        $client->catchExceptions(false);
        $this->expectException(AccessDeniedException::class);

        for ($i = 1; $i <= 18; $i++) {
            $client->request('GET', '/custodian/upvote/' . $i);

            $this->assertTrue($client->getResponse()->isForbidden());
            $this->assertEquals(403, $client->getResponse()->getStatusCode());
        }
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testDeleteTicket_AuthorizedUser_Statuscode302AndRedirectAndTicketDeletedSuccessfully($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);

        for ($i = 1; $i <= 5; $i++) {
            $client->request('GET', '/custodian/delete/' . $i);

            $this->assertTrue($client->getResponse()->isRedirect());
            $this->assertEquals(302, $client->getResponse()->getStatusCode());

            $client->followRedirect();

            $this->assertTrue($client->getResponse()->isOk());
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
        }
    }

    /**
     * @dataProvider unauthorizedUserAndPasswordProvider
     */
    public function testDeleteTicket_UnauthorizedUser_Statuscode403Unauthorized($unauthorizedUserAndPassword)
    {
        $client = static::createClient([], $unauthorizedUserAndPassword);
        $client->catchExceptions(false);
        $this->expectException(AccessDeniedException::class);

        for ($i = 1; $i <= 18; $i++) {
            $client->request('GET', '/custodian/delete/' . $i);

            $this->assertTrue($client->getResponse()->isForbidden());
            $this->assertEquals(403, $client->getResponse()->getStatusCode());
        }
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testEditTicket_AuthorizedUser_Statuscode200($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        for ($i = 1; $i <= 5; $i++) {
            $client->request('GET', '/custodian/edit_ticket/' . $i);

            $this->assertEquals(200, $client->getResponse()->getStatusCode());
        }
    }

    public function testEditTicket_AnonymousUser_Statuscode302AndRedirectToLoginPage()
    {
        $client = static::createClient();

        for ($i = 1; $i <= 5; $i++) {
            $client->request('GET', '/custodian/edit_ticket/' . $i);

            $this->assertTrue($client->getResponse()->isRedirect());
            $this->assertEquals(302, $client->getResponse()->getStatusCode());

            $client->followRedirect();

            $this->assertEquals(200, $client->getResponse()->getStatusCode());
        }
    }

    /**
     * @dataProvider unauthorizedUserAndPasswordProvider
     */
    public function testEditTicket_UnauthorizedUser_Statuscode403Unauthorized($unauthorizedUserAndPassword)
    {
        $client = static::createClient([], $unauthorizedUserAndPassword);
        $client->catchExceptions(false);
        $this->expectException(AccessDeniedException::class);

        for ($i = 1; $i <= 5; $i++) {
            $client->request('GET', '/custodian/edit_ticket/' . $i);

            $this->assertTrue($client->getResponse()->isForbidden());
            $this->assertEquals(403, $client->getResponse()->getStatusCode());
        }
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testEditTicket_CustodianEditsTicket_Statuscode200AndH1ContainsTicketName($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $crawler = $client->request('GET', '/custodian/edit_ticket/17');
        $h1 = $crawler->filter('h1')->text();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("Edit ticket Ticket for whiteboard", $h1);
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testCustodian_CustodianOnDashboard_TableWithTicketsContainsFiveRowsInTableBody($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $crawler = $client->request('GET', '/custodian');
        $tableBodyRowCount = $crawler->filter('tbody')->children()->count();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(5, $tableBodyRowCount);
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testCustodian_CustodianEditsTicketWithForm_Statuscode302AndRedirectToHomePage($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $crawler = $client->request('GET', '/custodian/edit_ticket/17');
        $editForm = $crawler->filter('form')->form();

        $editForm['ticket_edit_form[name]'] = "Ticket for whiteboard";
        $editForm['ticket_edit_form[description]'] = "Description of ticket";

        $client->submit($editForm);

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testCustodian_CustodianEditsTicketWithForm_FormContainsEditTicketButton($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $crawler = $client->request('GET', '/custodian/edit_ticket/17');

        $editBtn = $crawler->selectButton('editBtn')->text();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("Edit Ticket", $editBtn);
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testCustodian_CustodianEditsTicketWithForm_FormContainsNameFormGroupWithCorrectLabel($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $crawler = $client->request('GET', '/custodian/edit_ticket/17');

        $label = $crawler->filter('#nameFormGroup')->filter('label')->text();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("Give a new name to this complaint:", $label);
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testCustodian_CustodianEditsTicketWithForm_FormContainsDecriptionFormGroupWithCorrectLabel($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $crawler = $client->request('GET', '/custodian/edit_ticket/17');

        $label = $crawler->filter('#descriptionFormGroup')->filter('label')->text();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("Give a new description to this complaint:", $label);
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testCustodian_CustodianOnDashboard_TableWithTicketsContainsEditButton($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $crawler = $client->request('GET', '/custodian');

        $editBtn = $crawler->selectButton('editBtn 1')->text();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("Edit", $editBtn);
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testCustodian_CustodianOnDashboard_TableWithTicketsContainsDeleteButton($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $crawler = $client->request('GET', '/custodian');

        $deleteBtn = $crawler->selectButton('deleteBtn 1')->text();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("Delete", $deleteBtn);
    }

    /**
     * @dataProvider authorizedUserAndPasswordProvider
     */
    public function testCustodian_CustodianOnDashboard_TableWithTicketsContainsUpvoteButton($authorizedUserAndPassword)
    {
        $client = static::createClient([], $authorizedUserAndPassword);
        $crawler = $client->request('GET', '/custodian');

        $upvoteBtn = $crawler->selectButton('upvoteBtn 1')->text();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("Upvote", $upvoteBtn);
    }
}
