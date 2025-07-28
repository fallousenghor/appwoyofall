<?php

namespace App\Repository;

use App\Entity\Client;
use PDO;

class ClientRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function find(int $id): ?Client
    {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE id_client = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Client($data['nom'], $data['prenom']) : null;
    }

    public function save(Client $client): void
    {
        $stmt = $this->db->prepare("INSERT INTO clients(nom, prenom) VALUES (?, ?)");
        $stmt->execute([$client->getNom(), $client->getPrenom()]);
    }
}
