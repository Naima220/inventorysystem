<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Show all employees
    public function index()
    {
        $employees = Employee::all();
        return view('admin.employees.index', compact('employees'));
    }

    // Show form to create a new employee
    public function create()
    {
        return view('admin.employees.create');
    }

    // Store new employee to database
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email',
            'phone'     => 'required|string|max:20',
            'address'   => 'required|string|max:255',
            'position'  => 'required|string|max:100',
            'hire_date' => 'required|date',
            'salary'    => 'required|numeric|min:0',
            'status'    => 'required|in:Active,Inactive',
        ]);

        Employee::create($request->all());

        return redirect()->route('all.employees')->with('success', 'Employee added successfully.');
    }

    // Show form to edit an existing employee
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }

    // Update employee data (updated as you requested)
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email',
            'phone'     => 'required|string|max:20',
            'address'   => 'required|string|max:255',
            'position'  => 'required|string|max:100',
            'hire_date' => 'required|date',
            'salary'    => 'required|numeric|min:0',
            'status'    => 'required|in:Active,Inactive',
        ]);

        $employee->update($request->all());

        return redirect()->route('all.employees')->with('success', 'Employee updated successfully.');
    }

    // Delete an employee
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('all.employees')->with('success', 'Employee deleted successfully.');
    }
}
