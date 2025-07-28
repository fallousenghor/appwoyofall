<?php

namespace App\Service;


use App\Entity\Achat;
use App\Repository\CompteurRepositoryInterface;
use App\Repository\TrancheRepositoryInterface;
use App\Repository\AchatRepositoryInterface;
use DateTime;

class AchatService {

    private CompteurRepositoryInterface $compteurRepo;
    private TrancheRepositoryInterface $trancheRepo;
    private AchatRepositoryInterface $achatRepo;

    public function __construct(
        CompteurRepositoryInterface $compteurRepo,
        TrancheRepositoryInterface $trancheRepo,
        AchatRepositoryInterface $achatRepo
    ) {
        $this->compteurRepo = $compteurRepo;
        $this->trancheRepo = $trancheRepo;
        $this->achatRepo = $achatRepo;
    }

    public function getAchatById(int $id): ?Achat
    {
        return $this->achatRepo->findById($id);
    }

    public function getAchatByReference(string $reference): ?Achat
    {
        return $this->achatRepo->findByReference($reference);
    }

    public function effectuerAchat(string $numeroCompteur, float $montant, string $ip, string $localisation): ?Achat
    {
        $compteur = $this->compteurRepo->findByNumero($numeroCompteur);
        if (!$compteur) {
            file_put_contents('/tmp/debug_achat_service.txt', "Compteur non trouvé: $numeroCompteur\n", FILE_APPEND);
            return null;
        }

        $tranche = $this->trancheRepo->getTranchePourMontant($montant);
        if (!$tranche) {
            file_put_contents('/tmp/debug_achat_service.txt', "Tranche non trouvée pour montant: $montant\n", FILE_APPEND);
            return null;
        }

      
        $codeRecharge = str_pad(strval(random_int(0, 9999999999)), 10, '0', STR_PAD_LEFT);
        $achat = new Achat(
            uniqid('ACHAT_'), 
            $codeRecharge,
            new DateTime(), 
            $montant, 
            $montant / $tranche->getPrixUnitaire(), 
            $tranche->getLibelle(),
            $tranche->getPrixUnitaire(), 
            'valide', 
            $ip, 
            $localisation, 
            $numeroCompteur ? $compteur : null 
        );

        $this->achatRepo->save($achat);

        return $achat;
    }
  
    public function getAllAchats(): array
    {
        return $this->achatRepo->findAll();
    }
}
