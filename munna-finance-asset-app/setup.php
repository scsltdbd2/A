<?php
$dbFile = __DIR__ . '/database/munna_finance_asset.db';
if (file_exists($dbFile)) {
    unlink($dbFile);
}
touch($dbFile);
$schema = file_get_contents(__DIR__ . '/database/schema.sql');
$seed = file_get_contents(__DIR__ . '/database/seed.sql');
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec($schema);
$pdo->exec($seed);
echo "Database created successfully!\n";