<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'holder_type', 'holder_id', 'type', 'balance', 'version',
    ];

    protected $casts = [
        'balance' => 'integer',
        'version' => 'integer',
    ];

    public function transactions(): HasMany { return $this->hasMany(WalletTransaction::class); }

    public function holder()
    {
        return $this->morphTo('holder', 'holder_type', 'holder_id');
    }

    public function getFormattedBalanceAttribute(): string
    {
        if ($this->type === 'coin') return $this->balance . ' coins';
        return '₹' . number_format($this->balance / 100, 2);
    }

    /**
     * Atomic credit with optimistic locking
     */
    public function credit(int $amount, string $source, ?string $description = null, $referenceId = null, $referenceType = null): WalletTransaction
    {
        $updated = self::where('id', $this->id)
            ->where('version', $this->version)
            ->update([
                'balance' => \DB::raw("balance + {$amount}"),
                'version' => \DB::raw('version + 1'),
            ]);

        if (!$updated) throw new \RuntimeException('Wallet version conflict. Retry the transaction.');

        $this->refresh();

        return $this->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'source' => $source,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'description' => $description,
        ]);
    }

    /**
     * Atomic debit with balance check and optimistic locking
     */
    public function debit(int $amount, string $source, ?string $description = null, $referenceId = null, $referenceType = null): WalletTransaction
    {
        $updated = self::where('id', $this->id)
            ->where('version', $this->version)
            ->where('balance', '>=', $amount)
            ->update([
                'balance' => \DB::raw("balance - {$amount}"),
                'version' => \DB::raw('version + 1'),
            ]);

        if (!$updated) throw new \RuntimeException('Insufficient balance or version conflict.');

        $this->refresh();

        return $this->transactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'source' => $source,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'description' => $description,
        ]);
    }
}
