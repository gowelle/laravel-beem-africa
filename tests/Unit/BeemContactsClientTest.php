<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Support\BeemContactsClient;
use Illuminate\Support\Facades\Http;

it('creates http request with authentication', function () {
    $client = new BeemContactsClient(
        apiKey: 'test-api-key',
        secretKey: 'test-secret-key',
    );

    expect($client->getBaseUrl())->toBe('https://apicontacts.beem.africa/public/v1');
});

it('creates http request with custom base url', function () {
    $client = new BeemContactsClient(
        apiKey: 'test-api-key',
        secretKey: 'test-secret-key',
        baseUrl: 'https://custom.url/v1',
    );

    expect($client->getBaseUrl())->toBe('https://custom.url/v1');
});

it('makes get request', function () {
    Http::fake([
        'https://apicontacts.beem.africa/public/v1/address-books' => Http::response(['data' => []]),
    ]);

    $client = new BeemContactsClient('key', 'secret');
    $response = $client->get('/address-books');

    expect($response->successful())->toBeTrue();
});

it('makes post request', function () {
    Http::fake([
        'https://apicontacts.beem.africa/public/v1/address-books' => Http::response(['data' => ['id' => '123']]),
    ]);

    $client = new BeemContactsClient('key', 'secret');
    $response = $client->post('/address-books', ['addressbook' => 'Test']);

    expect($response->successful())->toBeTrue();
});

it('makes put request', function () {
    Http::fake([
        'https://apicontacts.beem.africa/public/v1/address-books/123' => Http::response(['data' => ['id' => '123']]),
    ]);

    $client = new BeemContactsClient('key', 'secret');
    $response = $client->put('/address-books/123', ['addressbook' => 'Updated']);

    expect($response->successful())->toBeTrue();
});

it('makes delete request', function () {
    Http::fake([
        'https://apicontacts.beem.africa/public/v1/address-books/123' => Http::response(['data' => ['status' => true]]),
    ]);

    $client = new BeemContactsClient('key', 'secret');
    $response = $client->delete('/address-books/123');

    expect($response->successful())->toBeTrue();
});
