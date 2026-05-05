<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDgtCustomersTable extends Migration
{
    private string $table = 'dgt_customers';
    public function up()
    {
        if ($this->db->tableExists($this->table)) {
            return;
        }

        $this->forge->addField([
            'customer_id' => [
                'type'           => 'SERIAL', // PostgreSQL auto-increment
                'unsigned'       => false,
                'null'           => false,
            ],
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'customer_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'unique'     => true,
            ],
            'customer_vat_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'customer_identifier_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'customer_password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'parent_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'settings' => [
                'type' => 'JSONB', // PostgreSQL specific
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('customer_id', true); // Primary Key

        $this->forge->createTable($this->table);
    }

    public function down()
    {
        if ($this->db->tableExists($this->table)) {
            $this->forge->dropTable($this->table);
        }
    }
}
