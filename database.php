<?php
class database {
	private $filename;
    private $data;
    private $log_filename = 'log.txt'; 
	private $backup_filename = 'backup.json'; 

	function __construct($filename) {
        $this->filename = $filename;
        if (!file_exists($filename)) {
            $this->data = [];
            $this->save();
        } else {
            $this->data = json_decode(file_get_contents($filename), true);
        }
	}

    function save() {
        $encryptedData = base64_encode(json_encode($this->data));
        file_put_contents($this->filename, $encryptedData);
    }

    function create_table($name, $fields) {
        $table = ['fields' => $fields, 'data' => []];
        $this->data[$name] = $table;
        $this->save();
    }

    function insert($table_name, $data) {
        if (empty($data)) {
            return false;
        }

        $table = $this->data[$table_name];
        $table['data'][] = $data;
        $this->data[$table_name] = $table;
        $this->save();
        return true;
    }

    function select_all($table_name) {
        $table = $this->data[$table_name];
        return $table['data'];
    }

    function select_where($table_name, $field, $value) {
        $table = $this->data[$table_name];
        $result = [];
        foreach ($table['data'] as $row) {
            if ($row[$field] == $value) {
                $result[] = $row;
            }
        }
        return $result;
    }

    function update_where($table_name, $field, $value, $data) {
        if (empty($data)) {
            return false;
        }

        $table = $this->data[$table_name];
        foreach ($table['data'] as &$row) {
            if ($row[$field] == $value) {
                foreach ($data as $key => $value) {
                    $row[$key] = $value;
                }
            }
        }
        $this->data[$table_name] = $table;
        $this->save();
        return true;
    }

    function delete_where($table_name, $field, $value) {
        $table = $this->data[$table_name];
        $new_data = [];
        foreach ($table['data'] as $row) {
            if ($row[$field] != $value) {
                $new_data[] = $row;
            }
        }
        $table['data'] = $new_data;
        $this->data[$table_name] = $table;
        $this->save();
    }

    function define_foreign_key($table_name, $field, $reference_table, $reference_field) {
        if (!isset($this->data[$table_name]['fields'][$field])) {
            return false;
        }

        $this->data[$table_name]['fields'][$field]['foreign_key'] = [
            'table' => $reference_table,
            'field' => $reference_field
        ];

        $this->save();
        return true;
    }

    function create_index($table_name, $field) {
        if (!isset($this->data[$table_name]['fields'][$field])) {
            return false;
        }

        $this->data[$table_name]['fields'][$field]['index'] = true;
        $this->save();
        return true;
    }

    
    function set_table_permissions($table_name, $read = true, $insert = true, $update = true, $delete = true) {
        $this->data[$table_name]['permissions'] = [
            'read' => $read,
            'insert' => $insert,
            'update' => $update,
            'delete' => $delete
        ];
        $this->save();
    }

    // Paginación
    function get_paginated_data($table_name, $page = 1, $items_per_page = 10) {
        $table = $this->data[$table_name];
        $total_items = count($table['data']);
        $total_pages = ceil($total_items / $items_per_page);
        $start_index = ($page - 1) * $items_per_page;
        $end_index = $start_index + $items_per_page;
        $data = array_slice($table['data'], $start_index, $items_per_page);

        return [
            'data' => $data,
            'page' => $page,
            'total_pages' => $total_pages
        ];
    }

    function create_backup($backup_filename) {
        $backup_data = base64_encode(json_encode($this->data));
        file_put_contents($backup_filename, $backup_data);
    }

	function log_operation($operation, $table_name, $data) {
        $log_entry = date('Y-m-d H:i:s') . " - Operation: {$operation}, Table: {$table_name}, Data: " . json_encode($data) . PHP_EOL;
        file_put_contents($this->log_filename, $log_entry, FILE_APPEND);
	}

	function backup() {
        $backup_data = json_encode($this->data);
        file_put_contents($this->backup_filename, $backup_data);
	}
}
