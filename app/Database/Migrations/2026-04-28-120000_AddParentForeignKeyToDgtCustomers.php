<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Throwable;

class AddParentForeignKeyToDgtCustomers extends Migration
{
    private string $table = 'dgt_customers';
    private string $constraint = 'fk_dgt_customers_parent_id';

    public function up()
    {
        if (!$this->db->tableExists($this->table)) {
            return;
        }

        if (!$this->db->fieldExists('parent_id', $this->table)) {
            $this->forge->addColumn($this->table, [
                'parent_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                    'after' => 'customer_identifier_code',
                ],
            ]);
        }

        // Keep data valid before adding FK:
        // any parent_id value that does not exist in dgt_customers.customer_id becomes NULL.
        $this->db->query(
            "UPDATE {$this->table} c
             LEFT JOIN {$this->table} p ON p.customer_id = c.parent_id
             SET c.parent_id = NULL
             WHERE c.parent_id IS NOT NULL AND p.customer_id IS NULL"
        );

        try {
            $this->db->query(
                "ALTER TABLE {$this->table}
                 ADD INDEX idx_dgt_customers_parent_id (parent_id)"
            );
        } catch (Throwable $e) {
            // index may already exist
        }

        try {
            $this->db->query(
                "ALTER TABLE {$this->table}
                 ADD CONSTRAINT {$this->constraint}
                 FOREIGN KEY (parent_id)
                 REFERENCES {$this->table}(customer_id)
                 ON UPDATE CASCADE
                 ON DELETE SET NULL"
            );
        } catch (Throwable $e) {
            // constraint may already exist
        }
    }

    public function down()
    {
        if (!$this->db->tableExists($this->table)) {
            return;
        }

        try {
            $this->db->query(
                "ALTER TABLE {$this->table} DROP FOREIGN KEY {$this->constraint}"
            );
        } catch (Throwable $e) {
            // constraint may not exist
        }

        try {
            $this->db->query(
                "ALTER TABLE {$this->table} DROP INDEX idx_dgt_customers_parent_id"
            );
        } catch (Throwable $e) {
            // index may not exist
        }
    }
}

