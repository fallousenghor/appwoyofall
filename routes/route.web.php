<?php
// Tableau de routes sans le préfixe /api
return [
    // Health Check
    [
        'method' => 'GET',
        'path' => '/health',
        'action' => function() {
            header('Content-Type: application/json');
            echo json_encode([
                'data' => ['status' => 'ok', 'timestamp' => date('Y-m-d H:i:s')],
                'statut' => 'success',
                'code' => 200,
                'message' => 'AppDAF API is running'
            ]);
        }
    ],
    // Liste des achats
    [
        'method' => 'GET',
        'path' => '/achats',
        'controller' => 'App\\Controller\\AchatController',
        'action' => 'index'
    ],
    // Sélection d'un achat par id ou référence
    [
        'method' => 'GET',
        'path' => '/achats/{id}',
        'controller' => 'App\\Controller\\AchatController',
        'action' => 'show'
    ],
    // Création d'un achat
    [
        'method' => 'POST',
        'path' => '/achats',
        'controller' => 'App\\Controller\\AchatController',
        'action' => 'store'
    ]
];




