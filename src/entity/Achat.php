<?php


namespace App\Entity;

use DateTime;

class Achat
{
    private ?int $id;
    private string $reference;
    private string $codeRecharge;
    private DateTime $dateAchat;
    private float $montant;
    private float $nbreKwt;
    private string $tranche;
    private float $prixKw;
    private string $statut;
    private string $ip;
    private string $localisation;
    private Compteur $compteur;

    public function __construct(
        string $reference,
        string $codeRecharge,
        DateTime $dateAchat,
        float $montant,
        float $nbreKwt,
        string $tranche,
        float $prixKw,
        string $statut,
        string $ip,
        string $localisation,
        Compteur $compteur,
        ?int $id = null
    ) {
        $this->reference = $reference;
        $this->codeRecharge = $codeRecharge;
        $this->dateAchat = $dateAchat;
        $this->montant = $montant;
        $this->nbreKwt = $nbreKwt;
        $this->tranche = $tranche;
        $this->prixKw = $prixKw;
        $this->statut = $statut;
        $this->ip = $ip;
        $this->localisation = $localisation;
        $this->compteur = $compteur;
        $this->id = $id;
    }

   
    public function getReference(): string { return $this->reference; }
    public function getCodeRecharge(): string { return $this->codeRecharge; }
    public function getDateAchat(): DateTime { return $this->dateAchat; }
    public function getMontant(): float { return $this->montant; }
    public function getNbreKwt(): float { return $this->nbreKwt; }
    public function getTranche(): string { return $this->tranche; }
    public function getPrixKw(): float { return $this->prixKw; }
    public function getStatut(): string { return $this->statut; }
    public function getIp(): string { return $this->ip; }
    public function getLocalisation(): string { return $this->localisation; }
    public function getCompteur(): Compteur { return $this->compteur; }

    public function getNumeroCompteur(): string
    {
        return $this->compteur->getNumero();
    }

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function toArray(): array
    {
        return [
            'reference' => $this->getReference(),
            'code_recharge' => $this->getCodeRecharge(),
            'date_achat' => $this->getDateAchat() ? $this->getDateAchat()->format('Y-m-d H:i:s') : null,
            'montant' => $this->getMontant(),
            'nbre_kwt' => $this->getNbreKwt(),
            'tranche' => $this->getTranche(),
            'prix_kw' => $this->getPrixKw(),
            'statut' => $this->getStatut(),
            'ip' => $this->getIp(),
            'numero_compteur' => $this->getNumeroCompteur(),
        ];
    }
}
