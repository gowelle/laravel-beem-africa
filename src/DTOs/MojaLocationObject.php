<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Location object DTO with coordinates.
 */
class MojaLocationObject
{
    public function __construct(
        public readonly string $latitude,
        public readonly string $longitude,
    ) {
        $this->validate();
    }

    /**
     * Validate the location data.
     */
    protected function validate(): void
    {
        if ($this->latitude === '') {
            throw new \InvalidArgumentException('Latitude is required');
        }

        if ($this->longitude === '') {
            throw new \InvalidArgumentException('Longitude is required');
        }

        // Basic validation for latitude (-90 to 90) and longitude (-180 to 180)
        $lat = (float) $this->latitude;
        $lon = (float) $this->longitude;

        if ($lat < -90 || $lat > 90) {
            throw new \InvalidArgumentException('Latitude must be between -90 and 90');
        }

        if ($lon < -180 || $lon > 180) {
            throw new \InvalidArgumentException('Longitude must be between -180 and 180');
        }
    }

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            latitude: (string) ($data['latitude'] ?? ''),
            longitude: (string) ($data['longitude'] ?? ''),
        );
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    /**
     * Get latitude as float.
     */
    public function getLatitudeFloat(): float
    {
        return (float) $this->latitude;
    }

    /**
     * Get longitude as float.
     */
    public function getLongitudeFloat(): float
    {
        return (float) $this->longitude;
    }
}
