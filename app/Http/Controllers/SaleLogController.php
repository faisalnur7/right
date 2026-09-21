<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SaleLog;
use Illuminate\Http\Request;

class SaleLogController extends Controller
{
    public function index()
    {
        $sale_logs = SaleLog::latest()->get();
        return view('admin.sale_logs.index', compact('sale_logs'));
    }

    public function create()
    {
        return view('admin.sale_logs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:sale_logs,name|max:255'
        ]);

        SaleLog::create($request->all());

        return redirect()->route('sale_log.list')->with('success', 'Sale Log entry created successfully.');
    }

    public function edit($id)
    {
        $sale_log = SaleLog::query()->findOrFail($id);
        return view('admin.sale_logs.edit', compact('sale_log'));
    }

    public function update(Request $request, $id)
    {
        $saleLog = SaleLog::query()->findOrFail($id);

        $saleLog->update($request->all());

        return redirect()->route('sale_log.list')->with('success', 'Sale Log entry updated successfully.');
    }

    public function destroy(SaleLog $saleLog)
    {
        $saleLog->delete();
        return redirect()->route('sale_log.list')->with('success', 'Sale Log entry deleted successfully.');
    }
}
