<?php

namespace App\Http\Controllers\V1;
use App\Models\Teacher;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Models\Student;
use App\Models\SuperAdmin;
class UserManagementController extends Controller
{
public function index()
{
    $users = User::with('roles')
        ->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })
        ->latest()
        ->get();

    $users = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'location' => $user->location,
            'profile_image' => $user->profile_image,
            'approved' => $user->approved,
            'role' => $user->getRoleNames()->first(),
            'created_at' => $user->created_at,
        ];
    });

    return response()->json([
        'status' => true,
        'data' => $users
    ]);
}




public function pendingUsers()
{
    $users = User::where('approved', false)
        ->latest()
        ->get();

    return response()->json([
        'status' => true,
        'count' => $users->count(),
        'data' => $users->map(function ($user) {
            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'location' => $user->location,
                'roles' => $user->getRoleNames(),
                'created_at' => $user->created_at,
            ];
        })
    ]);
}

public function teachers()
{
    $teachers = Teacher::with('user')
        ->latest()
        ->get();

    return response()->json([
        'status' => true,
        'message' => 'Teachers fetched successfully',
        'data' => $teachers->map(function ($teacher) {
            return [
                'teacher_id' => $teacher->id,
                'salary' => $teacher->salary,

                'user' => [
                    'id' => $teacher->user->id,
                    'full_name' => $teacher->user->full_name,
                    'email' => $teacher->user->email,
                    'phone' => $teacher->user->phone,
                    'location' => $teacher->user->location,
                    'profile_image' => $teacher->user->profile_image,
                    'approved' => $teacher->user->approved,
                ]
            ];
        })
    ]);
}

