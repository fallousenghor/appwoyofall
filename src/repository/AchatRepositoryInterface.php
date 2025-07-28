<?php

namespace App\Repository;

use App\Entity\Achat;

interface AchatRepositoryInterface
{
    
    public function findAll(): array;
    public function save(Achat $achat): void;
    public function findById(int $id): ?Achat;
    public function findByReference(string $reference): ?Achat;
    
}
