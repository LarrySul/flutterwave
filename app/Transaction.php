<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'account_id', 'ip_address', 'amount', 'charged_amount', 'currency', 'customer_email', 'customer_name',
        'customer_id', 'payment_type', 'transaction_reference', 'rave_reference', 'status'
    ];
}
