<?php

namespace App\DataFixtures;

use App\Entity\Asset;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AssetFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 4; $i++) {
            $room = $manager->getRepository(Room::class)->find($i);
            $this->loadAsset($manager, $room, 'beamer');
            $this->loadAsset($manager, $room, 'whiteboard');
        }
    }

    private function loadAsset(ObjectManager $manager, $room, $name)
    {
        $asset = new Asset();
        $asset->setRoom($room);
        $asset->setName($name);

        $manager->persist($asset);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            RoomFixtures::class
        ];
    }
}
