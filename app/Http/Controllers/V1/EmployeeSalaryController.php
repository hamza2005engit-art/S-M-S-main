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

        return response()->json([
            'data' => new EmployeeSalaryResource($employeeSalary),
        ], 201);
    }

    public function getSalary($type, $id = null)
    {
        if($id){
            // if ($type === 'admin') {
            //     $employee = Admin::findOrFail($id);
            // } else {
            //     $employee = Teacher::findOrFail($id);
            // }
            $employeeSalary = EmployeeSalary::findOrFail($id);
//$salaries = $employee->salaries()->latest('date')->with('employeeable')->with('user')->get();
            return new EmployeeSalaryResource($employeeSalary);
        }
            $employeeSalaries = EmployeeSalary::whereHasMorph(
                'employeeable',
                [Admin::class, Teacher::class],
                function ($query) use ($type) {
                    if ($type === 'admin') {
                        $query->where('employeeable_type', Admin::class);
                    } else {
                        $query->where('employeeable_type', Teacher::class);
                    }
                }
            )->latest('date')->paginate(10);
            return EmployeeSalaryResource::collection($employeeSalaries);
        // if ($id) {
        //     $employeeSalary = EmployeeSalary::findOrFail($id);

        //     return new EmployeeSalaryResource($employeeSalary);
        // }

        // $employeeSalary = EmployeeSalary::orderby('date', 'desc')->paginate(10);

        // return EmployeeSalaryResource::collection($employeeSalary);
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

        $salaries = $employee->salaries()->latest('date')->paginate(10);

        return EmployeeSalaryResource::collection($salaries);
    }
}
