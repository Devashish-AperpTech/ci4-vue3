<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Throwable;

class UpdateDgtCustomersTable extends Migration
{
    private string $table = 'dgt_customers';
    private string $constraint = 'fk_dgt_customers_parent_id';
    private string $index = 'idx_dgt_customers_parent_id';

    public function up()
    {
        if (!$this->db->tableExists($this->table)) {
            return;
        }

        if (!$this->db->fieldExists('parent_id', $this->table)) {
            $this->forge->addColumn($this->table, [
                'parent_id' => [
                    'type' => 'INT',
                    'null' => true,
                ],
            ]);
        }

        $this->db->query("
            UPDATE {$this->table} c
            SET parent_id = NULL
            WHERE parent_id IS NOT NULL
            AND NOT EXISTS (
                SELECT 1 FROM {$this->table} p
                WHERE p.customer_id = c.parent_id
            )
        ");

        try {
            $this->db->query("
                CREATE INDEX {$this->index}
                ON {$this->table}(parent_id)
            ");
        } catch (Throwable $e) {
            // Index already exists
        }

        try {
            $this->db->query("
                ALTER TABLE {$this->table}
                ADD CONSTRAINT {$this->constraint}
                FOREIGN KEY (parent_id)
                REFERENCES {$this->table}(customer_id)
                ON UPDATE CASCADE
                ON DELETE SET NULL
            ");
        } catch (Throwable $e) {
            // Constraint already exists
        }
    }

    public function down()
    {
        if (!$this->db->tableExists($this->table)) {
            return;
        }

        try {
            $this->db->query("
                ALTER TABLE {$this->table}
                DROP CONSTRAINT {$this->constraint}
            ");
        } catch (Throwable $e) {}

        try {
            $this->db->query("
                DROP INDEX {$this->index}
            ");
        } catch (Throwable $e) {}

        
        // $this->forge->dropColumn($this->table, 'parent_id');
    }
}