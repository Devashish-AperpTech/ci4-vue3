<?php

namespace App\Services;

use App\Repositories\InvoiceRepository;

class InvoiceService extends BaseService
{
    private InvoiceRepository $invoiceRepository;

    public function __construct()
    {
        $this->invoiceRepository = new InvoiceRepository();
    }

    public function getUserInvoices(int $customerId): array
    {
        return $this->invoiceRepository->getUserInvoices($customerId);
    }

    public function getInvoice(int $invoiceId, int $customerId): ?array
    {
        return $this->invoiceRepository->getUserInvoice($invoiceId, $customerId);
    }

    public function createInvoice(int $customerId, array $data): array
    {
        $validation = $this->validateInvoiceData($data);
        if (!$validation['valid']) {
            return $this->respondWithError(implode(', ', $validation['errors']));
        }

        $invoiceId = $this->invoiceRepository->createInvoice($customerId, $this->prepareInvoiceData($data));

        if (!$invoiceId) {
            return $this->respondWithError('Failed to save invoice');
        }

        return $this->respondWithSuccess('Invoice created successfully', ['invoice_id' => $invoiceId]);
    }

    public function updateInvoice(int $invoiceId, int $customerId, array $data): array
    {
        if (!$this->invoiceRepository->getUserInvoice($invoiceId, $customerId)) {
            return $this->respondWithError('Invoice not found or access denied');
        }

        $validation = $this->validateInvoiceData($data);
        if (!$validation['valid']) {
            return $this->respondWithError(implode(', ', $validation['errors']));
        }

        if (!$this->invoiceRepository->updateInvoice($invoiceId, $customerId, $this->prepareInvoiceData($data))) {
            return $this->respondWithError('Failed to update invoice');
        }

        return $this->respondWithSuccess('Invoice updated successfully');
    }

    public function deleteInvoice(int $invoiceId, int $customerId): array
    {
        if (!$this->invoiceRepository->deleteInvoice($invoiceId, $customerId)) {
            return $this->respondWithError('Invoice not found or access denied');
        }

        return $this->respondWithSuccess('Invoice deleted successfully');
    }

