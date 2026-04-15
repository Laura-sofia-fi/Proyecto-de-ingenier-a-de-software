<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', [
            'company' => Company::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $company = Company::current();
        $company->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'nit' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'invoice_prefix' => ['required', 'string', 'max:10'],
        ]));

        return redirect()->route('settings.edit')->with('status', 'Datos de la empresa actualizados.');
    }
}
