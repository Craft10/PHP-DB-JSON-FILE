# PHP-DB-JSON-FILE
PHP DB JSON FILE is a database system with .json file in which you can add tables, logs, backup

```
$db = new database('database.json');

$db->create_table('table_name', [
    'columm1' => 'VARCHAR(50)',
    'columm2' => 'VARCHAR(50) UNIQUE',
    'columm3' => 'VARCHAR(100)'
]);
```
