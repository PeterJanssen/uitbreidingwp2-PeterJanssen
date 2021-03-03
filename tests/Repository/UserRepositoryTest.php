<?php


namespace App\Tests\Repository;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
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

    public function testFindByUserRole_Admins_GetThreeAdminObjects()
    {
        $users = $this->entityManager
            ->getRepository(User::class)
            ->findByUserRole('ROLE_ADMIN');

        $this->assertCount(3, $users);
        foreach ($users as $user) {
            $this->assertContains('ROLE_ADMIN', $user->getRoles());
        }
    }
}
