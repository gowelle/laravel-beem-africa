<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Exceptions;

/**
 * Exception thrown when Contacts operations fail.
 */
class ContactsException extends BeemException
{
    /**
     * Create exception from API response.
     */
    public static function fromApiResponse(array $response, int $httpStatusCode = 0): self
    {
        $message = $response['message'] ?? 'Contacts operation failed';

        return new self($message, $httpStatusCode);
    }

    /**
     * Create exception for invalid response.
     */
    public static function invalidResponse(string $message = ''): self
    {
        return new self(
            "Invalid Contacts response: {$message}"
        );
    }

    /**
     * Create exception when AddressBook not found.
     */
    public static function addressBookNotFound(string $addressBookId): self
    {
        return new self(
            "AddressBook with ID '{$addressBookId}' not found"
        );
    }

    /**
     * Create exception when trying to delete default AddressBook.
     */
    public static function cannotDeleteDefaultAddressBook(): self
    {
        return new self(
            'Cannot delete the default AddressBook'
        );
    }

    /**
     * Create exception when contact not found.
     */
    public static function contactNotFound(string $contactId): self
    {
        return new self(
            "Contact with ID '{$contactId}' not found"
        );
    }
}
