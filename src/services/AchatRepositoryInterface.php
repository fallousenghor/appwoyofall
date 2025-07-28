<?php

namespace App\Repository;

use App\Entity\Achat;

interface AchatRepositoryInterface
{
    public function save(Achat $achat): void;
    public function findAll(): array;
}
