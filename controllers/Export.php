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
        $this->load->view('angola_saft/export', $data);
    }

    /**
     * Generate Global SAF-T AO 1.01 XML
     */
    public function generate()
    {
        $date_from = to_sql_date($this->input->get('from'));
        $date_to = to_sql_date($this->input->get('to'));

        if (!$date_from || !$date_to) {
            set_alert('warning', 'Please select a date range.');
            redirect(admin_url('angola_saft/export'));
        }

        // Fetch signed invoices
        $this->db->select('tblinvoices.*, tblsaft_ao_hashes.hash, tblsaft_ao_hashes.prev_hash, tblsaft_ao_hashes.signature');
        $this->db->join(db_prefix() . 'saft_ao_hashes', db_prefix() . 'saft_ao_hashes.rel_id = ' . db_prefix() . 'invoices.id AND rel_type = "invoice"');
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $invoices = $this->db->get(db_prefix() . 'invoices')->result();

        // Prepare data for template
        $saftData = [
            'COMPANY_VAT'    => get_option('company_vat'),
            'COMPANY_NAME'   => get_option('companyname'),
            'COMPANY_ADDRESS' => get_option('company_street'),
            'COMPANY_CITY'    => get_option('company_city'),
            'INVOICE_DATE_FROM' => $date_from,
            'INVOICE_DATE_TO'   => $date_to,
            'CURRENCY_CODE'  => get_base_currency()->name,
            'CERTIFICATE_NO' => get_option('angola_saft_certification_no'),
            'SOFTWARE_NAME'  => 'Perfex CRM Angola',
            'INVOICES'       => []
        ];

        foreach ($invoices as $invoice) {
            $einvoiceData = new \Perfexcrm\EInvoice\Data\Invoice($invoice);
            $docData = $einvoiceData->jsonSerialize();
            $docData = angola_saft_inject_template_data($docData);
            $saftData['INVOICES'][] = $docData;
        }

        // Render using the global template
        $template = $this->load->view('angola_saft/saft_global_xml', $saftData, true);

        // Download
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="SAFT_AO_' . $date_from . '_' . $date_to . '.xml"');
        echo $template;
        exit;
    }
}
