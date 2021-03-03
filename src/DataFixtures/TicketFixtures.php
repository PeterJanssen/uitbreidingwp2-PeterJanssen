<?php

namespace App\DataFixtures;

use App\Entity\Asset;
use App\Entity\Ticket;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TicketFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $assetRepository = $manager->getRepository(Asset::class);
        $numberOfVotes = 100;
        $counter = 1;
        $assets = $assetRepository->findAll();

        for ($i = 0; $i < sizeof($assets); $i++) {
            $this->loadTicket($manager, $assets[$i], $numberOfVotes, "Description of ticket " . $assets[$i]->getName(), "Ticket for " . $assets[$i]->getName());
            $numberOfVotes = $numberOfVotes + 100 * $counter;
            $this->loadTicket($manager, $assets[$i], $numberOfVotes, "Description of ticket " . $assets[$i]->getName(), "Ticket for " . $assets[$i]->getName());
            if ($counter === sizeof($assets)) {
                $this->loadTicket($manager, $assets[$i], $numberOfVotes, "Description of ticket " . $assets[$i]->getName(), "Ticket for " . $assets[$i]->getName());
            }
            $counter++;
        }
    }

    private function loadTicket(ObjectManager $manager, $asset, $numberOfVotes, $description, $name)
    {
        $creationDate = new DateTimeImmutable();

        $ticket = new Ticket();
        $ticket->setAsset($asset);
        $ticket->setNumberOfVotes($numberOfVotes);
        $ticket->setDescription($description);
        $ticket->setCreationDate($creationDate);
        $ticket->setName($name);

        $manager->persist($ticket);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            RoomFixtures::class,
            AssetFixtures::class
        ];
    }
}
