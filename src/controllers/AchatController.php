<?php

namespace App\Controller;

use App\Repository\AchatRepository;
use App\Repository\CompteurRepository;
use App\Repository\TrancheRepository;
use App\Service\AchatService;
use PDO;


class AchatController
{
    private AchatService $achatService;

    public function __construct(AchatService $achatService)
    {
        $this->achatService = $achatService;
    }

    // GET /achats/{id}
    public function show($id): void
    {
        header('Content-Type: application/json');
        $achat = null;
        if (is_numeric($id)) {
            $achat = $this->achatService->getAchatById((int)$id);
        } else {
            $achat = $this->achatService->getAchatByReference($id);
        }
        if ($achat) {
            echo json_encode([
                'data' => $achat,
                'statut' => 'success',
                'code' => 200,
                'message' => 'Achat trouvé'
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'data' => null,
                'statut' => 'error',
                'code' => 404,
                'message' => 'Aucun achat trouvé pour cet identifiant ou référence'
            ]);
        }
    }

    // GET /achats
    public function index(): void
    {
        header('Content-Type: application/json');
        try {
            $achats = $this->achatService->getAllAchats();
            echo json_encode([
                'data' => $achats,
                'statut' => 'success',
                'code' => 200,
                'message' => 'Liste des achats récupérée avec succès'
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'data' => null,
                'statut' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    // POST /achats
    public function store(): void
    {
        try {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode([
                    "data" => null,
                    "statut" => "error",
                    "code" => 405,
                    "message" => "Méthode non autorisée"
                ]);
                return;
            }

            $body = json_decode(file_get_contents('php://input'), true);
file_put_contents('/tmp/debug_achat_post.txt', print_r($body, true)); // Ajoute cette ligne pour debug

            if (!isset($body['numero_compteur']) || !isset($body['montant'])) {
                http_response_code(400);
                echo json_encode([
                    "data" => null,
                    "statut" => "error",
                    "code" => 400,
                    "message" => "Champs obligatoires manquants : numero_compteur, montant"
                ]);
                return;
            }

            $numero = trim($body['numero_compteur']);
            $montant = floatval($body['montant']);

            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $localisation = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 100) : 'unknown';

            $achat = $this->achatService->effectuerAchat($numero, $montant, $ip, $localisation);

            if ($achat) {
                http_response_code(201);
                echo json_encode([
                    'data' => is_object($achat) && method_exists($achat, 'toArray') ? $achat->toArray() : $achat,
                    'statut' => 'success',
                    'code' => 201,
                    'message' => 'Achat créé avec succès'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'data' => null,
                    'statut' => 'error',
                    'code' => 400,
                    'message' => 'Erreur lors de la création de l\'achat'
                ]);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'data' => null,
                'statut' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            exit;
        }
    }

    public function handleRequest(): void
    {
        // Vérifier que la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                "data" => null,
                "statut" => "error",
                "code" => 405,
                "message" => "Méthode non autorisée"
            ]);
            return;
        }

        // Lire et décoder le JSON
        $body = json_decode(file_get_contents('php://input'), true);

        if (!isset($body['numero_compteur']) || !isset($body['montant'])) {
            http_response_code(400);
            echo json_encode([
                "data" => null,
                "statut" => "error",
                "code" => 400,
                "message" => "Champs obligatoires manquants : numero_compteur, montant"
            ]);
            return;
        }

        $numero = trim($body['numero_compteur']);
        $montant = floatval($body['montant']);

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $localisation = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 100) : 'unknown';

        // Appeler le service métier
        $achat = $this->achatService->effectuerAchat($numero, $montant, $ip, $localisation);

        header('Content-Type: application/json');
        if ($achat) {
            http_response_code(201);
            echo json_encode([
                'data' => is_object($achat) && method_exists($achat, 'toArray') ? $achat->toArray() : $achat,
                'statut' => 'success',
                'code' => 201,
                'message' => 'Achat créé avec succès'
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'data' => null,
                'statut' => 'error',
                'code' => 400,
                'message' => 'Erreur lors de la création de l\'achat'
            ]);
        }
    }
}