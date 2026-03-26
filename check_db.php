<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306", "root", "");
    $stmt = $pdo->query("SHOW DATABASES LIKE 'bellavella'");
    if ($stmt->rowCount() > 0) {
        echo "Database 'bellavella' exists.\n";
    } else {
        echo "Database 'bellavella' DOES NOT exist.\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
