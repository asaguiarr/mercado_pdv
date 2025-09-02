<?php
// app/Http/Controllers/CustomerController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Listagem de clientes com busca e paginação dinâmica.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10); // padrão 10 registros por página

        $customers = Customer::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('cpf', 'like', "%{$search}%");
            })
            ->paginate($perPage)
            ->appends($request->all()); // mantém filtros e per_page nos links de paginação

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:customers',
            'contact'   => 'required',
            'rg'        => 'required',
            'cpf'       => 'required',
            'birthdate' => 'required|date',
            'address'   => 'required',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')
                         ->with('success', 'Cliente criado com sucesso!');
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:customers,email,' . $id,
            'contact'   => 'required',
            'rg'        => 'required',
            'cpf'       => 'required',
            'birthdate' => 'required|date',
            'address'   => 'required',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')
                         ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')
                         ->with('success', 'Cliente excluído com sucesso!');
    }
}
