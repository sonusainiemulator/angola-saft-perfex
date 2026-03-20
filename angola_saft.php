<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Angola E-Invoice (SAF-T AO)
Description: Compliance module for Angola AGT Mandatory Electronic Invoicing (SAF-T AO 1.01 & Real-time API).
Version: 1.0.0
Requires at least: 3.0.*
*/

define('ANGOLA_SAFT_MODULE_NAME', 'angola_saft');

require_once(__DIR__ . '/src/SaftAoSigner.php');
require_once(__DIR__ . '/src/AgtApiClient.php');

// Register activation hook
register_activation_hook(ANGOLA_SAFT_MODULE_NAME, 'angola_saft_module_activation_hook');

hooks()->add_action('admin_init', 'angola_saft_module_init');

// Add settings link on modules page
hooks()->add_filter('module_angola_saft_action_links', 'angola_saft_module_action_links');
function angola_saft_module_action_links($actions) {
    $actions[] = '<a href="' . admin_url('settings?group=angola_saft') . '">Settings</a>';
    return $actions;
}

// Hooks for digital signing
hooks()->add_action('after_invoice_added', 'angola_saft_sign_invoice_on_creation');
hooks()->add_action('after_credit_note_added', 'angola_saft_sign_credit_note_on_creation');

// Filter to inject data into einvoice module
hooks()->add_filter('einvoice_template_data', 'angola_saft_inject_template_data');



/**
 * Register module settings and menu items
 */
function angola_saft_module_init()
{
    $CI = &get_instance();
    
    // START REBRAND FIX FOR YANA
    if ($CI->db->table_exists(db_prefix() . 'modules')) {
        $module_anna = $CI->db->where('module_name', 'anna')->get(db_prefix() . 'modules')->row();
        if ($module_anna) {
            $CI->db->where('module_name', 'anna');
            $CI->db->update(db_prefix() . 'modules', ['module_name' => 'yana']);
            
            // Rename tables if they still have old names
            $tables = [
                'anna_chat_log', 'anna_conversations', 'anna_conversations_messages',
                'anna_plans', 'anna_subscriptions', 'anna_usage',
                'anna_voice_notifications', 'anna_role_permissions', 'anna_migrations'
            ];
            foreach ($tables as $table) {
                $old_table = db_prefix() . $table;
                $new_table = db_prefix() . str_replace('anna_', 'yana_', $table);
                if ($CI->db->table_exists($old_table) && !$CI->db->table_exists($new_table)) {
                    $CI->db->query("RENAME TABLE `{$old_table}` TO `{$new_table}`");
                }
            }
            // Update options
            $CI->db->query("UPDATE " . db_prefix() . "options SET name = REPLACE(name, 'anna_', 'yana_') WHERE name LIKE 'anna_%'");
            
            log_message('error', 'YANA REBRAND: Successfully migrated via angola_saft hook');
        }
    }
    // END REBRAND FIX FOR YANA
    
    // Load helper
    $CI->load->helper('angola_saft/angola_saft');

    // Add settings section child to sales (core section for guaranteed visibility)
    $CI->app->add_settings_section_child('sales', 'angola_saft', [
        'name'     => 'Angola E-Invoice',
        'view'     => 'angola_saft/settings',
        'position' => 60,
        'icon'     => 'fa-solid fa-file-invoice-dollar',
    ]);




    // Add menu item under utilities
    if (staff_can('export', 'angola_saft')) {
        $CI->app_menu->add_sidebar_children_item('utilities', [
            'slug'     => 'angola_saft_export',
            'name'     => 'Angola SAF-T Export',
            'href'     => admin_url('angola_saft/export'),
            'position' => 12,
        ]);
    }


    // Register permissions
    register_staff_capabilities('angola_saft', [
        'capabilities' => [
            'view'   => 'View SAFT Reports',
            'export' => 'Export SAFT XML',
        ]
    ], 'Angola E-Invoice');
}

/**
 * Module activation hook - Create database tables
 */
