<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table = 'act_invoices';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'customer_id',
        'InvoiceNumber',
        'IssueDate',
        'CreationDate',
        'ExpirationDate',
        'CustomExpirationDate',
        'Year',
        'Status',
        'TotalAmount',
        'TaxAmount',
        'Paid',
        'PaymentStatus',
        'PaymentMean',
        'CustomPaymentMean',
        'invoice_data',
        'invoice_xml'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'customer_id' => 'required|integer',
        'InvoiceNumber' => 'required|max_length[100]',
        'IssueDate' => 'permit_empty|valid_date[Y-m-d]',
        'CreationDate' => 'permit_empty|valid_date[Y-m-d H:i:s]',
        'ExpirationDate' => 'permit_empty|valid_date[Y-m-d]',
        'CustomExpirationDate' => 'permit_empty|valid_date[Y-m-d]',
        'Year' => 'permit_empty|integer',
        'Status' => 'permit_empty|max_length[100]',
        'TotalAmount' => 'permit_empty|decimal',
        'TaxAmount' => 'permit_empty|decimal',
        'Paid' => 'permit_empty|max_length[50]',
        'PaymentStatus' => 'permit_empty|max_length[100]',
        'PaymentMean' => 'permit_empty|max_length[100]',
        'CustomPaymentMean' => 'permit_empty|max_length[100]',
        'invoice_data' => 'permit_empty',
        'invoice_xml' => 'permit_empty'
    ];

    public function getUserInvoices(int $customerId): array
    {
        return $this->where('customer_id', $customerId)
            ->orderBy('CreationDate', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll(); 
    }

    public function getUserInvoice(int $invoiceId, int $customerId): ?array
    {
        return $this->where('id', $invoiceId)
            ->where('customer_id', $customerId)
            ->first();
    }

    public function createInvoice(int $customerId, array $data)
    {
        $data['customer_id'] = $customerId;
        return $this->insert($data);
    }

    public function updateInvoice(int $invoiceId, int $customerId, array $data): bool
    {
        if (!$this->getUserInvoice($invoiceId, $customerId)) {
            return false;
        }

        return (bool) $this->update($invoiceId, $data);
    }

    public function deleteInvoice(int $invoiceId, int $customerId): bool
    {
        if (!$this->getUserInvoice($invoiceId, $customerId)) {
            return false;
        }

        return (bool) $this->delete($invoiceId);
    }
}
