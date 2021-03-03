<?php


namespace App\Tests\Repository;


use App\Entity\Ticket;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TicketRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function tearDown()
    {
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testFindAllDescendingByNumberOfVotes()
    {
        $tickets = $this->entityManager
            ->getRepository(Ticket::class)
            ->findAllDescendingByNumberOfVotes();

        $this->assertCount(17, $tickets);
        for ($i = 0; $i < sizeof($tickets) - 1; $i++) {
            $this->assertGreaterThanOrEqual($tickets[$i + 1]->getNumberOfVotes(), $tickets[$i]->getNumberOfVotes());
        }
    }
}