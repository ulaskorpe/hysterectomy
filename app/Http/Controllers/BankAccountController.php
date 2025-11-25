<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->wantsJson() ){

            return BankAccount::all();

        }

        $this->authorize('viewAny', BankAccount::class);

        return Inertia('BankAccount/Index',[
            'bank_accounts' => BankAccount::paginate(30),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', BankAccount::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', BankAccount::class);

        $request->validate([
            'bank' => 'required',
            'holder' => 'required',
            'iban' => 'required',
            'currency' => 'required',
        ]);

        $account = BankAccount::create($request->all());

        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Banka hesabı oluşturuldu.',
            'data' => $account
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        $this->authorize('view', BankAccount::class);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        $this->authorize('update', BankAccount::class);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $this->authorize('update', BankAccount::class);

        $request->validate([
            'bank' => 'required',
            'holder' => 'required',
            'iban' => 'required',
            'currency' => 'required',
        ]);

        $bankAccount->fill($request->all());
        $bankAccount->save();

        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Hesap güncellendi.',
            'data' => $bankAccount
        ]);
    }

    public function delete(BankAccount $bankAccount)
    {
        $this->authorize('delete', BankAccount::class);

        $bankAccount->delete();
        return back();
    }

    public function restore(BankAccount $bankAccount)
    {
        $this->authorize('restore', BankAccount::class);

        $bankAccount->restore();
        return back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        $this->authorize('forceDelete', BankAccount::class);

        $bankAccount->forceDelete();
        return back();
    }

}
