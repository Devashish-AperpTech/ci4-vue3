<?php

namespace App\Controllers;

use App\Services\InvoiceService;

class InvoiceController extends BaseController
{
    private int $customerId;
    private InvoiceService $invoiceService;

    public function __construct()
    {
        if (!session()->get('isLoggedIn')) {
            redirect()->to('/login');
            exit();
        }

        $this->customerId = (int) session()->get('customer_id');
        $this->invoiceService = new InvoiceService();
    }

    public function index()
    {
        return view('invoices/index', [
            'title' => 'Manage Invoices',
            'activePage' => 'invoices',
        ]);
    }

    public function create()
    {
        return view('invoices/form', [
            'title' => 'Create Invoice',
            'activePage' => 'invoices',
            'mode' => 'create',
            'invoiceId' => null,
            'invoiceData' => $this->emptyInvoice(),
            'invoiceSchema' => $this->invoiceSchema(),
        ]);
    }

    public function edit($invoiceId)
    {
        $invoice = $this->invoiceService->getInvoice((int) $invoiceId, $this->customerId);

        if (!$invoice) {
            return redirect()->to('/invoices')->with('error', 'Invoice not found');
        }

        return view('invoices/form', [
            'title' => 'Edit Invoice',
            'activePage' => 'invoices',
            'mode' => 'edit',
            'invoiceId' => (int) $invoiceId,
            'invoiceData' => $invoice,
            'invoiceSchema' => $this->invoiceSchema(),
        ]);
    }

    public function getData()
    {
        return $this->response->setJSON([
            'success' => true,
            'data' => $this->invoiceService->getUserInvoices($this->customerId),
        ]);
    }

