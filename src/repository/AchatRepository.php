<?php




namespace App\Repository;

use App\Entity\Achat;
use App\Repository\CompteurRepository;
use PDO;

class AchatRepository implements AchatRepositoryInterface
{
    private PDO $db;
    private CompteurRepository $compteurRepo;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->compteurRepo = new CompteurRepository($db);
    }

public function save(Achat $achat): void
{
    try {
        $stmt = $this->db->prepare("INSERT INTO achats(reference, code_recharge, date_achat, heure_achat, montant, nbre_kwt, tranche, prix_kw, statut, ip, localisation, numero_compteur) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $achat->getReference(),
            $achat->getCodeRecharge(),
            $achat->getDateAchat()->format('Y-m-d'),
            $achat->getDateAchat()->format('H:i:s'),
            $achat->getMontant(),
            $achat->getNbreKwt(),
            $achat->getTranche(),
            $achat->getPrixKw(),
            $achat->getStatut(),
            $achat->getIp(),
            $achat->getLocalisation(),
            $achat->getCompteur()->getNumero()
        ]);
    } catch (\Exception $e) {
        error_log($e->getMessage());
        throw $e;
    }
}

  
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM achats");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?Achat
    {
        $stmt = $this->db->prepare("SELECT * FROM achats WHERE id_achat = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->hydrateAchat($data) : null;
    }

    public function findByReference(string $reference): ?Achat
    {
        $stmt = $this->db->prepare("SELECT * FROM achats WHERE reference = ?");
        $stmt->execute([$reference]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->hydrateAchat($data) : null;
    }

    private function hydrateAchat(array $data): Achat
    {
        // On suppose que CompteurRepository et les entités sont corrects
        $compteur = $this->compteurRepo->find($data['numero_compteur']);
        return new Achat(
            $data['reference'],
            $data['code_recharge'],
            new \DateTime($data['date_achat'].' '.$data['heure_achat']),
            (float)$data['montant'],
            (float)$data['nbre_kwt'],
            $data['tranche'],
            (float)$data['prix_kw'],
            $data['statut'],
            $data['ip'],
            $data['localisation'],
            $compteur
        );
    }
}
