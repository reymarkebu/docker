<?php

namespace App\Models;

use App\Models\Company;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PhpParser\Node\Stmt\TryCatch;

class Prospect extends Model
{
    /** @use HasFactory<\Database\Factories\ProspectFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'email_status',
        'email_sent_at',
        'phone_call_status',
        'phone_called_at',
        'notes',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function getProspects()
    {
        $phoneCallStatus = config("custom.phone_call_statuses");
        $emailStatus = config("custom.email_statuses");

        $pStatus = [];
        $eStatus = [];

        foreach ($phoneCallStatus as $key => $val) {
            $pStatus[$val['id']] = $val['label'];
        }

        foreach ($emailStatus as $key => $val) {
            $eStatus[$val['id']] = $val['label'];
        }

        $prospects = Prospect::with('company')
            ->latest()
            ->get()
            ->map(function ($prospect) use ($pStatus, $eStatus) {
                $prospect->phone_call_status_label = $pStatus[$prospect->phone_call_status] ?? 'Unknown';
                $prospect->email_status_label = $eStatus[$prospect->email_status] ?? 'Unknown';

                return $prospect;
            })->filter(function ($prospect) {
                return $prospect->company->account_type === 'prospect';
            })
            ->values();

        return $prospects;
    }

    public function storeProspect($data)
    {
        # Create company
        $company = Company::create($data['company']);
        Prospect::create([
            'company_id' => $company->id,
            'notes' => $data['prospect']['notes'],
        ]);
    }

    public function getProspectById($id)
    {
        return Prospect::where('id', $id)->first();
    }

    public function updateProspectById($id, $data)
    {

        $prospect = Prospect::findOrFail($id);
        $company = Company::findOrFail($prospect->company_id);

        if ($prospect->phone_call_status != $data['prospect']['phone_call_status']) {
            $data['prospect']['phone_called_at'] = now();
        }

        if ($prospect->email_status != $data['prospect']['email_status']) {
            $data['prospect']['email_sent_at'] = now();
        }

        $prospect->update($data['prospect']);
        $company->update($data['company']);

        if ($company->account_type == 'lead') {
            try {

                $isExist = Lead::where('company_id', $company->id)->first();

                if($isExist) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Lead already exists for this company.',
                    ], 409);
                }

                $lead = new Lead();
                $lead->company_id = $company->id;
                $lead->save();
                
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'message' => 'Server error.',
                ], 500);
            }
        }
    }

    public function deleteProspectById($id)
    {
        $prospect = Prospect::findOrFail($id);
        $company = Company::findOrFail($prospect->company_id);

        $prospect->delete();
        $company->delete();
    }
}
