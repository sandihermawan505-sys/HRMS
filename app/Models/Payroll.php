<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'user_id', 'month', 'year','user', 'basic_salary', 'allowances',
        'deductions', 'bonus', 'net_salary', 'status', 'paid_at'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'bonus' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }
}
