<?php

namespace App\Repository;

use App\Entity\Compteur;

interface CompteurRepositoryInterface
{
    public function find(string $numero): ?Compteur;
    public function save(Compteur $compteur): void;
}
