<?php
$dsn = "pgsql:host=db.lerrozebikwiurfirjzs.supabase.co;port=5432;dbname=postgres;sslmode=require";
try {
    $pdo = new PDO($dsn, 'postgres', '_6X5WLi%F@WTZxP');
    echo "✅ Connexion réussie à Supabase via IPv6 !";
} catch (PDOException $e) {
    echo "❌ Erreur PDO : " . $e->getMessage();
}
