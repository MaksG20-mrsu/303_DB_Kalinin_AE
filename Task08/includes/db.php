<?php
// includes/db.php
$dbPath = dirname(__DIR__) . '/data/students.db';
$db = new PDO("sqlite:" . $dbPath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("PRAGMA foreign_keys = ON");

// Инициализируем БД при первом запуске
include_once 'init_db.php';
initDatabase($db);
?>