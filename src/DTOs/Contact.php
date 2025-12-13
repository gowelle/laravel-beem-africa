<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for Contact.
 */
class Contact
{
    public function __construct(
        public readonly string $id,
        public readonly string $mob_no,
        public readonly string $mob_no2,
        public readonly string $title,
        public readonly string $fname,
        public readonly string $lname,
        public readonly string $gender,
        public readonly string $birth_date,
        public readonly string $area,
        public readonly string $city,
        public readonly string $country,
        public readonly string $email,
        public readonly string $created,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            mob_no: (string) ($data['mob_no'] ?? ''),
            mob_no2: (string) ($data['mob_no2'] ?? ''),
            title: (string) ($data['title'] ?? ''),
            fname: (string) ($data['fname'] ?? ''),
            lname: (string) ($data['lname'] ?? ''),
            gender: (string) ($data['gender'] ?? ''),
            birth_date: (string) ($data['birth_date'] ?? ''),
            area: (string) ($data['area'] ?? ''),
            city: (string) ($data['city'] ?? ''),
            country: (string) ($data['country'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            created: (string) ($data['created'] ?? ''),
        );
    }

    /**
     * Get the Contact ID.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the primary mobile number.
     */
    public function getMobileNumber(): string
    {
        return $this->mob_no;
    }

    /**
     * Get the secondary mobile number.
     */
    public function getSecondaryMobileNumber(): string
    {
        return $this->mob_no2;
    }

    /**
     * Get the full name.
     */
    public function getFullName(): string
    {
        $name = trim($this->fname.' '.$this->lname);

        return $name ?: 'Unknown';
    }

    /**
     * Get the first name.
     */
    public function getFirstName(): string
    {
        return $this->fname;
    }

    /**
     * Get the last name.
     */
    public function getLastName(): string
    {
        return $this->lname;
    }

    /**
     * Get the title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the gender.
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * Get the birth date.
     */
    public function getBirthDate(): string
    {
        return $this->birth_date;
    }

    /**
     * Get the area/locality.
     */
    public function getArea(): string
    {
        return $this->area;
    }

    /**
     * Get the city.
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Get the country.
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * Get the email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get the creation date.
     */
    public function getCreated(): string
    {
        return $this->created;
    }
}
