<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Get hash for an invoice
 */
function get_angola_invoice_hash($invoice_id)
{
    $CI = &get_instance();
    $CI->db->where('rel_id', $invoice_id);
    $CI->db->where('rel_type', 'invoice');
    return $CI->db->get(db_prefix() . 'saft_ao_hashes')->row();
}

/**
 * Get previous invoice hash in the chain
 */
function get_last_angola_invoice_hash()
{
    $CI = &get_instance();
    $CI->db->where('rel_type', 'invoice');
    $CI->db->order_by('id', 'DESC');
    $CI->db->limit(1);
    $row = $CI->db->get(db_prefix() . 'saft_ao_hashes')->row();
    return $row ? $row->hash : '';
}

/**
 * Format invoice number for SAF-T AO
 * Usually: Type Year/Number (e.g. FT 2026/1)
 */
function format_angola_saft_number($invoice)
{
    return 'FT ' . date('Y', strtotime($invoice->date)) . '/' . $invoice->number;
}
