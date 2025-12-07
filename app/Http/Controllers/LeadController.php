<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{

    protected $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leads = $this->lead->getLeads();
        $emailStatuses = config('custom.email_statuses');
        $proposalTypes = config('custom.proposal_types');
        $phoneCallStatuses = config('custom.phone_call_statuses');
        $islandGroups = config('custom.island_groups');
        $accountTypes = config('custom.account_types');

        return Inertia::render('leads/Index', [
            'leads' => $leads,
            'emailStatuses' => $emailStatuses,
            'proposalTypes' => $proposalTypes,
            'phoneCallStatuses' => $phoneCallStatuses,
            'islandGroups' => $islandGroups,
            'accountTypes' => $accountTypes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'company.company_name' => 'required|string|max:255',
            'company.address' => 'required|string|max:500',
            'company.email_address1' => [
                'email',
                'max:255',
                Rule::unique('companies', 'email_address1')->ignore($request->lead['company_id']),
            ],
            'company.email_address2' => 'nullable|email|max:255',
            'company.email_address3' => 'nullable|email|max:255',

            'lead.proposal_type' => 'nullable|integer',
            'company.phone_number1' => 'nullable|string|max:20',
            'company.phone_number2' => 'nullable|string|max:20',
            'company.phone_number3' => 'nullable|string|max:20',
            'company.account_type' => 'required|string',

            'lead.phone_call_status' => 'nullable|integer',
            'company.island_group' => 'required|integer',
            'company.company_size' => 'nullable|integer',
            'company.contact_person' => 'nullable|string|max:255',
            'lead.designation' => 'nullable|string|max:255',
            
            'company.industry' => 'required|string|max:255',
            'lead.has_existing_hmo' => 'required|string|max:255',
            'lead.expiry_date' => 'required|string|max:255',

        ]);

        $this->lead->updateLead($id, $validated);

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        //
    }
}
