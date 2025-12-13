<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

use Gowelle\BeemAfrica\Enums\Gender;
use Gowelle\BeemAfrica\Enums\Title;

/**
 * Data Transfer Object for Contact request.
 */
class ContactRequest
{
    /**
     * @param  string  $mob_no  Primary mobile number (required)
     * @param  array  $addressbook_id  Array of addressbook IDs (required)
     * @param  string|null  $fname  First name (optional)
     * @param  string|null  $lname  Last name (optional)
     * @param  Title|string|null  $title  Title: Mr. / Mrs. / Ms. (optional)
     * @param  Gender|string|null  $gender  Gender: male / female (optional)
     * @param  string|null  $mob_no2  Secondary mobile number (optional)
     * @param  string|null  $email  Email address (optional)
     * @param  string|null  $country  Country name (optional)
     * @param  string|null  $city  City name (optional)
     * @param  string|null  $area  Locality/area (optional)
     * @param  string|null  $birth_date  Birth date in yyyy-mm-dd format (optional)
     */
    public function __construct(
        public readonly string $mob_no,
        public readonly array $addressbook_id,
        public readonly ?string $fname = null,
        public readonly ?string $lname = null,
        public readonly Title|string|null $title = null,
        public readonly Gender|string|null $gender = null,
        public readonly ?string $mob_no2 = null,
        public readonly ?string $email = null,
        public readonly ?string $country = null,
        public readonly ?string $city = null,
        public readonly ?string $area = null,
        public readonly ?string $birth_date = null,
    ) {
        $this->validate();
    }

    /**
     * Validate the Contact request data.
     */
    protected function validate(): void
    {
        if (empty($this->mob_no)) {
            throw new \InvalidArgumentException('Mobile number (mob_no) is required for contact');
        }

        // Basic phone number validation (10-15 digits)
        if (! preg_match('/^[0-9]{10,15}$/', $this->mob_no)) {
            throw new \InvalidArgumentException('Invalid phone number format. Must be 10-15 digits in international format without +');
        }

        if (empty($this->addressbook_id)) {
            throw new \InvalidArgumentException('At least one addressbook ID is required');
        }

        // Validate secondary phone number if provided
        if ($this->mob_no2 !== null && ! empty($this->mob_no2)) {
            if (! preg_match('/^[0-9]{10,15}$/', $this->mob_no2)) {
                throw new \InvalidArgumentException('Invalid secondary phone number format. Must be 10-15 digits in international format without +');
            }
        }

        // Validate birth date format if provided
        if ($this->birth_date !== null && ! empty($this->birth_date)) {
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->birth_date)) {
                throw new \InvalidArgumentException('Birth date must be in yyyy-mm-dd format');
            }
        }

        // Validate gender if provided
        if ($this->gender !== null && ! empty($this->gender)) {
            if ($this->gender instanceof Gender) {
                // Enum is always valid
            } elseif (! in_array($this->gender, ['male', 'female'], true)) {
                throw new \InvalidArgumentException('Gender must be either "male" or "female"');
            }
        }

        // Validate title if provided
        if ($this->title !== null && ! empty($this->title)) {
            if ($this->title instanceof Title) {
                // Enum is always valid
            } elseif (! in_array($this->title, ['Mr.', 'Mrs.', 'Ms.'], true)) {
                throw new \InvalidArgumentException('Title must be one of: Mr., Mrs., Ms.');
            }
        }
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        $data = [
            'mob_no' => $this->mob_no,
            'addressbook_id' => $this->addressbook_id,
        ];

        if ($this->fname !== null) {
            $data['fname'] = $this->fname;
        }

        if ($this->lname !== null) {
            $data['lname'] = $this->lname;
        }

        if ($this->title !== null) {
            $data['title'] = $this->title instanceof Title ? $this->title->value : $this->title;
        }

        if ($this->gender !== null) {
            $data['gender'] = $this->gender instanceof Gender ? $this->gender->value : $this->gender;
        }

        if ($this->mob_no2 !== null) {
            $data['mob_no2'] = $this->mob_no2;
        }

        if ($this->email !== null) {
            $data['email'] = $this->email;
        }

        if ($this->country !== null) {
            $data['country'] = $this->country;
        }

        if ($this->city !== null) {
            $data['city'] = $this->city;
        }

        if ($this->area !== null) {
            $data['area'] = $this->area;
        }

        if ($this->birth_date !== null) {
            $data['birth_date'] = $this->birth_date;
        }

        return $data;
    }
}
