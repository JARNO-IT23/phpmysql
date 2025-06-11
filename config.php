<<<<<<< HEAD
<<?php
$host = '127.0.0.1';          
$db   = 'helpdesk_system';             
$user = 'admin';              
$pass = 'Passw0rd';          
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
    PDO::ATTR_EMULATE_PREPARES   => false,                 
];
=======
<?php
$host = 'localhost';
$dbname = 'helpdesk_system';
$username = 'admin';
$password = 'passw0rd';
>>>>>>> 8a26b7ba9b55f479caca15a5bc5b2bcc7317b469

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Could not connect to the database: " . $e->getMessage();
    exit;
}
?>