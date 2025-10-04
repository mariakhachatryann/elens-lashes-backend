<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactService
{
    public function getAllContacts(int $perPage = 15): LengthAwarePaginator
    {
        return Contact::paginate($perPage);
    }

    public function getAllContactsForAdmin(): LengthAwarePaginator
    {
        return Contact::paginate(15);
    }

    public function getPrimaryContact(): ?Contact
    {
        return Contact::first();
    }

    public function createContact(array $data): Contact
    {
        if (isset($data['social_links'])) {
            $data['social_links'] = array_filter($data['social_links']);
        }

        return Contact::create($data);
    }

    public function updateContact(int $id, array $data): ?Contact
    {
        $contact = Contact::find($id);
        
        if (!$contact) {
            return null;
        }

        if (isset($data['social_links'])) {
            $data['social_links'] = array_filter($data['social_links']);
        }

        $contact->update($data);
        return $contact->fresh();
    }

    public function deleteContact(int $id): bool
    {
        $contact = Contact::find($id);
        
        if (!$contact) {
            return false;
        }

        return $contact->delete();
    }
}