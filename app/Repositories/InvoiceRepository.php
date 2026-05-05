<?php

namespace App\Repositories;

use App\Models\InvoiceModel;

class InvoiceRepository extends BaseRepository
{
    protected InvoiceModel $invoiceModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        parent::__construct($this->invoiceModel);
    }

    public function getUserInvoices(int $customerId): array
    {
        return $this->invoiceModel->getUserInvoices($customerId);
    }

    public function getUserInvoice(int $invoiceId, int $customerId): ?array
    {
        return $this->invoiceModel->getUserInvoice($invoiceId, $customerId);
    }

    public function createInvoice(int $customerId, array $data)
    {
        return $this->invoiceModel->createInvoice($customerId, $data);
    }

    public function updateInvoice(int $invoiceId, int $customerId, array $data): bool
    {
        return $this->invoiceModel->updateInvoice($invoiceId, $customerId, $data);
    }

    public function deleteInvoice(int $invoiceId, int $customerId): bool
    {
        return $this->invoiceModel->deleteInvoice($invoiceId, $customerId);
    }
}
