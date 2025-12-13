<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\DTOs\Contact;
use Gowelle\BeemAfrica\DTOs\ContactDeleteResponse;
use Gowelle\BeemAfrica\DTOs\ContactListResponse;
use Gowelle\BeemAfrica\DTOs\ContactRequest;
use Gowelle\BeemAfrica\DTOs\ContactResponse;

describe('ContactRequest', function () {
    it('creates contact request with required fields', function () {
        $request = new ContactRequest(
            mob_no: '255712345678',
            addressbook_id: ['123'],
        );

        expect($request->mob_no)->toBe('255712345678')
            ->and($request->addressbook_id)->toBe(['123']);
    });

    it('creates contact request with all optional fields', function () {
        $request = new ContactRequest(
            mob_no: '255712345678',
            addressbook_id: ['123'],
            fname: 'John',
            lname: 'Doe',
            title: 'Mr.',
            gender: 'male',
            mob_no2: '255787654321',
            email: 'john@example.com',
            country: 'Tanzania',
            city: 'Dar es Salaam',
            area: 'Kisutu',
            birth_date: '1990-01-15'
        );

        expect($request->fname)->toBe('John')
            ->and($request->lname)->toBe('Doe')
            ->and($request->title)->toBe('Mr.')
            ->and($request->gender)->toBe('male')
            ->and($request->email)->toBe('john@example.com');
    });

    it('throws exception when mobile number is invalid', function () {
        new ContactRequest(
            mob_no: 'invalid',
            addressbook_id: ['123'],
        );
    })->throws(InvalidArgumentException::class, 'Invalid phone number format');

    it('throws exception when mobile number is empty', function () {
        new ContactRequest(
            mob_no: '',
            addressbook_id: ['123'],
        );
    })->throws(InvalidArgumentException::class, 'Mobile number (mob_no) is required');

    it('throws exception when addressbook_id is empty', function () {
        new ContactRequest(
            mob_no: '255712345678',
            addressbook_id: [],
        );
    })->throws(InvalidArgumentException::class, 'At least one addressbook ID is required');

    it('throws exception when birth date format is invalid', function () {
        new ContactRequest(
            mob_no: '255712345678',
            addressbook_id: ['123'],
            birth_date: '01-01-1990'
        );
    })->throws(InvalidArgumentException::class, 'Birth date must be in yyyy-mm-dd format');

    it('throws exception when gender is invalid', function () {
        new ContactRequest(
            mob_no: '255712345678',
            addressbook_id: ['123'],
            gender: 'other'
        );
    })->throws(InvalidArgumentException::class, 'Gender must be either "male" or "female"');

    it('converts contact request to array', function () {
        $request = new ContactRequest(
            mob_no: '255712345678',
            addressbook_id: ['123'],
            fname: 'John',
        );
        $array = $request->toArray();

        expect($array)->toHaveKey('mob_no')
            ->toHaveKey('addressbook_id')
            ->toHaveKey('fname');
    });

    it('creates contact from api response', function () {
        $data = [
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
        ];

        $contact = Contact::fromArray($data);

        expect($contact->id)->toBe('604919718fb0191944537663')
            ->and($contact->mob_no)->toBe('255784021800')
            ->and($contact->getFullName())->toBe('Test Test')
            ->and($contact->getEmail())->toBe('test@gmail.com');
    });

    it('creates contact response from api response', function () {
        $data = [
            'data' => [
                'id' => '60e99589eeadc6338b16cb42',
                'message' => 'Contact added successfully',
                'status' => 'true',
            ],
        ];

        $response = ContactResponse::fromArray($data);

        expect($response->id)->toBe('60e99589eeadc6338b16cb42')
            ->and($response->message)->toBe('Contact added successfully')
            ->and($response->isSuccessful())->toBeTrue();
    });

    it('creates contact list response from api response', function () {
        $data = [
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

        $response = ContactListResponse::fromArray($data);

        expect($response->count())->toBe(1)
            ->and($response->getContacts())->toHaveCount(1);
    });

    it('creates contact delete response from api response', function () {
        $data = [
            'data' => [
                'message' => 'Contacts Deleted Successfully',
                'status' => 'true',
            ],
        ];

        $response = ContactDeleteResponse::fromArray($data);

        expect($response->message)->toBe('Contacts Deleted Successfully')
            ->and($response->isSuccessful())->toBeTrue();
    });
});
