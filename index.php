<?php
require_once 'database.php';

#EXAMPLE USAGE

$db = new database('database.json');//Puedes Usar cualquier extensión de DB

$db->create_table('table_name', [
    'columm1' => 'VARCHAR(50)',
    'columm2' => 'VARCHAR(50) UNIQUE',
    'columm3' => 'VARCHAR(100)'
]);

$db->insert('table_name', [
    'columm1' => 'Data1',
    'columm2' => 'Data2',
    'columm3' => 'Data3'
]);


$rows = $db->select_all('table_name');
var_dump($rows);