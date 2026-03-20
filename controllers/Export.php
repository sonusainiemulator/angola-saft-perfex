<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Export extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (!has_permission('angola_saft', '', 'export')) {
            access_denied('angola_saft');
        }
        $this->load->model('invoices_model');
        $this->load->model('credit_notes_model');
        $this->load->helper('angola_saft/angola_saft');
    }

    public function index()
    {
        $data['title'] = 'Angola SAF-T AO Export';
        $data['invoiceStatuses'] = $this->invoices_model->get_statuses();
        $this->load->model('credit_notes_model');
        $data['creditNoteStatuses'] = $this->credit_notes_model->get_statuses();
        $this->load->view('angola_saft/export', $data);
    }

    /**
     * Generate Global SAF-T AO 1.01 XML with filters
     */
    public function generate()
    {
        $export_type = $this->input->get('export_type') ?? 'invoice';
        $status      = $this->input->get('status') ?? 'all';
        $cn_status   = $this->input->get('cn_status') ?? 'all';
        $period      = $this->input->get('period') ?? 'all_time';

        // Set date range based on period
        $date_from = null;
        $date_to   = null;

        if ($period == 'all_time') {
            $date_from = null;
            $date_to   = null;
        } elseif ($period == 'this_month') {
            $date_from = date('Y-m-01');
            $date_to   = date('Y-m-t');
        } elseif ($period == 'last_month') {
            $date_from = date('Y-m-01', strtotime('-1 month'));
            $date_to   = date('Y-m-t', strtotime('-1 month'));
        } elseif ($period == 'this_year') {
            $date_from = date('Y-01-01');
            $date_to   = date('Y-12-31');
        } elseif ($period == 'last_year') {
            $date_from = date('Y-01-01', strtotime('-1 year'));
            $date_to   = date('Y-12-31', strtotime('-1 year'));
        } elseif ($period == 'custom') {
            $date_from = to_sql_date($this->input->get('from'));
            $date_to   = to_sql_date($this->input->get('to'));
        }

        // Prepare data for template
        $saftData = [
            'COMPANY_VAT'    => get_option('company_vat'),
            'COMPANY_NAME'   => get_option('companyname'),
            'COMPANY_ADDRESS' => get_option('company_street'),
            'COMPANY_CITY'    => get_option('company_city'),
            'INVOICE_DATE_FROM' => $date_from ?: '2000-01-01',
            'INVOICE_DATE_TO'   => $date_to ?: date('Y-m-d'),
            'CURRENCY_CODE'  => get_base_currency()->name,
            'CERTIFICATE_NO' => get_option('angola_saft_certification_no'),
            'SOFTWARE_NAME'  => 'Perfex CRM Angola',
            'INVOICES'       => [],
            'CUSTOMERS'      => [],
            'PRODUCTS'       => [],
            'TOTAL_CREDIT'   => 0.00,
            'TOTAL_DEBIT'    => 0.00
        ];

        $collected_customers = [];
        $collected_products  = [];

        // 1. Fetch Invoices
        $this->db->select(db_prefix() . 'invoices.*, ' . db_prefix() . 'saft_ao_hashes.hash, ' . db_prefix() . 'saft_ao_hashes.prev_hash, ' . db_prefix() . 'saft_ao_hashes.signature, ' . db_prefix() . 'saft_ao_hashes.signing_key_version as hash_control');
        $this->db->join(db_prefix() . 'saft_ao_hashes', db_prefix() . 'saft_ao_hashes.rel_id = ' . db_prefix() . 'invoices.id AND rel_type = "invoice"', 'left');
        if ($date_from) { $this->db->where('date >=', $date_from); }
        if ($date_to) { $this->db->where('date <=', $date_to); }
        if ($status !== 'all') { $this->db->where('status', $status); }
        $invoices = $this->db->get(db_prefix() . 'invoices')->result();

        foreach ($invoices as $item) {
            $einvoiceData = new \Perfexcrm\EInvoice\Data\Invoice($item);
            $docData = $einvoiceData->jsonSerialize();
            $docData = angola_saft_inject_template_data($docData);
            
            if (empty($docData['HASH']) && !empty($item->hash)) {
                $docData['HASH'] = $item->hash;
                $docData['HASH_CONTROL'] = $item->hash_control;
            }
            $docData['INVOICE_TYPE_CODE'] = 'FT'; // Fatura
            $docData['SAFT_DOCUMENT_TOTAL'] = $docData['INVOICE_TOTAL'];

            $saftData['INVOICES'][] = $docData;
            $saftData['TOTAL_CREDIT'] += $docData['SAFT_DOCUMENT_TOTAL'];

            // Extract Customer
            $customer_id = $docData['CUSTOMER_ID'] ?? '1';
            if (!isset($collected_customers[$customer_id])) {
                $collected_customers[$customer_id] = [
                    'CUSTOMER_ID'      => $customer_id,
                    'CUSTOMER_VAT'     => $docData['CUSTOMER_VAT'] ?? '999999999',
                    'CUSTOMER_NAME'    => $docData['CUSTOMER_NAME'] ?? 'Consumidor Final',
                    'CUSTOMER_ADDRESS' => $docData['CUSTOMER_ADDRESS'] ?? 'Desconhecido',
                    'CUSTOMER_CITY'    => $docData['CUSTOMER_CITY'] ?? 'Desconhecido',
                ];
            }

            // Extract Products
            if (isset($docData['LINE_ITEMS']) && is_array($docData['LINE_ITEMS'])) {
                foreach ($docData['LINE_ITEMS'] as $line) {
                    $item_id = $line['ITEM_ID'] ?? '1';
                    if (!isset($collected_products[$item_id])) {
                        $collected_products[$item_id] = [
                            'ITEM_ID'          => $item_id,
                            'ITEM_DESCRIPTION' => $line['ITEM_DESCRIPTION'] ?? 'Produto',
                        ];
                    }
                }
            }
        }

        // 2. Fetch Credit Notes
        $this->db->select(db_prefix() . 'creditnotes.*, ' . db_prefix() . 'saft_ao_hashes.hash, ' . db_prefix() . 'saft_ao_hashes.prev_hash, ' . db_prefix() . 'saft_ao_hashes.signature, ' . db_prefix() . 'saft_ao_hashes.signing_key_version as hash_control');
        $this->db->join(db_prefix() . 'saft_ao_hashes', db_prefix() . 'saft_ao_hashes.rel_id = ' . db_prefix() . 'creditnotes.id AND rel_type = "credit_note"', 'left');
        if ($date_from) { $this->db->where('date >=', $date_from); }
        if ($date_to) { $this->db->where('date <=', $date_to); }
        if ($cn_status !== 'all') { $this->db->where('status', $cn_status); }
        $credit_notes = $this->db->get(db_prefix() . 'creditnotes')->result();

        foreach ($credit_notes as $item) {
            $einvoiceData = new \Perfexcrm\EInvoice\Data\CreditNote($item);
            $docData = $einvoiceData->jsonSerialize();
            $docData = angola_saft_inject_template_data($docData);
            
            if (empty($docData['HASH']) && !empty($item->hash)) {
                $docData['HASH'] = $item->hash;
                $docData['HASH_CONTROL'] = $item->hash_control;
            }
            $docData['INVOICE_TYPE_CODE'] = 'NC'; // Nota de Crédito
            $docData['SAFT_DOCUMENT_TOTAL'] = $docData['INVOICE_TOTAL'];

            $saftData['INVOICES'][] = $docData;
            $saftData['TOTAL_DEBIT'] += $docData['SAFT_DOCUMENT_TOTAL']; // Credit notes increase Debit in SAFT

            // Extract Customer
            $customer_id = $docData['CUSTOMER_ID'] ?? '1';
            if (!isset($collected_customers[$customer_id])) {
                $collected_customers[$customer_id] = [
                    'CUSTOMER_ID'      => $customer_id,
                    'CUSTOMER_VAT'     => $docData['CUSTOMER_VAT'] ?? '999999999',
                    'CUSTOMER_NAME'    => $docData['CUSTOMER_NAME'] ?? 'Consumidor Final',
                    'CUSTOMER_ADDRESS' => $docData['CUSTOMER_ADDRESS'] ?? 'Desconhecido',
                    'CUSTOMER_CITY'    => $docData['CUSTOMER_CITY'] ?? 'Desconhecido',
                ];
            }

            // Extract Products
            if (isset($docData['LINE_ITEMS']) && is_array($docData['LINE_ITEMS'])) {
                foreach ($docData['LINE_ITEMS'] as $line) {
                    $item_id = $line['ITEM_ID'] ?? '1';
                    if (!isset($collected_products[$item_id])) {
                        $collected_products[$item_id] = [
                            'ITEM_ID'          => $item_id,
                            'ITEM_DESCRIPTION' => $line['ITEM_DESCRIPTION'] ?? 'Produto',
                        ];
                    }
                }
            }
        }

        $saftData['CUSTOMERS'] = array_values($collected_customers);
        $saftData['PRODUCTS']  = array_values($collected_products);
        $saftData['TOTAL_ENTRIES'] = count($saftData['INVOICES']);

        // Render using the global template
        $template = $this->load->view('angola_saft/saft_global_xml', $saftData, true);

        // Download
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="SAFT_AO_Documents_' . ($date_from ?: 'all') . '.xml"');
        echo $template;
        exit;
    }

}
