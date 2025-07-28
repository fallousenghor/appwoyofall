<?php

namespace App\Repository;

use App\Entity\Tranche;

interface TrancheRepositoryInterface
{
    public function findAll(): array;
    public function getTranchePourMontant(float $montant): ?Tranche;
}
