<?php

namespace App\Repository;

use App\Entity\Tranche;
use PDO;

class TrancheRepository implements TrancheRepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM tranches ORDER BY montant_min ASC");
        $results = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new Tranche($data['libelle'], $data['montant_min'], $data['montant_max'], $data['prix_unitaire']);
        }
        return $results;
    }

    public function getTranchePourMontant(float $montant): ?Tranche
    {
        $stmt = $this->db->prepare("SELECT * FROM tranches WHERE :montant BETWEEN montant_min AND montant_max LIMIT 1");
        $stmt->execute(['montant' => $montant]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Tranche($data['libelle'], $data['montant_min'], $data['montant_max'], $data['prix_unitaire']) : null;
    }

    public function findTrancheByMontant(float $montant)
    {
        $sql = "SELECT * FROM tranches WHERE limite_superieure >= :montant ORDER BY limite_superieure ASC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['montant' => $montant]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
