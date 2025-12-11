<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Models;

use Gowelle\BeemAfrica\DTOs\CallbackPayload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $transaction_id
 * @property string $reference_number
 * @property float $amount
 * @property string $status
 * @property string|null $msisdn
 * @property \Carbon\Carbon|null $processed_at
 * @property array|null $raw_payload
 * @property int|null $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static self create(array $attributes = [])
 * @method static self updateOrCreate(array $attributes, array $values = [])
 * @method static \Illuminate\Database\Eloquent\Builder|self query()
 * @method static \Illuminate\Database\Eloquent\Builder|self where($column, $operator = null, $value = null)
 */
class BeemTransaction extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    protected $table = 'beem_transactions';

    protected $fillable = [
        'transaction_id',
        'reference_number',
        'amount',
        'status',
        'msisdn',
        'processed_at',
        'raw_payload',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    /**
     * Create a transaction record from a callback payload.
     */
    public static function fromCallback(CallbackPayload $payload): self
    {
        return static::updateOrCreate(
            ['transaction_id' => $payload->transactionId],
            [
                'reference_number' => $payload->referenceNumber,
                'amount' => $payload->getAmountAsFloat(),
                'status' => $payload->status,
                'msisdn' => $payload->msisdn ?: null,
                'processed_at' => $payload->getTimestampAsDateTime(),
                'raw_payload' => $payload->toArray(),
            ]
        );
    }

    /**
     * Create a pending transaction before redirect.
     */
    public static function createPending(
        string $transactionId,
        string $referenceNumber,
        float $amount,
        ?string $msisdn = null,
        ?int $userId = null
    ): self {
        return static::create([
            'transaction_id' => $transactionId,
            'reference_number' => $referenceNumber,
            'amount' => $amount,
            'status' => self::STATUS_PENDING,
            'msisdn' => $msisdn,
            'user_id' => $userId,
        ]);
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        $userModel = config('beem.user_model', 'App\\Models\\User');

        return $this->belongsTo($userModel);
    }

    /**
     * Check if the transaction was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * Check if the transaction failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if the transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Scope to find by reference number.
     */
    public function scopeByReference($query, string $referenceNumber)
    {
        return $query->where('reference_number', $referenceNumber);
    }

    /**
     * Scope to find successful transactions.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    /**
     * Scope to find failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope to find pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
