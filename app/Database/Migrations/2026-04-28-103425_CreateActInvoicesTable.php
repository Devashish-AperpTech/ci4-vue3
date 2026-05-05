<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActInvoicesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'customer_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'InvoiceNumber' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'IssueDate' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'CreationDate' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'ExpirationDate' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'CustomExpirationDate' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'Year' => [
                'type' => 'INT',
                'null' => true,
            ],
            'Status' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'TotalAmount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'TaxAmount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'Paid' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'PaymentStatus' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'PaymentMean' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'CustomPaymentMean' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'invoice_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('customer_id', 'dgt_customers', 'customer_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('act_invoices');
    }

    public function down()
    {
        $this->forge->dropTable('act_invoices');
    }
}
