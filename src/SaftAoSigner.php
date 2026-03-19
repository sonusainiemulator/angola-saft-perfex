<?php

namespace AngolaSaft;

defined('BASEPATH') or exit('No direct script access allowed');

class SaftAoSigner
{
    private string $privateKey;
    private string $keyVersion;

    public function __construct(string $privateKey = '', string $keyVersion = '1')
    {
        $this->privateKey = $privateKey ?: get_option('angola_saft_private_key');
        $this->keyVersion = $keyVersion ?: get_option('angola_saft_key_version');
    }

    /**
     * sign an invoice and return the hash and signature
     * 
     * Concatenation format: InvoiceDate;SystemEntryDate;InvoiceNo;GrossTotal;PrevHash
     * Algorithm: RSA-SHA1 (standard for SAF-T AO 1.01)
     * 
     * @param object $invoice The invoice data (date, datecreated, number, total)
     * @param string $prevHash The hash of the previous invoice
     * @return array [hash, signature]
     */
    public function signInvoice(object $invoice, string $prevHash = ''): array
    {
        $concatenation = $this->getInvoiceConcatenation($invoice, $prevHash);
        
        // Use PHP openssl to sign
        $binarySignature = '';
        $binaryKey = openssl_get_privatekey($this->privateKey);
        
        if (!$binaryKey) {
            throw new \Exception('Invalid private key for SAF-T AO signing.');
        }

        // Sign using SHA1 (historically used in SAF-T AO/PT)
        openssl_sign($concatenation, $binarySignature, $binaryKey, OPENSSL_ALGO_SHA1);
        
        $signature = base64_encode($binarySignature);
        
        // The "hash" in SAF-T terms is often the signature truncated or just the signature itself 
        // depending on the version. In SAF-T PT/AO, the <Hash> tag contains the digital signature.
        // There is also usually a "HashControl" tag which is the key version.
        
        return [
            'hash'      => $signature, // In SAF-T AO, the signature is the hash
            'signature' => $signature,
            'control'   => $this->keyVersion
        ];
    }

    /**
     * Format the concatenation string for signing
     */
    public function getInvoiceConcatenation(object $invoice, string $prevHash): string
    {
        // InvoiceDate;SystemEntryDate;InvoiceNo;GrossTotal;PrevHash
        // Date format: YYYY-MM-DD
        // DateTime format: YYYY-MM-DDTHH:MM:SS
        // Total format: 0.00
        
        $invoiceDate = date('Y-m-d', strtotime($invoice->date));
        $systemEntryDate = date('Y-m-d\TH:i:s', strtotime($invoice->datecreated));
        $invoiceNo = $invoice->number; // e.g. AG FT/2026/1
        $grossTotal = number_format($invoice->total, 2, '.', '');
        
        return sprintf(
            '%s;%s;%s;%s;%s',
            $invoiceDate,
            $systemEntryDate,
            $invoiceNo,
            $grossTotal,
            $prevHash
        );
    }
}
