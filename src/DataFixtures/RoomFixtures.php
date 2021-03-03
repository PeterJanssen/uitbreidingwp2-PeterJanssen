<?php

namespace App\DataFixtures;

use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoomFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadRoom($manager, 'B051', 1);
        $this->loadRoom($manager, 'B052', 5);
        $this->loadRoom($manager, 'B053', 100);
        $this->loadRoom($manager, 'B054', 1000);
    }

    private function loadRoom(ObjectManager $manager, $name, $happinessScore)
    {
        $room = new Room();
        $room->setName($name);
        $room->setHappinessScore($happinessScore);

        $manager->persist($room);
        $manager->flush();
    }
}
