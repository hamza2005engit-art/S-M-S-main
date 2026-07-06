<?php

use App\Http\Controllers\MaterialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\MarkController;
use App\Http\Controllers\V1\AttendanceController;
use App\Http\Controllers\V1\StudentController;
use App\Http\Controllers\V1\TeacherController;
use App\Http\Controllers\V1\SectionController;
use App\Http\Controllers\V1\FeeTypeController;
use App\Http\Controllers\V1\InvoiceController;
use App\Http\Controllers\V1\PaymentController;
use App\Http\Controllers\V1\EmployeeSalaryController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\InvoiceItemController;
use App\Http\Controllers\V1\SalaryController;
use App\Http\Controllers\V1\ScheduleController;
use App\Http\Controllers\V1\ScheduleSlotController;
use App\Http\Controllers\V1\StudyStageController;
use App\Http\Controllers\V1\ExerciseController;
use App\Http\Controllers\V1\TeacherStudentController;
use App\Http\Controllers\V1\BookController;
use App\Http\Controllers\V1\AdminMarkController;
use App\Http\Controllers\V1\AdminBookController;
use App\Http\Controllers\V1\ReportController;
use App\Http\Controllers\V1\UserManagementController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('v1')->name('api.v1')->group(function () {


// Route::get(
//     'reports',
//     ReportController::class
// );

// Route::get(
//     'reports/{report}/download',
//     [ReportController::class,'download']
// );




    Route::post('exercises', [ExerciseController::class, 'store']);
    Route::get('teacher/exercises', [ExerciseController::class, 'getTeacherExercises']);
    Route::put('exercises/{id}', [ExerciseController::class, 'updateExercise']);

    // حذف تمرين
    Route::delete('exercises/{id}', [ExerciseController::class, 'deleteExercise']);
    Route::get('student/exercises', [ExerciseController::class, 'getStudentExercises']);

    Route::get('/teacher/students', [TeacherStudentController::class, 'index']);

    Route::get('/teacher/marks', [MarkController::class, 'index']);

    Route::middleware('auth:api')->get(
        '/student/{student_id}/report',
        [MarkController::class, 'studentReport']
    );
    Route::middleware('auth:api')->put(
        '/teacher/marks/update-by-student',
        [MarkController::class, 'updateByStudent']
    );

    Route::middleware('auth:api')->group(function () {
        Route::post('/teacher/marks', [MarkController::class, 'store']);
    });
    Route::middleware('auth:api')->get(
        '/teacher/marks/summary',
        [MarkController::class, 'teacherMarks']
    );
    Route::middleware('auth:api')->get(
        '/student/my-marks',
        [MarkController::class, 'myMarks']
    );

    Route::get('/teacher/marks/student/{student_id}', [MarkController::class, 'studentMarks']);


    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{id}', [BookController::class, 'show']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        Route::put('/books/{id}', [BookController::class, 'update']);
        Route::delete('/books/{id}', [BookController::class, 'destroy']);
    });


    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::get('me', [AuthController::class, 'me'])->name('me');
    Route::put('approve/{id}', [AuthController::class, 'approveUser'])->name('approve');
    Route::group(['middleware' => 'check.token'], function () {});
    Route::post('forget_password', [AuthController::class, 'forgotPassword'])->name('password.forget');
    Route::post('reset_password', [AuthController::class, 'resetPassword'])->name('password.reset');

    Route::post('create_salary', [EmployeeSalaryController::class, 'createSalary'])->name('salary.create');
    Route::get('get_salary/{type}/{id?}', [EmployeeSalaryController::class, 'getSalary'])->whereIn('type', ['admin', 'teacher'])->name('salary.get');
    Route::put('change_salary/{id}', [EmployeeSalaryController::class, 'changeSalary'])->name('salary.update');
    Route::put('assign_salary/{id}', [EmployeeSalaryController::class, 'assignSalary'])->name('salary.assign')->middleware('role:super_admin');
    Route::put('pay_salary/{id}', [EmployeeSalaryController::class, 'paySalary'])->name('salary.pay');
    Route::get('salary_history', [EmployeeSalaryController::class, 'salaryHistory'])->name('salary.history');

    Route::post('create_fee', [FeeTypeController::class, 'createFee'])->name('fee.create');
    Route::get('get_fee/{id?}', [FeeTypeController::class, 'getFee'])->name('fee.get');
    Route::put('update_fee/{id}', [FeeTypeController::class, 'updateFee'])->name('fee.update');
    Route::delete('delete_fee/{id}', [FeeTypeController::class, 'deleteFee'])->name('fee.delete');

    Route::post('create_invoice', [InvoiceController::class, 'createInvoice'])->name('invoice.create');
    Route::get('get_invoice/{id?}', [InvoiceController::class, 'getInvoice'])->name('invoice.get');
    Route::put('update_invoice/{id}', [InvoiceController::class, 'updateInvoice'])->name('invoice.update');
    Route::delete('delete_invoice/{id}', [InvoiceController::class, 'deleteInvoice'])->name('invoice.delete');

    Route::post('add_invoice_item', [InvoiceItemController::class, 'addInvoiceItem'])->name('invoice.add_item');
    Route::put('update_invoice_item/{id}', [InvoiceItemController::class, 'updateInvoiceItem'])->name('invoice.update_item');
    Route::delete('delete_invoice_item/{id}', [InvoiceItemController::class, 'deleteInvoiceItem'])->name('invoice.delete_item');
    Route::get('get_invoice_items/{invoice_id}', [InvoiceItemController::class, 'getInvoiceItems'])->name('invoice.get_items');

    Route::post('pay_invoice/{invoiceId}/{itemId}', [PaymentController::class, 'payInvoice'])->name('invoice.pay');
    Route::get('payment_history', [PaymentController::class, 'paymentHistory'])->name('invoice.history');

    Route::post('create_study_stage', [StudyStageController::class, 'createStudyStage'])->name('study_stage.create');
    Route::get('get_study_stage/{id?}', [StudyStageController::class, 'getStudyStage'])->name('study_stage.get');
    Route::put('update_study_stage/{id}', [StudyStageController::class, 'updateStudyStage'])->name('study_stage.update');
    Route::delete('delete_study_stage/{id}', [StudyStageController::class, 'deleteStudyStage'])->name('study_stage.delete');

    Route::post('create_section', [SectionController::class, 'createSection'])->name('section.create');
    Route::get('get_section/{id?}', [SectionController::class, 'getSection'])->name('section.get');
    Route::put('update_section/{id}', [SectionController::class, 'updateSection'])->name('section.update');
    Route::delete('delete_section/{id}', [SectionController::class, 'deleteSection'])->name('section.delete');

    Route::post('set_student_in_section', [StudentController::class, 'setStudentInSection'])->name('section.set_student');

    Route::post('assign_teacher_to_section', [TeacherController::class, 'assignTeacherToSection'])->name('section.assign_teacher');
    Route::post('assign_material_to_teacher', [TeacherController::class, 'assignMaterialToTeacher'])->name('teacher.assign_material');

    Route::post('create_schedule', [ScheduleController::class, 'createSchedule'])->name('schedule.create');
    Route::get('get_schedule/{id}', [ScheduleController::class, 'getSchedule'])->name('schedule.get');
    Route::put('update_schedule/{id}', [ScheduleController::class, 'updateSchedule'])->name('schedule.update');
    Route::delete('delete_schedule/{id}', [ScheduleController::class, 'deleteSchedule'])->name('schedule.delete');

    Route::post('schedule_slot/sync', [ScheduleSlotController::class, 'scheduleSlotSync'])->name('schedule.create_slots_sync');
    Route::get('my_schedule/{day?}', [ScheduleController::class, 'mySchedule'])->name('schedule.my');
    Route::get('schedul_of_section/{section_id}/{study_stage_id}', [ScheduleController::class, 'scheduleOfSection'])->name('schedule.section');

    Route::put('take_attendance/bulk', [AttendanceController::class, 'takeAttendance'])->name('attendance.take');
    Route::get('attendance/{section}/{studyStage}', [AttendanceController::class, 'getAttendance'])->name('attendance.get');
    Route::get('/student/attendance/rate', [UserManagementController::class, 'studentAttendanceRatePerDay']);
    Route::get('/get_salary', [UserManagementController::class, 'getSalary']);

    //

    Route::middleware('auth:api')->prefix('admin')->group(function () {
        Route::get('/marks', [AdminMarkController::class, 'index']);
        Route::post('/marks', [AdminMarkController::class, 'store']);
        Route::put('/marks/{id}', [AdminMarkController::class, 'update']);
        Route::delete('/marks/{id}', [AdminMarkController::class, 'destroy']);
        Route::get('/student/{student_id}/report', [AdminMarkController::class, 'studentReport']);
    });
    //


    Route::middleware('auth:api')->prefix('admin')->group(function () {
        Route::post('/books', [AdminBookController::class, 'store']);
        Route::put('/books/{id}', [AdminBookController::class, 'update']);
        Route::delete('/books/{id}', [AdminBookController::class, 'destroy']);
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::get('/users/pending', [UserManagementController::class, 'pendingUsers']);
        Route::get('/admin/users/{id}', [UserManagementController::class, 'show']);
        Route::get('/teachers', [UserManagementController::class, 'teachers']);
        Route::get('/teachers/{id}', [UserManagementController::class, 'showTeacher']);
        Route::put('/teachers/{id}', [UserManagementController::class, 'updateTeacher']);
        Route::delete('/teachers/{id}', [UserManagementController::class, 'deleteTeacher']);

        Route::get('/count/teacher', [UserManagementController::class, 'countTeacher']);
        Route::get('/count/student', [UserManagementController::class, 'countStudent']);
        ///
        Route::get('/students', [UserManagementController::class, 'students']);
        Route::get('/students/{id}', [UserManagementController::class, 'showStudent']);
        Route::put('/students/{id}', [UserManagementController::class, 'updateStudent']);
        Route::delete('/students/{id}', [UserManagementController::class, 'deleteStudent']);
        ///
        Route::get('/supervisors', [UserManagementController::class, 'supervisors']);
        Route::get('/supervisors/{id}', [UserManagementController::class, 'showSupervisor']);
        Route::put('/supervisors/{id}', [UserManagementController::class, 'updateSupervisor']);
        Route::delete('/supervisors/{id}', [UserManagementController::class, 'deleteSupervisor']);
        ////
    });
    //

    Route::middleware('auth:api')->prefix('admin')->group(function () {
        Route::post('/books', [AdminBookController::class, 'store']);
        Route::put('/books/{id}', [AdminBookController::class, 'update']);
        Route::delete('/books/{id}', [AdminBookController::class, 'destroy']);
    });
    //

    Route::post('create_material', [MaterialController::class, 'createMaterial'])->name('material.create');
    Route::get('get_material/{study_stage_id?}', [MaterialController::class, 'getMaterial'])->name('material.get');


});
