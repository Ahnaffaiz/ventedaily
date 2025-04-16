<?php

namespace App\Models;

use App\Enums\PaymentType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'sale_id', 'date', 'reference', 'amount', 'cash_received', 'cash_change', 'payment_type',
        'bank_id', 'account_number', 'account_name', 'desc'
    ];

    protected $casts = [
        'payment_type' => PaymentType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function getTotalCash()
    {
        return self::where('payment_type', PaymentType::CASH)
                ->whereDate('created_at', Carbon::now())
                ->sum('amount');
    }

    public static function getTotalTransfer($bank_id)
    {
        return self::where('payment_type', PaymentType::TRANSFER)
            ->whereDate('created_at', Carbon::now())
            ->where('bank_id', $bank_id)
            ->sum('amount');
    }
}
