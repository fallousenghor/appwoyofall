<?php

namespace App\Repository;

use App\Entity\Compteur;

interface CompteurRepositoryInterface
{
    public function findByNumero(string $numero): ?Compteur;

    public function findById(int $id): ?Compteur;

    public function save(Compteur $compteur): void;

    public function delete(Compteur $compteur): void;

    public function findAll(): array;
}