function angola_saft_module_activation_hook()
{
    $CI = &get_instance();
    if (!$CI->db->table_exists(db_prefix() . 'saft_ao_hashes')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'saft_ao_hashes` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `rel_id` int(11) NOT NULL,
          `rel_type` varchar(20) NOT NULL,
          `hash` varchar(255) NOT NULL,
          `prev_hash` varchar(255) NOT NULL,
          `signature` text NOT NULL,
          `signing_key_version` varchar(10) DEFAULT "1",
          `created_at` datetime NOT NULL,
          PRIMARY KEY (`id`),
          KEY `rel_id` (`rel_id`),
          KEY `rel_type` (`rel_type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
    }

    add_option('angola_saft_certification_no', '');
    add_option('angola_saft_private_key', '');
    add_option('angola_saft_public_key', '');
    add_option('angola_saft_key_version', '1');
    add_option('angola_saft_api_endpoint', 'https://invoice.minfin.gov.ao/api/v1');
    add_option('angola_saft_api_token', '');
}

/**
 * Perform digital signing for a new invoice
 */
function angola_saft_sign_invoice_on_creation($id)
{
    $CI = &get_instance();
    $CI->load->model('invoices_model');
    $invoice = $CI->invoices_model->get($id);
    
    // Format number for SAFT
    $invoice->number = format_angola_saft_number($invoice);
    
    $prevHash = get_last_angola_invoice_hash();
    
    try {
        $signer = new \AngolaSaft\SaftAoSigner();
        $result = $signer->signInvoice($invoice, $prevHash);
        
        $CI->db->insert(db_prefix() . 'saft_ao_hashes', [
            'rel_id'              => $id,
            'rel_type'            => 'invoice',
            'hash'                => $result['hash'],
            'prev_hash'           => $prevHash,
            'signature'           => $result['signature'],
            'signing_key_version' => $result['control'],
            'created_at'          => date('Y-m-d H:i:s'),
        ]);
        
        // Log activity
        log_activity('Invoice #' . $id . ' signed for Angola SAF-T AO (Hash: ' . substr($result['hash'], 0, 10) . '...)');
        
        // Real-time API Submission
        $apiClient = new \AngolaSaft\AgtApiClient();
        
        // Prepare data for JSON submission (leveraging einvoice data if available)
        $einvoiceData = new \Perfexcrm\EInvoice\Data\Invoice($invoice);
        $jsonData = $einvoiceData->jsonSerialize();
        $jsonData = angola_saft_inject_template_data($jsonData);
        
        $apiResult = $apiClient->submitDocument($jsonData);

        
        if ($apiResult['success']) {
            log_activity('Invoice #' . $id . ' submitted successfully to AGT Portal.');
        } else {
            log_activity('FAILED TO SUBMIT INVOICE #' . $id . ' TO AGT: ' . $apiResult['error']);
        }
        
    } catch (\Exception $e) {

        log_activity('ERROR SIGNING INVOICE FOR SAF-T AO: ' . $e->getMessage());
    }
}

/**
 * Perform digital signing for a new credit note
 */
function angola_saft_sign_credit_note_on_creation($id)
{
    $CI = &get_instance();
    $CI->load->model('credit_notes_model');
    $credit_note = $CI->credit_notes_model->get($id);
    
    // Format number for SAFT (NC for Credit Note)
    $credit_note->number = 'NC ' . date('Y', strtotime($credit_note->date)) . '/' . $credit_note->number;
    
    $prevHash = get_last_angola_invoice_hash(); // Chain with invoices/credit notes
    
    try {
        $signer = new \AngolaSaft\SaftAoSigner();
        $result = $signer->signInvoice($credit_note, $prevHash);
        
        $CI->db->insert(db_prefix() . 'saft_ao_hashes', [
            'rel_id'              => $id,
            'rel_type'            => 'credit_note',
            'hash'                => $result['hash'],
            'prev_hash'           => $prevHash,
            'signature'           => $result['signature'],
            'signing_key_version' => $result['control'],
            'created_at'          => date('Y-m-d H:i:s'),
        ]);
        
        // Log activity
        log_activity('Credit Note #' . $id . ' signed for Angola SAF-T AO (Hash: ' . substr($result['hash'], 0, 10) . '...)');
        
        // Real-time API Submission
        $apiClient = new \AngolaSaft\AgtApiClient();
        
        // Prepare data for JSON submission
        $einvoiceData = new \Perfexcrm\EInvoice\Data\CreditNote($credit_note);
        $jsonData = $einvoiceData->jsonSerialize();
        $jsonData = angola_saft_inject_template_data($jsonData);
        
        $apiResult = $apiClient->submitDocument($jsonData);
        
        if ($apiResult['success']) {
            log_activity('Credit Note #' . $id . ' submitted successfully to AGT Portal.');
        } else {
            log_activity('FAILED TO SUBMIT CREDIT NOTE #' . $id . ' TO AGT: ' . $apiResult['error']);
        }
        
    } catch (\Exception $e) {
        log_activity('ERROR SIGNING CREDIT NOTE: ' . $e->getMessage());
    }
}


/**
 * Inject data into the e-invoice template
 */
function angola_saft_inject_template_data($data)
{
    $CI = &get_instance();
    
    // If it's an array (from Invoice/CreditNote class)
    if (is_array($data)) {
        $rel_id = $data['INVOICE_ID'] ?? null;
        $rel_type = isset($data['INVOICE_ID']) ? 'invoice' : 'credit_note';
        
        if ($rel_id) {
            $hash_data = get_angola_invoice_hash($rel_id);
            if ($hash_data) {
                $data['HASH'] = $hash_data->hash;
                $data['HASH_4'] = substr($hash_data->hash, 0, 4);
                $data['HASH_CONTROL'] = $hash_data->signing_key_version;
            }
        }
        
        $data['CERTIFICATE_NO'] = get_option('angola_saft_certification_no');
        $data['SOFTWARE_NAME'] = 'Perfex CRM - Angola Edition';
    }
    
    return $data;
}


