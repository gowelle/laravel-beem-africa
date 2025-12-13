<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Contacts;

use Gowelle\BeemAfrica\DTOs\AddressBook;
use Gowelle\BeemAfrica\DTOs\AddressBookDeleteResponse;
use Gowelle\BeemAfrica\DTOs\AddressBookListResponse;
use Gowelle\BeemAfrica\DTOs\AddressBookRequest;
use Gowelle\BeemAfrica\DTOs\AddressBookResponse;
use Gowelle\BeemAfrica\DTOs\Contact;
use Gowelle\BeemAfrica\DTOs\ContactDeleteResponse;
use Gowelle\BeemAfrica\DTOs\ContactListResponse;
use Gowelle\BeemAfrica\DTOs\ContactRequest;
use Gowelle\BeemAfrica\DTOs\ContactResponse;
use Gowelle\BeemAfrica\Exceptions\ContactsException;
use Gowelle\BeemAfrica\Support\BeemContactsClient;

/**
 * Service for handling Beem Africa Contacts operations.
 */
class BeemContactsService
{
    public function __construct(
        protected BeemContactsClient $client,
    ) {}

    /**
     * List all AddressBooks.
     *
     * @throws ContactsException
     */
    public function listAddressBooks(?string $query = null): AddressBookListResponse
    {
        $params = [];

        if ($query !== null) {
            $params['q'] = $query;
        }

        $response = $this->client->get('/address-books', $params);

        if (! $response->successful()) {
            throw ContactsException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw ContactsException::invalidResponse('Empty response from API');
        }

        return AddressBookListResponse::fromArray($data);
    }

    /**
     * Create an AddressBook.
     *
     * @throws ContactsException
     */
    public function createAddressBook(AddressBookRequest $request): AddressBookResponse
    {
        $response = $this->client->post('/address-books', $request->toArray());

        if (! $response->successful()) {
            throw ContactsException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw ContactsException::invalidResponse('Empty response from API');
        }

        return AddressBookResponse::fromArray($data);
    }

    /**
     * Update an AddressBook.
     *
     * @throws ContactsException
     */
    public function updateAddressBook(string $addressBookId, AddressBookRequest $request): AddressBookResponse
    {
        if (empty($addressBookId)) {
            throw new \InvalidArgumentException('AddressBook ID is required');
        }

        $response = $this->client->put("/address-books/{$addressBookId}", $request->toArray());

        if (! $response->successful()) {
            throw ContactsException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw ContactsException::invalidResponse('Empty response from API');
        }

        return AddressBookResponse::fromArray($data);
    }

    /**
     * Delete an AddressBook.
     *
     * @throws ContactsException
     */
    public function deleteAddressBook(string $addressBookId): AddressBookDeleteResponse
    {
        if (empty($addressBookId)) {
            throw new \InvalidArgumentException('AddressBook ID is required');
        }

        $response = $this->client->delete("/address-books/{$addressBookId}");

        if (! $response->successful()) {
            throw ContactsException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw ContactsException::invalidResponse('Empty response from API');
        }

        return AddressBookDeleteResponse::fromArray($data);
    }

    /**
     * List contacts in an AddressBook.
     *
     * @throws ContactsException
     */
    public function listContacts(string $addressBookId, ?string $query = null): ContactListResponse
    {
        if (empty($addressBookId)) {
            throw new \InvalidArgumentException('AddressBook ID is required');
        }

        $params = ['addressbook_id' => $addressBookId];

        if ($query !== null) {
            $params['q'] = $query;
        }

        $response = $this->client->get('/contacts', $params);

        if (! $response->successful()) {
            throw ContactsException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw ContactsException::invalidResponse('Empty response from API');
        }

        return ContactListResponse::fromArray($data);
    }

    /**
     * Create a contact.
     *
     * @throws ContactsException
     */
    public function createContact(ContactRequest $request): ContactResponse
    {
        $response = $this->client->post('/contacts', $request->toArray());

        if (! $response->successful()) {
            throw ContactsException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw ContactsException::invalidResponse('Empty response from API');
        }

        return ContactResponse::fromArray($data);
    }

    /**
     * Update a contact.
     *
     * @throws ContactsException
     */
    public function updateContact(string $contactId, ContactRequest $request): ContactResponse
    {
        if (empty($contactId)) {
            throw new \InvalidArgumentException('Contact ID is required');
        }

        $response = $this->client->put("/contacts/{$contactId}", $request->toArray());

        if (! $response->successful()) {
            throw ContactsException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw ContactsException::invalidResponse('Empty response from API');
        }

        return ContactResponse::fromArray($data);
    }

    /**
     * Delete contacts from AddressBooks.
     *
     * @param  array<string>  $addressBookIds
     * @param  array<string>  $contactIds
     *
     * @throws ContactsException
     */
    public function deleteContacts(array $addressBookIds, array $contactIds): ContactDeleteResponse
    {
        if (empty($addressBookIds)) {
            throw new \InvalidArgumentException('At least one AddressBook ID is required');
        }

        if (empty($contactIds)) {
            throw new \InvalidArgumentException('At least one Contact ID is required');
        }

        $data = [
            'addressbook_id' => $addressBookIds,
            'contacts_id' => $contactIds,
        ];

        $response = $this->client->delete('/contacts', $data);

        if (! $response->successful()) {
            throw ContactsException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $responseData = $response->json();

        if (empty($responseData)) {
            throw ContactsException::invalidResponse('Empty response from API');
        }

        return ContactDeleteResponse::fromArray($responseData);
    }

    /**
     * Get the underlying HTTP client.
     */
    public function getClient(): BeemContactsClient
    {
        return $this->client;
    }
}
