<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager, 'admin', 'ROLE_ADMIN');
        $this->loadUsers($manager, 'mod', 'ROLE_MOD');
        $this->loadUsers($manager, 'custodian', 'ROLE_CUSTODIAN');
    }

    private function loadUsers(ObjectManager $manager, $name, $userRole)
    {
        for ($i = 1; $i <= 3; $i++) {
            $user = new User();
            $user->setEmail("$name$i@pxl.be");
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                "secret123"
            ));
            $userRoles = array($userRole);
            $user->setRoles($userRoles);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