    private function validateInvoiceData(array $data): array
    {
        $errors = [];

        if (empty(trim((string) ($data['InvoiceNumber'] ?? '')))) {
            $errors[] = 'Invoice number is required';
        }

        $schema = $this->decodeInvoiceData($data['invoice_data'] ?? '');
        if (!$schema || empty($schema['form']['sections'])) {
            $errors[] = 'Invoice data is required';
            return ['valid' => false, 'errors' => $errors];
        }

        foreach ($schema['form']['sections'] as $section) {
            $this->validateFields($section['fields'] ?? [], $schema, $errors);
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }

    private function prepareInvoiceData(array $data): array
    {
        $schema = $this->decodeInvoiceData($data['invoice_data'] ?? '') ?? [];
        $formData = $this->extractFormValues($schema);
        $map = [
            'id-paese' => 'IdPaese',
            'id-codice' => 'IdCodice',
            'progressivo-invio' => 'ProgressivoInvio',
            'formato-trasmissione' => 'FormatoTrasmissione',
            'codice-destinatario' => 'CodiceDestinatario',
            'pec-destinatario' => 'PECDestinatario',

            'denominazione' => 'Denominazione',
            'regime-fiscale' => 'RegimeFiscale',

            'indirizzo' => 'Indirizzo',
            'numero-civico' => 'NumeroCivico',
            'cap' => 'CAP',
            'comune' => 'Comune',
            'provincia' => 'Provincia',
            'nazione' => 'Nazione',

            'tipo-documento' => 'TipoDocumento',
            'divisa' => 'Divisa',
            'data' => 'Data',
            'numero' => 'Numero',

            'dettaglio-linee' => 'DettaglioLinee',
            'dati-beni-servizi' => 'DatiBeniServizi',
            'numero-linea' => 'NumeroLinea',
            'descrizione' => 'Descrizione',
            'quantita' => 'Quantita',
            'prezzo-unitario' => 'PrezzoUnitario',
            'prezzo-totale' => 'PrezzoTotale',
            'aliquota-iva' => 'AliquotaIVA',
        ];
        $xmlData = $this->convertToXml($schema, $formData, $map);
        file_put_contents(
            WRITEPATH . 'logs/invoice-debug.txt',
            date('c') . " | schema:\n" . print_r($schema, true)." | formData:\n" . print_r($formData, true)
            . "\nxmlData:\n" . substr($xmlData, 0, 2000) . "\n",
            FILE_APPEND
        );
        $issueDate = $this->findValueById($schema, 'data');
        $lineTotals = $this->findAllValuesById($schema, 'prezzo-totale');

        $totalAmount = array_reduce($lineTotals, function ($sum, $value) {
            return is_numeric($value) ? $sum + (float) $value : $sum;
        }, 0.0);

        return [
            'InvoiceNumber' => trim((string) ($data['InvoiceNumber'] ?? '')),
            'IssueDate' => $this->nullIfEmpty($issueDate),
            'CreationDate' => date('Y-m-d H:i:s'),
            'ExpirationDate' => null,
            'CustomExpirationDate' => null,
            'Year' => $issueDate ? date('Y', strtotime((string) $issueDate)) : null,
            'Status' => 'draft',
            'TotalAmount' => $totalAmount > 0 ? $totalAmount : null,
            'TaxAmount' => null,
            'Paid' => null,
            'PaymentStatus' => null,
            'PaymentMean' => null,
            'CustomPaymentMean' => null,
            'invoice_data' => json_encode($schema),
            'invoice_xml' => $xmlData,
        ];
    }

    private function validateFields(array $fields, array $schema, array &$errors): void
    {
        foreach ($fields as $field) {
            if (!$this->isVisible($field, $schema)) {
                continue;
            }

            if (($field['type'] ?? '') === 'group') {
                $this->validateFields($field['fields'] ?? [], $schema, $errors);
                continue;
            }

            if (($field['type'] ?? '') === 'array') {
                foreach (($field['items'] ?? []) as $item) {
                    $this->validateFields($item, $schema, $errors);
                }
                continue;
            }

            $value = $field['value'] ?? '';
            $label = $field['label'] ?? $field['id'] ?? 'Field';

            if (($field['required'] ?? false) && trim((string) $value) === '') {
                $errors[] = $label . ' is required';
                continue;
            }

            if (trim((string) $value) === '') {
                continue;
            }

            $this->validateFieldType($field, $value, $label, $errors);
        }
    }

    private function validateFieldType(array $field, $value, string $label, array &$errors): void
    {
        switch ($field['type'] ?? 'text') {
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = $label . ' must be a valid email';
                }
                break;
            case 'date':
                if (strtotime((string) $value) === false) {
                    $errors[] = $label . ' must be a valid date';
                }
                break;
            case 'number':
                if (!is_numeric($value)) {
                    $errors[] = $label . ' must be numeric';
                }
                break;
        }

        $validation = $field['validation'] ?? [];
        if (!empty($validation['maxLength']) && strlen((string) $value) > (int) $validation['maxLength']) {
            $errors[] = $label . ' must be at most ' . $validation['maxLength'] . ' characters';
        }

        if (!empty($validation['pattern']) && !preg_match('/' . str_replace('/', '\/', $validation['pattern']) . '/', (string) $value)) {
            $errors[] = $label . ' has an invalid format';
        }
    }

    private function isVisible(array $field, array $schema): bool
    {
        if (empty($field['visibility'])) {
            return true;
        }

        $visibility = $field['visibility'];
        $current = $this->findValueById($schema, $visibility['dependsOn'] ?? '');

        if (($visibility['condition'] ?? '') === 'equals') {
            return (string) $current === (string) ($visibility['value'] ?? '');
        }

        return true;
    }

