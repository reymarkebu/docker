<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class ProspectController extends Controller
{
    protected $prospect;


    public function __construct(Prospect $prospect)
    {
        $this->prospect = $prospect;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $prospects = $this->prospect->getProspects();
        $emailStatuses = config('custom.email_statuses');
        $phoneCallStatuses = config('custom.phone_call_statuses');
        $islandGroups = config('custom.island_groups');
        $accountTypes = config('custom.account_types');



        return Inertia::render('prospects/Index', [
            'prospects' => $prospects,
            'emailStatuses' => $emailStatuses,
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
        $userId = Auth()->user()->id;
        $request->merge(['user_id' => $userId]);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',

            'company.company_name' => 'required|string|max:255',
            'company.address' => 'required|string|max:500',
            'company.industry' => 'required|string|max:255',
            'company.email_address1' => 'email|max:255|unique:companies,email_address1',
            'company.email_address2' => 'nullable|email|max:255',
            'company.email_address3' => 'nullable|email|max:255',
            'company.phone_number1' => 'nullable|string|max:20',
            'company.phone_number2' => 'nullable|string|max:20',
            'company.phone_number3' => 'nullable|string|max:20',
            'company.company_size' => 'nullable|integer',
            'company.contact_person' => 'nullable|string|max:255',
            'company.island_group' => 'required|integer',

            'prospect.notes' => 'nullable|string',
        ]);

        $validated['company']['user_id'] = $validated['user_id'];
        $validated['company']['account_type'] = config("custom.account_types.prospect");

        $this->prospect->storeProspect($validated);

        return to_route('prospects.index')->with('success', 'Prospect created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        return Inertia::render('prospects/Index', [
            'prospect' => Prospect::findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $userId = Auth()->user()->id;
        $request->merge(['user_id' => $userId]);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'company.company_name' => 'required|string|max:255',
            'company.address' => 'required|string|max:500',
            'company.email_address1' => [
                'email',
                'max:255',
                Rule::unique('companies', 'email_address1')->ignore($request->prospect['company_id']),
            ],
            'company.email_address2' => 'nullable|email|max:255',
            'company.email_address3' => 'nullable|email|max:255',

            'company.industry' => 'required|string|max:255',
            'company.phone_number1' => 'nullable|string|max:20',
            'company.phone_number2' => 'nullable|string|max:20',
            'company.phone_number3' => 'nullable|string|max:20',

            'company.company_size' => 'nullable|integer',
            'company.contact_person' => 'nullable|string|max:255',
            'company.island_group' => 'required|integer',
            'company.account_type' => 'required|string',

            'prospect.email_status' => 'nullable|integer',
            'prospect.phone_call_status' => 'nullable|integer',
            'prospect.notes' => 'nullable|string',
        ]);
        
        $validated['company']['user_id'] = $validated['user_id'];
        $this->prospect->updateProspectById($id, $validated);

        return redirect()->route('prospects.index')->with('success', 'Prospect updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->prospect->deleteProspectById($id);
        return redirect()->route('prospects.index')->with('success', 'Prospect deleted successfully.');
    }
}
