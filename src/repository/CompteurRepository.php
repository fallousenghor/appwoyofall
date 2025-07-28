<?php

namespace App\Repository;

use App\Entity\Compteur;
use App\Entity\Client;
use PDO;

class CompteurRepository implements CompteurRepositoryInterface
{
    private PDO $db;
    private ClientRepository $clientRepo;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->clientRepo = new ClientRepository($db);
    }

    public function find(string $numero): ?Compteur
    {
        $stmt = $this->db->prepare("SELECT * FROM compteurs WHERE numero_compteur = ?");
        $stmt->execute([$numero]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $client = $this->clientRepo->find($data['client_id']);
            return new Compteur($data['numero_compteur'], $client);
        }
        return null;
    }

    public function findByNumero(string $numero): ?Compteur
    {
        $sql = "SELECT c.*, cl.* FROM compteurs c JOIN clients cl ON c.client_id = cl.id_client WHERE c.numero_compteur = :numero LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['numero' => $numero]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $client = new Client(
                $data['client_id'],
                $data['nom']
            );
            return new Compteur($data['numero_compteur'], $client);
        }
        return null;
    }

    public function save(Compteur $compteur): void
    {
        $stmt = $this->db->prepare("INSERT INTO compteurs(numero_compteur, client_id) VALUES (?, ?)");
        $stmt->execute([$compteur->getNumero(), $compteur->getClient()->getId()]);
    }

    public function findById(int $id): ?Compteur
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM compteurs WHERE id_compteur = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $client = $this->clientRepo->find($data['client_id']);
                return new Compteur($data['numero_compteur'], $client);
            }
            return null;
        } catch (\Exception $e) {
           
            return null;
        }
    }

    public function delete(Compteur $compteur): void
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM compteurs WHERE id_compteur = ?");
            $stmt->execute([$compteur->getId()]);
        } catch (\Exception $e) {
           
        }
    }

    public function findAll(): array
    {
        $compteurs = [];
        try {
            $stmt = $this->db->query("SELECT * FROM compteurs");
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $client = $this->clientRepo->find($data['client_id']);
                $compteurs[] = new Compteur($data['numero_compteur'], $client);
            }
        } catch (\Exception $e) {
            // Log l’erreur si besoin
        }
        return $compteurs;
    }

    // ...autres méthodes existantes...
}
