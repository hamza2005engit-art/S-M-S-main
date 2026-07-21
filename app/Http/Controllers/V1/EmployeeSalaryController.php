<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EmployeeResource;
use App\Http\Resources\V1\EmployeeSalaryResource;
use App\Models\Admin;
use App\Models\EmployeeSalary;
use App\Models\Teacher;
use Illuminate\Http\Request;

class EmployeeSalaryController extends Controller
{

    public function assignSalary($id, Request $request)
    {
        $request->validate([
            'salary' => 'required|integer|min:0',
            'type' => 'required|string|in:admin,teacher',
        ]);

        if ($request->type === 'admin') {

            $employee = Admin::findOrFail($id);
        } else {

            $employee = Teacher::findOrFail($id);
        }

        $employee->update([
            'salary' => $request->salary,
        ]);

        return response()->json([
            'data' => new EmployeeResource($employee),
        ], 200);
    }

    public  function createSalary(Request $request)
    {
        $request->validate([
            'employeeable_id' => 'required|integer',
            'employeeable_type' => 'required|string',
            'salary' => 'required|integer',
            'date' => 'required|date|date_format:Y-m-d',
        ]);
        $employeeSalary = EmployeeSalary::create([
            'employeeable_id' => $request->employeeable_id,
            'employeeable_type' => $request->employeeable_type,
            'salary' => $request->salary,
            'date' => $request->date,
            'paid' => $request->paid ?? false,
            'paid_at' => $request->paid ? now() : null,
        ]);

        $employeeSalary->save();

        $employeeSalary = EmployeeSalary::with('employeeable.user')
            ->findOrFail($employeeSalary->id);

        return response()->json([
            'data' => new EmployeeSalaryResource($employeeSalary),
        ], 201);
    }


public function getSalary($type, $id = null)
{
   $employeeType = match ($type) {
    'admin' => 'admin',
    'teacher' => 'teacher',
    default => abort(404),
};

    if ($id) {

        $employeeSalary = EmployeeSalary::with('employeeable.user')
            ->where('employeeable_type', $employeeType)
            ->findOrFail($id);

        return response()->json([
            'data' => new EmployeeSalaryResource($employeeSalary),
        ], 200);
    }

    $employeeSalaries = EmployeeSalary::with('employeeable.user')
        ->where('employeeable_type', $employeeType)
        ->latest()
        ->paginate(10);

    return EmployeeSalaryResource::collection($employeeSalaries);
}


    public function paySalary($id)
    {
        $employeeSalary = EmployeeSalary::findOrFail($id);

        if ($employeeSalary->paid) {
            return response()->json([
                'message' => 'Salary already paid',
            ], 400);
        }

        $employeeSalary->update([
            'paid' => true,
            'paid_at' => now(),
        ]);

        $employeeSalary = EmployeeSalary::with('employeeable.user')
            ->findOrFail($id);

        return response()->json([
            'data' => new EmployeeSalaryResource($employeeSalary),
        ], 200);
    }

    public function changeSalary($id, Request $request)
    {
        $employeeSalary = EmployeeSalary::findOrFail($id);

        $request->validate([
            'salary' => 'sometimes|integer|min:0',
            'date' => 'date|sometimes|date_format:Y-m-d',
        ]);

        $employeeSalary->update($request->only(['salary', 'date']));

        $employeeSalary->save();

        $employeeSalary = EmployeeSalary::with('employeeable.user')
            ->findOrFail($id);

        return response()->json([
            'data' => new EmployeeSalaryResource($employeeSalary),
        ], 200);
    }

    public function salaryHistory()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        $employee = $user->admin ?? $user->teacher;
        if (!$employee) {
            return response()->json([
                'message' => 'unauthorized',
            ], 404);
        }

        $salaries = $employee->salaries()
            ->with('employeeable.user')
            ->latest('date')
            ->paginate(10);

        return EmployeeSalaryResource::collection($salaries);
    }
}