    private function decodeInvoiceData(string $invoiceData): ?array
    {
        if (trim($invoiceData) === '') {
            return null;
        }

        $decoded = json_decode($invoiceData, true);
        return json_last_error() === JSON_ERROR_NONE && is_array($decoded) ? $decoded : null;
    }

    private function findValueById(array $schema, string $id)
    {
        $values = $this->findAllValuesById($schema, $id);
        return $values[0] ?? null;
    }

    private function findAllValuesById(array $schema, string $id): array
    {
        $values = [];
        foreach (($schema['form']['sections'] ?? []) as $section) {
            $this->collectValuesById($section['fields'] ?? [], $id, $values);
        }
        return $values;
    }

    private function collectValuesById(array $fields, string $id, array &$values): void
    {
        foreach ($fields as $field) {
            if (($field['type'] ?? '') === 'group') {
                $this->collectValuesById($field['fields'] ?? [], $id, $values);
                continue;
            }

            if (($field['type'] ?? '') === 'array') {
                foreach (($field['items'] ?? []) as $item) {
                    $this->collectValuesById($item, $id, $values);
                }
                continue;
            }

            if (($field['id'] ?? '') === $id) {
                $values[] = $field['value'] ?? null;
            }
        }
    }

    private function nullIfEmpty($value)
    {
        return $value === '' || $value === null ? null : $value;
    }

    // xml related
    private function extractFormValues(array $schema): array
    {
        $result = [];

        foreach ($schema['form']['sections'] ?? [] as $section) {
            $this->extractFields($section['fields'] ?? [], $result);
        }

        return $result;
    }

    private function extractFields(array $fields, array &$result)
    {
        foreach ($fields as $field) {

            // GROUP
            if (($field['type'] ?? '') === 'group') {
                $this->extractFields($field['fields'] ?? [], $result);
            }

            // ARRAY
            elseif (($field['type'] ?? '') === 'array') {
                $id = $field['id'] ?? null;

                if ($id && !empty($field['items'])) {
                    foreach ($field['items'] as $index => $item) {
                        $result[$id][$index] = [];
                        $this->extractFields($item, $result[$id][$index]);
                    }
                }
            }

            // FIELD
            else {
                if (isset($field['id'], $field['value'])) {
                    $result[$field['id']] = $field['value'];
                }
            }
        }
    }

    private function convertToXml(array $schema, array $data, array $map): string
    {
        $xml = new \SimpleXMLElement('<FatturaElettronica/>');

        foreach ($schema['form']['sections'] as $section) {

            $sectionNode = $xml->addChild($this->toXmlTag($section['id']));

            $this->processFields($section['fields'], $sectionNode, $data, $map);
        }

        return $xml->asXML();
    }

    private function processFields(array $fields, \SimpleXMLElement $xmlNode, array $data, array $map)
    {
        foreach ($fields as $field) {

            // GROUP
            if ($field['type'] === 'group') {
                $groupNode = $xmlNode->addChild($this->toXmlTag($field['id']));
                $this->processFields($field['fields'], $groupNode, $data, $map);
            }

            // ARRAY
            elseif ($field['type'] === 'array') {

                if (!isset($data[$field['id']])) continue;

                foreach ($data[$field['id']] as $item) {
                    $arrayNode = $xmlNode->addChild($this->toXmlTag($field['id']));
                    $this->processFields($field['fields'], $arrayNode, $item, $map);
                }
            }

            // FIELD
            else {
                $id = $field['id'];

                if (!isset($data[$id])) continue;
                if (!isset($map[$id])) continue;

                $value = $data[$id];

                if ($value === '' || $value === null) continue;

                $xmlNode->addChild($map[$id], htmlspecialchars($value));
            }
        }
    }

    private function toXmlTag(string $id): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $id)));
    }
}
