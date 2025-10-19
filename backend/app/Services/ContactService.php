<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactService
{
    /**
     * Fetch paginated list of contacts.
     */
    public function getPaginatedContacts(int $perPage = 20): LengthAwarePaginator
    {
        return Contact::latest('created_at')->paginate($perPage);
    }
}
