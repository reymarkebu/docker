<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    protected $fillable = [
        'company_id',
        'plan_type',
        'designation',
        'contract_start_date',
        'contract_end_date',
        'notes'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function getAccounts()
    {
        $planTypes = config("custom.plan_types");

        $pTypes = [];
        foreach ($planTypes as $key => $val) {
            $pTypes[$val['id']] = $val['label'];
        }

         $accounts = Account::with('company')
            ->latest()
            ->get()
             ->map(function ($account) use ($pTypes) {
                $account->plan_type_label = $pTypes[$account->plan_type] ?? 'Unknown';

                return $account;
             })->filter(function ($account) {
                return $account->company->account_type === 'account';
            })
            ->values();
        
        return $accounts;
    }

    public function getAccountById($id)
    {
        $planTypes = config("custom.plan_types");

        $pTypes = [];
        foreach ($planTypes as $key => $val) {
            $pTypes[$val['id']] = $val['label'];
        }
        
        $account = Account::with('company')
            ->where('id', $id)
            ->first();
        
        $account->plan_type_label = $pTypes[$account->plan_type] ?? 'Unknown';

        return $account;
    }

    public function updateAccount($id, $data)
    {
        $account = Account::findOrFail($id);
        $company = Company::findOrFail($account->company_id);

        $account->update($data['account']);
        $company->update($data['company']);
    }


}
