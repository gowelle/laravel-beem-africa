<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\DTOs\ContactRequest;
use Gowelle\BeemAfrica\Enums\Gender;
use Gowelle\BeemAfrica\Enums\Title;

it('Gender enum has correct values', function () {
    expect(Gender::MALE->value)->toBe('male')
        ->and(Gender::FEMALE->value)->toBe('female');
});

it('Gender enum has correct labels', function () {
    expect(Gender::MALE->label())->toBe('Male')
        ->and(Gender::FEMALE->label())->toBe('Female');
});

it('Gender enum has helper methods', function () {
    expect(Gender::MALE->isMale())->toBeTrue()
        ->and(Gender::MALE->isFemale())->toBeFalse()
        ->and(Gender::FEMALE->isMale())->toBeFalse()
        ->and(Gender::FEMALE->isFemale())->toBeTrue();
});

it('Title enum has correct values', function () {
    expect(Title::MR->value)->toBe('Mr.')
        ->and(Title::MRS->value)->toBe('Mrs.')
        ->and(Title::MS->value)->toBe('Ms.');
});

it('Title enum has helper methods', function () {
    expect(Title::MR->isMr())->toBeTrue()
        ->and(Title::MR->isMrs())->toBeFalse()
        ->and(Title::MR->isMs())->toBeFalse()
        ->and(Title::MRS->isMr())->toBeFalse()
        ->and(Title::MRS->isMrs())->toBeTrue()
        ->and(Title::MS->isMs())->toBeTrue();
});

it('creates contact request with Gender enum', function () {
    $request = new ContactRequest(
        mob_no: '255712345678',
        addressbook_id: ['123'],
        gender: Gender::MALE,
    );

    expect($request->gender)->toBe(Gender::MALE);

    $array = $request->toArray();
    expect($array['gender'])->toBe('male');
});

it('creates contact request with Title enum', function () {
    $request = new ContactRequest(
        mob_no: '255712345678',
        addressbook_id: ['123'],
        title: Title::MR,
    );

    expect($request->title)->toBe(Title::MR);

    $array = $request->toArray();
    expect($array['title'])->toBe('Mr.');
});

it('creates contact request with both enums', function () {
    $request = new ContactRequest(
        mob_no: '255712345678',
        addressbook_id: ['123'],
        fname: 'John',
        lname: 'Doe',
        title: Title::MR,
        gender: Gender::MALE,
    );

    expect($request->title)->toBe(Title::MR)
        ->and($request->gender)->toBe(Gender::MALE);

    $array = $request->toArray();
    expect($array['title'])->toBe('Mr.')
        ->and($array['gender'])->toBe('male');
});

it('creates contact request with string gender (backward compatibility)', function () {
    $request = new ContactRequest(
        mob_no: '255712345678',
        addressbook_id: ['123'],
        gender: 'female',
    );

    expect($request->gender)->toBe('female');

    $array = $request->toArray();
    expect($array['gender'])->toBe('female');
});

it('creates contact request with string title (backward compatibility)', function () {
    $request = new ContactRequest(
        mob_no: '255712345678',
        addressbook_id: ['123'],
        title: 'Mrs.',
    );

    expect($request->title)->toBe('Mrs.');

    $array = $request->toArray();
    expect($array['title'])->toBe('Mrs.');
});
