<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\DTOs\AddressBook;
use Gowelle\BeemAfrica\DTOs\AddressBookDeleteResponse;
use Gowelle\BeemAfrica\DTOs\AddressBookListResponse;
use Gowelle\BeemAfrica\DTOs\AddressBookRequest;
use Gowelle\BeemAfrica\DTOs\AddressBookResponse;

it('creates address book request with required fields', function () {
    $request = new AddressBookRequest('Test Group');

    expect($request->addressbook)->toBe('Test Group')
        ->and($request->description)->toBeNull();
});

it('creates address book request with optional description', function () {
    $request = new AddressBookRequest('Test Group', 'Test Description');

    expect($request->addressbook)->toBe('Test Group')
        ->and($request->description)->toBe('Test Description');
});

it('converts address book request to array', function () {
    $request = new AddressBookRequest('Test Group', 'Description');
    $array = $request->toArray();

    expect($array)->toBe([
        'addressbook' => 'Test Group',
        'description' => 'Description',
    ]);
});

it('throws exception when address book name is empty', function () {
    new AddressBookRequest('');
})->throws(InvalidArgumentException::class, 'AddressBook name is required');

it('creates address book from api response', function () {
    $data = [
        'id' => '123',
        'addressbook' => 'Default',
        'contacts_count' => 30,
        'description' => '',
        'created' => '2021-03-10T19:10:47.708Z',
    ];

    $addressBook = AddressBook::fromArray($data);

    expect($addressBook->id)->toBe('123')
        ->and($addressBook->addressbook)->toBe('Default')
        ->and($addressBook->contacts_count)->toBe(30)
        ->and($addressBook->description)->toBe('')
        ->and($addressBook->created)->toBe('2021-03-10T19:10:47.708Z');
});

it('creates address book response from api response', function () {
    $data = [
        'data' => [
            'id' => '60914e137f86e925b970df04',
            'message' => 'AddressBook Added Successfully',
            'status' => 'true',
        ],
    ];

    $response = AddressBookResponse::fromArray($data);

    expect($response->id)->toBe('60914e137f86e925b970df04')
        ->and($response->message)->toBe('AddressBook Added Successfully')
        ->and($response->isSuccessful())->toBeTrue();
});

it('creates address book list response from api response', function () {
    $data = [
        'data' => [
            [
                'addressbook' => 'Default',
                'contacts_count' => 30,
                'description' => '',
                'created' => '2021-03-10T19:10:47.708Z',
                'id' => '604919718fb019194453764e',
            ],
            [
                'description' => 'Test Group',
                'addressbook' => 'Trial',
                'contacts_count' => 0,
                'created' => '2021-07-08T14:28:54.155Z',
                'id' => '60e70ba6ffe24b0019011782',
            ],
        ],
        'pagination' => [
            'totalItems' => 2,
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

    $response = AddressBookListResponse::fromArray($data);

    expect($response->count())->toBe(2)
        ->and($response->getAddressBooks())->toHaveCount(2)
        ->and($response->getPagination()->getTotalItems())->toBe(2)
        ->and($response->getPagination()->hasMorePages())->toBeFalse();
});

it('creates address book delete response from api response', function () {
    $data = [
        'data' => [
            'message' => 'AddressBook Deleted successfully',
            'status' => 'true',
        ],
    ];

    $response = AddressBookDeleteResponse::fromArray($data);

    expect($response->message)->toBe('AddressBook Deleted successfully')
        ->and($response->isSuccessful())->toBeTrue();
});
