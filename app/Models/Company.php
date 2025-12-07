<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    //
    
    protected $fillable = [
        'user_id',
        'company_name',
        'account_type',
        'address',
        'email_address1',
        'email_address2',
        'email_address3',
        'industry',
        'phone_number1',
        'phone_number2',
        'phone_number3',
        'company_size',
        'contact_person',
        'island_group',
        'is_deleted',
    ];

    // A company belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A company has many prospects
    public function prospect()
    {
        return $this->hasOne(Prospect::class, 'company_id');
    }

    public function lead()
    {
        return $this->hasOne(Lead::class, 'company_id');
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'company_id');
    }


}
