<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    protected $account;
    /**
     * Display a listing of the resource.
     */

    public function __construct(Account $account)
    {
        $this->account = $account;
    }
    public function index()
    {
        $accounts = $this->account->getAccounts();

        return Inertia::render('accounts/Index', [
            'accounts' => $accounts
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
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $account = $this->account->getAccountById($id);
        $islandGroups = config('custom.island_groups');
        $accountTypes = config('custom.account_types');
        $planTypes = config('custom.plan_types');

        return Inertia::render('accounts/Edit', [
            'account' => $account,
            'islandGroups' => $islandGroups,
            'accountTypes' => $accountTypes,
            'planTypes' => $planTypes
        ]);
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
                Rule::unique('companies', 'email_address1')->ignore($request->account['company_id']),
            ],
            'company.email_address2' => 'nullable|email|max:255',
            'company.email_address3' => 'nullable|email|max:255',
            'company.phone_number1' => 'nullable|string|max:20',
            'company.phone_number2' => 'nullable|string|max:20',
            'company.phone_number3' => 'nullable|string|max:20',
            'company.account_type' => 'required|string',
            'company.island_group' => 'required|integer',
            'company.company_size' => 'nullable|integer',
            'company.contact_person' => 'nullable|string|max:255',
            'company.industry' => 'required|string|max:255',
            
            'account.designation' => 'nullable|string|max:255',
            'account.plan_type' => 'required|integer',
            'account.contract_start_date' => ['required', 'date_format:Y-m-d'],
            'account.contract_end_date' => ['required', 'date_format:Y-m-d'],

        ]);

        $this->account->updateAccount($id, $validated);

        return redirect()->route('accounts.edit', $id)->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        //
    }
}
