<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Employee;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    // Show All Salaries
    public function index()
    {
        $salaries = Salary::with('employee')->latest()->get();
        return view('admin.salaries.all_salaries', compact('salaries'));
    }

    // Show Add Salary Form
    public function create()
    {
        $employees = Employee::all();
        return view('admin.salaries.add_salary', compact('employees'));
    }

    // Handle Add Salary Form Submission
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
        ]);

        Salary::create($request->all());

        return redirect()->route('all.salaries')->with('success', 'Salary added successfully.');
    }

    // Show Edit Salary Form
    public function edit(Salary $salary)
    {
        $employees = Employee::all();
        return view('admin.salaries.edit_salary', compact('salary', 'employees'));
    }

    // Handle Salary Update
    public function update(Request $request, Salary $salary)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
        ]);

        $salary->update($request->all());

        return redirect()->route('all.salaries')->with('success', 'Salary updated successfully.');
    }
public function getEmployeeSalary(Request $request)
{
    $employee = Employee::find($request->id);
    return response()->json([
        'salary' => $employee?->salary ?? 0
    ]);
}
    // Handle Salary Delete
    public function destroy(Salary $salary)
    {
        $salary->delete();
        return redirect()->route('all.salaries')->with('success', 'Salary deleted successfully.');
    }
}
