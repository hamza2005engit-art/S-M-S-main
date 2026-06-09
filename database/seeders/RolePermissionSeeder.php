<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // permissions
        Permission::create(['name' => 'student_managment', 'guard_name' => 'api']);
        Permission::create(['name' => 'view-students', 'guard_name' => 'api']);
        Permission::create(['name' => 'teacher_managment', 'guard_name' => 'api']);
        Permission::create(['name' => 'salaries_managment', 'guard_name' => 'api']);
        Permission::create(['name' => 'schedule_managmet', 'guard_name' => 'api']);
        Permission::create(['name' => 'view_scheduale', 'guard_name' => 'api']);
        Permission::create(['name' => 'attendance', 'guard_name' => 'api']);
        Permission::create(['name' => 'send_student_notifications', 'guard_name' => 'api']);
        Permission::create(['name' => 'send_teacher_notifications', 'guard_name' => 'api']);
        Permission::create(['name' => 'view_notifications', 'guard_name' => 'api']);
        Permission::create(['name' => 'book_managment', 'guard_name' => 'api']);
        Permission::create(['name' => 'download_books', 'guard_name' => 'api']);
        Permission::create(['name' => 'mark_managment', 'guard_name' => 'api']);
        Permission::create(['name' => 'assessment_managment', 'guard_name' => 'api']);
        Permission::create(['name' => 'invoice_managment', 'guard_name' => 'api']);
        Permission::create(['name' => 'view_invoice', 'guard_name' => 'api']);
        Permission::create(['name' => 'manage-users', 'guard_name' => 'api']);
        Permission::create(['name' => 'view_school_reports', 'guard_name' => 'api']);
        Permission::create(['name' => 'view_student_reports', 'guard_name' => 'api']);
        Permission::create(['name' => 'super_admin', 'guard_name' => 'api']);

        // roles
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $super_admin = Role::create(['name' => 'super_admin', 'guard_name' => 'api']);
        $teacher = Role::create(['name' => 'teacher', 'guard_name' => 'api']);
        $student = Role::create(['name' => 'student', 'guard_name' => 'api']);

        //assign all permissions to admin who is super admin
        $super_admin->givePermissionTo(Permission::all());
        // assign permissions
        // $admin->givePermissionTo(Permission::all());

        $teacher->givePermissionTo([
            'view-students',
            'view_scheduale',
            'send_student_notifications',
            'view_notifications',
            'download_books',
            'mark_managment',
            'assessment_managment',
        ]);

        $student->givePermissionTo([
            'view_scheduale',
            'view_notifications',
            'download_books',
            'view_invoice',
        ]);


        $admin->givePermissionTo([
           'student_managment',
           'send_student_notifications',
           'send_teacher_notifications',
           'view_notifications',
        ]);
    }
}
