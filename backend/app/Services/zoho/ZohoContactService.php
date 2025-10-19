<?php

namespace App\Services\Zoho;

use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class ZohoContactService
{
    protected ZohoApiClient $client;

    public function __construct(ZohoApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch all contacts from Zoho Books.
     */
    public function fetchContacts(): array
    {
        $data = $this->client->get('contacts');

        if (!isset($data['contacts'])) {
            Log::error('Failed to fetch Zoho contacts', ['response' => $data]);
            throw new Exception('Failed to fetch contacts', 500);
        }

        // Log::info('Fetched Zoho contacts successfully.', ['count' => count($data['contacts'])]);
        return $data['contacts'];
    }

    /**
     * Save or update Zoho contacts in local database.
     */
    public function saveContacts(array $contacts): void
    {
        foreach ($contacts as $contactData) {
            Contact::updateOrCreate(
                ['contact_id' => $contactData['contact_id']],
                [
                    'contact_name'            => $contactData['contact_name'] ?? null,
                    'customer_name'           => $contactData['customer_name'] ?? null,
                    'vendor_name'             => $contactData['vendor_name'] ?? null,
                    'company_name'            => $contactData['company_name'] ?? null,
                    'website'                 => $contactData['website'] ?? null,
                    'language_code'           => $contactData['language_code'] ?? null,
                    'language_code_formatted' => $contactData['language_code_formatted'] ?? null,
                    'contact_type'            => $contactData['contact_type'] ?? null,
                    'contact_type_formatted'  => $contactData['contact_type_formatted'] ?? null,
                    'status'                  => $contactData['status'] ?? null,
                    'customer_sub_type'       => $contactData['customer_sub_type'] ?? null,
                    'source'                  => $contactData['source'] ?? null,

                    'is_linked_with_zohocrm'  => (bool)($contactData['is_linked_with_zohocrm'] ?? false),
                    'payment_terms'           => $contactData['payment_terms'] ?? 0,
                    'payment_terms_label'     => $contactData['payment_terms_label'] ?? null,
                    'currency_id'             => $contactData['currency_id'] ?? null,
                    'currency_code'           => $contactData['currency_code'] ?? null,

                    'outstanding_receivable_amount'        => $contactData['outstanding_receivable_amount'] ?? 0,
                    'outstanding_receivable_amount_bcy'    => $contactData['outstanding_receivable_amount_bcy'] ?? 0,
                    'outstanding_payable_amount'           => $contactData['outstanding_payable_amount'] ?? 0,
                    'outstanding_payable_amount_bcy'       => $contactData['outstanding_payable_amount_bcy'] ?? 0,
                    'unused_credits_receivable_amount'     => $contactData['unused_credits_receivable_amount'] ?? 0,
                    'unused_credits_receivable_amount_bcy' => $contactData['unused_credits_receivable_amount_bcy'] ?? 0,
                    'unused_credits_payable_amount'        => $contactData['unused_credits_payable_amount'] ?? 0,
                    'unused_credits_payable_amount_bcy'    => $contactData['unused_credits_payable_amount_bcy'] ?? 0,

                    'first_name'              => $contactData['first_name'] ?? null,
                    'last_name'               => $contactData['last_name'] ?? null,
                    'email'                   => $contactData['email'] ?? null,
                    'phone'                   => $contactData['phone'] ?? null,
                    'mobile'                  => $contactData['mobile'] ?? null,
                    'portal_status'           => $contactData['portal_status'] ?? null,
                    'portal_status_formatted' => $contactData['portal_status_formatted'] ?? null,

                    'created_time' => isset($contactData['created_time'])
                        ? Carbon::parse($contactData['created_time'])
                        : null,
                    'last_modified_time' => isset($contactData['last_modified_time'])
                        ? Carbon::parse($contactData['last_modified_time'])
                        : null,

                    'custom_fields'     => $contactData['custom_fields'] ?? [],
                    'custom_field_hash' => $contactData['custom_field_hash'] ?? [],
                    'tags'              => $contactData['tags'] ?? [],
                    'ach_supported'     => (bool)($contactData['ach_supported'] ?? false),
                    'has_attachment'    => (bool)($contactData['has_attachment'] ?? false),
                ]
            );
        }

        // Log::info('Contacts saved successfully.', ['count' => count($contacts)]);
    }

    /**
     * Sync Contacts end-to-end.
     */
    public function sync(): array
    {
        $contacts = $this->fetchContacts();
        $this->saveContacts($contacts);

        return [
            'message' => 'Contacts synced successfully.',
            'count'   => count($contacts),
        ];
    }
}
