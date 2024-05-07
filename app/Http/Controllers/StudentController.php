<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    
    public function index()
    {
        $students = Student::latest()->paginate(10);
        return [
            "status" => 1,
            "student_list" => $students
        ];
    }

    public function show(Student $student)
    {       
        return [
            "status" => 1,
            "data" =>$student
        ];
        
    }

    public function addstudent(Request $request)
    {
        $student = new Student;
        $student->name=$request->studentName;
        $student->class=$request->selected_class;
        $student->month_fee=$request->fee;
        $student->start_date=$request->start_date;
        $student->status_record='Active';

        $student->save();
        return [
            'success'=>true,
            'status_code' => 200,
            'return_data' => $student, 
            'message'=>'Add student successfully'
        ];      
    }

    public function testauth(Student $student)
    {
        return [
            "status" => 1,
            "data" =>'call dc ne'
        ];
    }
    
}