public function showTeacher($id)
{
    $teacher = Teacher::with([
        'user.roles',
        'sections.studyStage',
        'materials',
        'salaries'
    ])->find($id);

    if (!$teacher) {
        return response()->json([
            'status' => false,
            'message' => 'Teacher not found'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => [
            'teacher_id' => $teacher->id,
            'salary' => $teacher->salary,

            'user' => [
                'id' => $teacher->user->id,
                'full_name' => $teacher->user->full_name,
                'email' => $teacher->user->email,
                'phone' => $teacher->user->phone,
                'location' => $teacher->user->location,
                'profile_image' => $teacher->user->profile_image,
                'approved' => $teacher->user->approved,
                'role' => $teacher->user->getRoleNames()->first(),
                'created_at' => $teacher->user->created_at,
            ],

            'sections' => $teacher->sections,
            'materials' => $teacher->materials,
            'salaries' => $teacher->salaries,

            'statistics' => [
                'sections_count' => $teacher->sections->count(),
                'materials_count' => $teacher->materials->count(),
            ]
        ]
    ]);
}


public function updateTeacher(Request $request, $id)
{
    $teacher = Teacher::with('user')->find($id);

    if (!$teacher) {
        return response()->json([
            'status' => false,
            'message' => 'Teacher not found'
        ], 404);
    }

    $request->validate([
        'full_name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:users,email,' . $teacher->user->id,
        'phone' => 'sometimes|string|max:20',
        'location' => 'nullable|string|max:255',
        'salary' => 'nullable|integer|min:0',
    ]);

    $teacher->user->update([
        'full_name' => $request->full_name ?? $teacher->user->full_name,
        'email' => $request->email ?? $teacher->user->email,
        'phone' => $request->phone ?? $teacher->user->phone,
        'location' => $request->location ?? $teacher->user->location,
    ]);

    $teacher->update([
        'salary' => $request->salary ?? $teacher->salary,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Teacher updated successfully',
        'data' => $teacher->load('user')
    ]);
}
public function deleteTeacher($id)
{
    $teacher = Teacher::with('user')->find($id);

    if (!$teacher) {
        return response()->json([
            'status' => false,
            'message' => 'Teacher not found'
        ], 404);
    }

    $teacher->user->delete();

    return response()->json([
        'status' => true,
        'message' => 'Teacher deleted successfully'
    ]);
}


public function students()
{
    $students = Student::with('user')
        ->latest()
        ->get();

    return response()->json([
        'status' => true,
        'data' => $students->map(function ($student) {
            return [
                'student_id' => $student->id,
                'student_number' => $student->student_number,

                'user' => [
                    'id' => $student->user->id,
                    'full_name' => $student->user->full_name,
                    'email' => $student->user->email,
                    'phone' => $student->user->phone,
                    'location' => $student->user->location,
                    'approved' => $student->user->approved,
                ],
            ];
        })
    ]);
}



public function showStudent($id)
{
    $student = Student::with([
        'user',
        'section.studyStage',
        'invoices',
        'user.roles'
    ])->find($id);

    if (!$student) {
        return response()->json([
            'status' => false,
            'message' => 'Student not found'
        ], 404);
    }

    // جلب العلامات (Marks) إذا عندك جدول marks مربوط بـ student_id
    $marks = \App\Models\Mark::where('student_id', $student->id)->get();

    return response()->json([
        'status' => true,
        'data' => [
            'student_id' => $student->id,
            'student_number' => $student->student_number,

            'user' => [
                'id' => $student->user->id,
                'full_name' => $student->user->full_name,
                'email' => $student->user->email,
                'phone' => $student->user->phone,
                'location' => $student->user->location,
                'approved' => $student->user->approved,
                'role' => $student->user->getRoleNames()->first(),
                'created_at' => $student->user->created_at,
            ],

            'section' => $student->section,

            'study_stage' => $student->section?->studyStage,

            'invoices' => $student->invoices,

            'marks' => $marks,

            'statistics' => [
                'marks_count' => $marks->count(),
                'invoices_count' => $student->invoices->count(),
            ]
        ]
    ]);
}
public function updateStudent(Request $request, $id)
{
    $student = Student::with('user')->find($id);

    if (!$student) {
        return response()->json([
            'status' => false,
            'message' => 'Student not found'
        ], 404);
    }

    $request->validate([
        'full_name' => 'sometimes|string',
        'email' => 'sometimes|email|unique:users,email,' . $student->user->id,
        'phone' => 'sometimes|string',
        'section_id' => 'nullable|exists:sections,id',
    ]);

    $student->user->update([
        'full_name' => $request->full_name ?? $student->user->full_name,
        'email' => $request->email ?? $student->user->email,
        'phone' => $request->phone ?? $student->user->phone,
    ]);

    $student->update([
        'section_id' => $request->section_id ?? $student->section_id,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Student updated successfully',
        'data' => $student->load('user')
    ]);
}
public function deleteStudent($id)
{
    $student = Student::with('user')->find($id);

    if (!$student) {
        return response()->json([
            'status' => false,
            'message' => 'Student not found'
        ], 404);
    }

    // حذف user (وسيتم حذف student تلقائي بسبب cascade)
    $student->user->delete();

    return response()->json([
        'status' => true,
        'message' => 'Student deleted successfully'
    ]);
}



public function supervisors()
{
    $supervisors = SuperAdmin::with('user')->latest()->get();

    return response()->json([
        'status' => true,
        'count' => $supervisors->count(),
        'data' => $supervisors->map(function ($sup) {
            return [
                'id' => $sup->id,

                'user' => [
                    'id' => $sup->user->id,
                    'full_name' => $sup->user->full_name,
                    'email' => $sup->user->email,
                    'phone' => $sup->user->phone,
                    'location' => $sup->user->location,
                    'approved' => $sup->user->approved,
                    'created_at' => $sup->user->created_at,
                ],
            ];
        })
    ]);
}






public function showSupervisor($id)
{
    $supervisor = SuperAdmin::with('user')
        ->find($id);

    if (!$supervisor) {
        return response()->json([
            'status' => false,
            'message' => 'Supervisor not found'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => [
            'id' => $supervisor->id,

            'user' => [
                'id' => $supervisor->user->id,
                'full_name' => $supervisor->user->full_name,
                'email' => $supervisor->user->email,
                'phone' => $supervisor->user->phone,
                'location' => $supervisor->user->location,
                'approved' => $supervisor->user->approved,
                'created_at' => $supervisor->user->created_at,
            ],

            'roles' => $supervisor->user->getRoleNames(),

            'statistics' => [
                // نضيفها لاحقًا (طلاب / أساتذة مرتبطين)
                'assigned_students' => 0,
                'assigned_teachers' => 0,
            ]
        ]
    ]);
}


public function updateSupervisor(Request $request, $id)
{
    $supervisor = SuperAdmin::with('user')->find($id);

    if (!$supervisor) {
        return response()->json([
            'status' => false,
            'message' => 'Supervisor not found'
        ], 404);
    }

    $request->validate([
        'full_name' => 'sometimes|string',
        'email' => 'sometimes|email|unique:users,email,' . $supervisor->user->id,
        'phone' => 'sometimes|string',
        'location' => 'nullable|string',
        'approved' => 'sometimes|boolean',
    ]);

    // تحديث user
    $supervisor->user->update([
        'full_name' => $request->full_name ?? $supervisor->user->full_name,
        'email' => $request->email ?? $supervisor->user->email,
        'phone' => $request->phone ?? $supervisor->user->phone,
        'location' => $request->location ?? $supervisor->user->location,
        'approved' => $request->approved ?? $supervisor->user->approved,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Supervisor updated successfully',
        'data' => $supervisor->load('user')
    ]);
}
public function deleteSupervisor($id)
{
    $supervisor = SuperAdmin::with('user')->find($id);

    if (!$supervisor) {
        return response()->json([
            'status' => false,
            'message' => 'Supervisor not found'
        ], 404);
    }

    // حذف user (وسيتم حذف super_admin تلقائيًا إذا عندك cascade)
    $supervisor->user->delete();

    return response()->json([
        'status' => true,
        'message' => 'Supervisor deleted successfully'
    ]);
}

    public function show($id)
    {
        $user = User::with([
            'teacher',
            'student',
            'admin',
            'superAdmin',
            'roles'
        ])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => new UserResource($user)
        ]);
    }
}