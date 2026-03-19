<?php

namespace AngolaSaft;

defined('BASEPATH') or exit('No direct script access allowed');

class AgtApiClient
{
    private string $endpoint;
    private string $token;

    public function __construct(string $endpoint = '', string $token = '')
    {
        $this->endpoint = $endpoint ?: get_option('angola_saft_api_endpoint');
        $this->token    = $token ?: get_option('angola_saft_api_token');
    }

    /**
     * Submit an invoice or credit note to the AGT API
     * 
     * @param array $data The JSON-serializable data of the document
     * @return array [success, response, error]
     */
    public function submitDocument(array $data): array
    {
        if (empty($this->endpoint) || empty($this->token)) {
            return [
                'success' => false,
                'error'   => 'AGT API credentials not configured.'
            ];
        }

        $url = rtrim($this->endpoint, '/') . '/invoices';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token,
            'X-Company-VAT: ' . get_option('company_vat')
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error'   => 'CURL Error: ' . $error
            ];
        }

        $decoded = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success'  => true,
                'response' => $decoded
            ];
        }

        return [
            'success' => false,
            'error'   => 'AGT API Error (HTTP ' . $httpCode . '): ' . ($decoded['message'] ?? $response)
        ];
    }
}
