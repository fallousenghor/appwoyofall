<?php

namespace App\Entity;

class Tranche
{
    private int $id;
    private string $libelle;
    private float $montantMin;
    private float $montantMax;
    private float $prixUnitaire;

    public function __construct(string $libelle, float $montantMin, float $montantMax, float $prixUnitaire)
    {
        $this->libelle = $libelle;
        $this->montantMin = $montantMin;
        $this->montantMax = $montantMax;
        $this->prixUnitaire = $prixUnitaire;
    }

    public function getId(): int { return $this->id; }
    public function getLibelle(): string { return $this->libelle; }
    public function getMontantMin(): float { return $this->montantMin; }
    public function getMontantMax(): float { return $this->montantMax; }
    public function getPrixUnitaire(): float { return $this->prixUnitaire; }
}