    public function getInvoice($invoiceId)
    {
        $invoice = $this->invoiceService->getInvoice((int) $invoiceId, $this->customerId);

        if (!$invoice) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invoice not found',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $invoice,
        ]);
    }

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $result = $this->invoiceService->createInvoice($this->customerId, $this->invoicePayload());

        if ($result['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $result['message'],
                'redirect' => site_url('/invoices'),
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'errors' => [$result['message']],
        ]);
    }

    public function update($invoiceId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $result = $this->invoiceService->updateInvoice((int) $invoiceId, $this->customerId, $this->invoicePayload());

        if ($result['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $result['message'],
                'redirect' => site_url('/invoices'),
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'errors' => [$result['message']],
        ]);
    }

    public function delete($invoiceId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $result = $this->invoiceService->deleteInvoice((int) $invoiceId, $this->customerId);

        return $this->response->setJSON([
            'success' => $result['success'],
            'message' => $result['message'],
        ]);
    }

    private function invoicePayload(): array
    {
        return [
            'InvoiceNumber' => $this->request->getPost('InvoiceNumber'),
            'invoice_data' => $this->request->getPost('invoice_data'),
        ];
    }

    private function emptyInvoice(): array
    {
        return [
            'InvoiceNumber' => '',
            'IssueDate' => '',
            'CreationDate' => '',
            'ExpirationDate' => '',
            'CustomExpirationDate' => '',
            'Year' => date('Y'),
            'Status' => '',
            'TotalAmount' => '',
            'TaxAmount' => '',
            'Paid' => '',
            'PaymentStatus' => '',
            'PaymentMean' => '',
            'CustomPaymentMean' => '',
            'invoice_data' => '',
        ];
    }

    private function invoiceSchema(): array
    {
        $json = <<<'JSON'
            {
            "form": {
                "id": "fattura-elettronica",
                "title": "Fattura Elettronica",
                "layout": "sidebar",
                "sections": [
                {
                    "id": "dati-trasmissione",
                    "title": "Dati Trasmissione",
                    "type": "section",
                    "fields": [
                    {
                        "id": "id-trasmittente",
                        "type": "group",
                        "label": "Id Trasmittente",
                        "columns": 2,
                        "fields": [
                        {"id": "id-paese", "type": "text", "label": "Id Paese", "placeholder": "IT", "required": true, "validation": {"maxLength": 2, "pattern": "^[A-Z]{2}$"}},
                        {"id": "id-codice", "type": "text", "label": "Id Codice", "placeholder": "12345678901", "required": true, "validation": {"maxLength": 28}}
                        ]
                    },
                    {"id": "progressivo-invio", "type": "text", "label": "Progressivo Invio", "placeholder": "00001", "required": true},
                    {"id": "formato-trasmissione", "type": "select", "label": "Formato Trasmissione", "required": true, "options": [{"label": "FPR12 (Privati)", "value": "FPR12"}, {"label": "FPA12 (PA)", "value": "FPA12"}]},
                    {"id": "codice-destinatario", "type": "text", "label": "Codice Destinatario", "placeholder": "0000000", "required": true},
                    {"id": "pec-destinatario", "type": "email", "label": "PEC Destinatario", "placeholder": "example@pec.it", "required": false, "visibility": {"dependsOn": "codice-destinatario", "condition": "equals", "value": "0000000"}}
                    ]
                },
                {
                    "id": "cedente-prestatore",
                    "title": "Cedente / Prestatore",
                    "type": "section",
                    "fields": [
                    {
                        "id": "dati-anagrafici",
                        "type": "group",
                        "label": "Dati Anagrafici",
                        "fields": [
                        {"id": "id-fiscale-iva", "type": "group", "label": "Id Fiscale IVA", "columns": 2, "fields": [{"id": "id-paese", "type": "text", "label": "Id Paese", "required": true}, {"id": "id-codice", "type": "text", "label": "Id Codice", "required": true}]},
                        {"id": "denominazione", "type": "text", "label": "Denominazione", "required": true},
                        {"id": "regime-fiscale", "type": "select", "label": "Regime Fiscale", "required": true, "options": [{"label": "RF01 Ordinario", "value": "RF01"}, {"label": "RF19 Forfettario", "value": "RF19"}]}
                        ]
                    },
                    {
                        "id": "sede",
                        "type": "group",
                        "label": "Sede",
                        "columns": 3,
                        "fields": [
                        {"id": "indirizzo", "type": "text", "label": "Indirizzo", "required": true},
                        {"id": "numero-civico", "type": "text", "label": "Numero Civico"},
                        {"id": "cap", "type": "text", "label": "CAP", "required": true},
                        {"id": "comune", "type": "text", "label": "Comune", "required": true},
                        {"id": "provincia", "type": "text", "label": "Provincia"},
                        {"id": "nazione", "type": "select", "label": "Nazione", "required": true, "options": [{"label": "IT", "value": "IT"}]}
                        ]
                    }
                    ]
                },
                {
                    "id": "dati-generali",
                    "title": "Dati Generali Documento",
                    "type": "section",
                    "fields": [
                    {"id": "tipo-documento", "type": "select", "label": "Tipo Documento", "required": true, "options": [{"label": "TD01 Fattura", "value": "TD01"}]},
                    {"id": "divisa", "type": "select", "label": "Divisa", "required": true, "options": [{"label": "EUR", "value": "EUR"}]},
                    {"id": "data", "type": "date", "label": "Data", "required": true},
                    {"id": "numero", "type": "text", "label": "Numero", "required": true}
                    ]
                },
                {
                    "id": "dati-beni-servizi",
                    "title": "Dati Beni e Servizi",
                    "type": "section",
                    "fields": [
                    {
                        "id": "dettaglio-linee",
                        "type": "array",
                        "label": "Dettaglio Linee",
                        "itemLabel": "Linea",
                        "fields": [
                        {"id": "numero-linea", "type": "number", "label": "Numero Linea", "required": true},
                        {"id": "descrizione", "type": "textarea", "label": "Descrizione", "required": true},
                        {"id": "quantita", "type": "number", "label": "Quantita", "required": true},
                        {"id": "prezzo-unitario", "type": "number", "label": "Prezzo Unitario", "required": true},
                        {"id": "prezzo-totale", "type": "number", "label": "Prezzo Totale", "required": true},
                        {"id": "aliquota-iva", "type": "number", "label": "Aliquota IVA", "required": true}
                        ]
                    }
                    ]
                }
                ]
            }
            }
            JSON;

        return json_decode($json, true);
    }
}
