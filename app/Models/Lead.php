<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    
    const LEAD_EXISTS = 'exists';
    const LEAD_CREATED = 'created';

    protected $fillable = [
        'company_id',
        'designation',
        'email_status',
        'email_status_at',
        'proposal_type',
        'proposal_type_at',
        'phone_call_status',
        'phone_called_at',
        'has_existing_hmo',
        'expiry_date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function getLeads()
    {
        $leads = Lead::with('company')
            ->latest()
            ->get()
            ->filter(function ($lead) {
                return $lead->company->account_type === 'lead';
            })
            ->values();;
        
        return $leads;
    }

    public function updateLead($id, $data)
    {
        $lead = Lead::findOrFail($id);
        $company = Company::findOrFail($lead->company_id);

        if ($lead->phone_call_status != $data['lead']['phone_call_status']) {
            $data['prospect']['phone_called_at'] = now();
        }
        
        $lead->update($data['lead']);
        $company->update($data['company']);

        if ($company->account_type == 'account') {
            try {

                $isExist = Account::where('company_id', $company->id)->first();

                if($isExist) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Account already exists for this company.',
                    ], 409);
                }

                $account = new Account();
                $account->company_id = $company->id;
                $account->save();
                
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'message' => 'Server error.',
                ], 500);
            }
        }
    }

}
