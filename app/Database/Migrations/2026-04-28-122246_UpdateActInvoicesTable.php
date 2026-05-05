<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateActInvoicesTable extends Migration
{
    public function up()
    {
       $fields = [
            'invoice_xml' => [
                'type' => 'TEXT',
                'null' => true,
            ],
    ];

    $this->forge->addColumn('act_invoices', $fields);
    }

   
    public function down()
    {
        $this->forge->dropColumn('act_invoices', ['new_column']);
    }
}
