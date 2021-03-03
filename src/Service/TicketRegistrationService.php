<?php


namespace App\Service;


use App\Repository\AssetRepository;
use App\Repository\TicketRepository;

class TicketRegistrationService
{
    private $ticketRepository;
    private $assetRepository;
    private $upperBoundary = 3;

    public function __construct(TicketRepository $ticketRepository, AssetRepository $assetRepository)
    {
        $this->ticketRepository = $ticketRepository;
        $this->assetRepository = $assetRepository;
    }

    public function getAvailableAssetsForCurrentUser($isGranted)
    {
        $assetsInDatabase = $this->assetRepository->findAll();

        if ($isGranted) {
            return $assetsInDatabase;
        }

        $assetWithLimitedTickets = [];
        foreach ($assetsInDatabase as $assetInDatabase) {
            if ($this->ticketCountForAssetIdIsLessThanGivenValueToday($assetInDatabase->getId(), $this->upperBoundary)) {
                $assetWithLimitedTickets[] = $assetInDatabase;
            }
        }

        return $assetWithLimitedTickets;
    }

    private function ticketCountForAssetIdIsLessThanGivenValueToday($assetId, $upperBoundary)
    {
        return sizeof($this->ticketRepository->findTicketsTodayByAssetId($assetId)) < $upperBoundary;
    }
}
