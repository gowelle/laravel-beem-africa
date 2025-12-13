<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Contacts\BeemContactsService;
use Gowelle\BeemAfrica\DTOs\AddressBookRequest;
use Gowelle\BeemAfrica\DTOs\ContactRequest;
use Gowelle\BeemAfrica\Exceptions\ContactsException;
use Gowelle\BeemAfrica\Support\BeemContactsClient;
use Illuminate\Http\Client\Response;

beforeEach(function () {
    $this->client = Mockery::mock(BeemContactsClient::class);
    $this->service = new BeemContactsService($this->client);
});

it('lists address books successfully', function () {
    $mockResponse = [
        'data' => [
            [
                'id' => '123',
                'addressbook' => 'Default',
                'contacts_count' => 30,
                'description' => '',
                'created' => '2021-03-10T19:10:47.708Z',
            ],
        ],
        'pagination' => [
            'totalItems' => 1,
            'currentPage' => 1,
            'pageSize' => 10,
            'totalPages' => 1,
            'startPage' => 1,
            'endPage' => 1,
            'startIndex' => 0,
            'endIndex' => 0,
            'pages' => [1],
        ],
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($mockResponse);

    $this->client->shouldReceive('get')->with('/address-books', [])->andReturn($response);

    $result = $this->service->listAddressBooks();

    expect($result->count())->toBe(1);
});

it('lists address books with search query', function () {
    $mockResponse = [
        'data' => [],
        'pagination' => [
            'totalItems' => 0,
            'currentPage' => 1,
            'pageSize' => 10,
            'totalPages' => 1,
            'startPage' => 1,
            'endPage' => 1,
            'startIndex' => 0,
            'endIndex' => 0,
            'pages' => [1],
        ],
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($mockResponse);

    $this->client->shouldReceive('get')
        ->with('/address-books', ['q' => 'Test'])
        ->andReturn($response);

    $result = $this->service->listAddressBooks('Test');

    expect($result->count())->toBe(0);
});

it('creates address book successfully', function () {
    $mockResponse = [
        'data' => [
            'id' => '60914e137f86e925b970df04',
            'message' => 'AddressBook Added Successfully',
            'status' => 'true',
        ],
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($mockResponse);

    $this->client->shouldReceive('post')
        ->with('/address-books', ['addressbook' => 'Test'])
        ->andReturn($response);

    $request = new AddressBookRequest('Test');
    $result = $this->service->createAddressBook($request);

    expect($result->isSuccessful())->toBeTrue()
        ->and($result->getId())->toBe('60914e137f86e925b970df04');
});

it('throws exception when create address book fails', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(false);
    $response->shouldReceive('json')->andReturn(['message' => 'API Error']);
    $response->shouldReceive('status')->andReturn(400);

    $this->client->shouldReceive('post')->andReturn($response);

    $request = new AddressBookRequest('Test');
    $this->service->createAddressBook($request);
})->throws(ContactsException::class);

it('lists contacts successfully', function () {
    $mockResponse = [
        'data' => [
            [
                'id' => '604919718fb0191944537663',
                'mob_no' => '255784021800',
                'mob_no2' => '',
                'title' => 'Mr.',
                'fname' => 'Test',
                'lname' => 'Test',
                'gender' => '',
                'birth_date' => '1970-01-01T00:00:00.000Z',
                'area' => '',
                'city' => '',
                'country' => '',
                'email' => 'test@gmail.com',
                'created' => '2021-03-10T19:09:39.884Z',
            ],
        ],
        'pagination' => [
            'totalItems' => 1,
            'currentPage' => 1,
            'pageSize' => 25,
            'totalPages' => 1,
            'startPage' => 1,
            'endPage' => 1,
            'startIndex' => 0,
            'endIndex' => 0,
            'pages' => [1],
        ],
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($mockResponse);

    $this->client->shouldReceive('get')
        ->with('/contacts', ['addressbook_id' => 'addressbook-123'])
        ->andReturn($response);

    $result = $this->service->listContacts('addressbook-123');

    expect($result->count())->toBe(1);
});

it('creates contact successfully', function () {
    $mockResponse = [
        'data' => [
            'id' => '60e99589eeadc6338b16cb42',
            'message' => 'Contact added successfully',
            'status' => 'true',
        ],
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($mockResponse);

    $this->client->shouldReceive('post')->andReturn($response);

    $request = new ContactRequest(
        mob_no: '255712345678',
        addressbook_id: ['addressbook-123'],
        fname: 'John',
    );
    $result = $this->service->createContact($request);

    expect($result->isSuccessful())->toBeTrue();
});

it('updates contact successfully', function () {
    $mockResponse = [
        'data' => [
            'id' => '6093ee8b46e0380018962d2a',
            'message' => 'Contact Updated Successfully',
            'status' => 'true',
        ],
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($mockResponse);

    $this->client->shouldReceive('put')->andReturn($response);

    $request = new ContactRequest(
        mob_no: '255712345678',
        addressbook_id: ['addressbook-123'],
    );
    $result = $this->service->updateContact('contact-123', $request);

    expect($result->isSuccessful())->toBeTrue();
});

it('deletes contacts successfully', function () {
    $mockResponse = [
        'data' => [
            'message' => 'Contacts Deleted Successfully',
            'status' => 'true',
        ],
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($mockResponse);

    $this->client->shouldReceive('delete')->andReturn($response);

    $result = $this->service->deleteContacts(['ab-123'], ['contact-123']);

    expect($result->isSuccessful())->toBeTrue();
});

it('throws exception when delete contact without addressbook ids', function () {
    $this->service->deleteContacts([], ['contact-123']);
})->throws(InvalidArgumentException::class, 'At least one AddressBook ID is required');

it('throws exception when delete contact without contact ids', function () {
    $this->service->deleteContacts(['ab-123'], []);
})->throws(InvalidArgumentException::class, 'At least one Contact ID is required');
