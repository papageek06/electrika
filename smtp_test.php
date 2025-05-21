<?php

// Simple test de connexion SMTP brute
$host = 'smtp.gmail.com';
$port = 587;

$connection = fsockopen($host, $port, $errno, $errstr, 10);

if (!$connection) {
    echo "❌ Impossible de se connecter à $host:$port\nErreur : $errstr ($errno)";
} else {
    echo "✅ Connexion à $host:$port OK\n";

    $response = fgets($connection, 1024);
    echo "Réponse serveur : $response";

    fclose($connection);
}
