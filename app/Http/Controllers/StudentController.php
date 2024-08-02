<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentFeeList;
use Carbon\Carbon;


class StudentController extends Controller
{
    
    public function get_all_student($status_record)
    {
        $now = Carbon::now();

        $student_list = Student::with(['studentFeeLists' => function($query) use ($now) {
            $query->whereDate('start_date', '<=', $now)
                  ->where('state', 'debt');
        }])
        ->where('status_record', $status_record)
        ->get();
    
        // Add unpaid_month attribute for each student
        $student_list->transform(function ($student) {
            $student['unpaid_month'] = $student->studentFeeLists->count();
            return $student;
        });
        return [            
            "return_data" => $student_list,
            'success'=>true,
            'status_code' => 200,                       
        ];
    }

    public function get_student($id)
    {
        $student = Student::find($id);        

        if(!$student)
        {
            return [
                'success'=>true,
                'status_code' => 200,                
                'message'=>'not found'
            ];      
        }
        else
        {
            $student_fee_list=StudentFeeList::where('id_student',$student->id)->get();
            $return_data['student']=$student;
            $return_data['fee_list']=$student_fee_list;
            return [
                'success'=>true,
                'status_code' => 200,
                'return_data' => $return_data, 
                'message'=>'fetch student successfully'
            ];      
        }       
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

        $fee_collection=[];
        $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date);

        for ($i = 0; $i < 12; $i++) {
            $endDate = $startDate->copy()->addMonth();
            $fee_collection[] = [
                'id_student'=>$student->id,
                'fee_value'=>$request->fee,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'state'=>'debt',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $startDate = $endDate; // Move to the next start date
          
        }
        StudentFeeList::insert($fee_collection);


        return [
            'success'=>true,
            'status_code' => 200,
            'return_data' => $student, 
            'message'=>'Add student successfully'
        ];      
    }

    public function move_student(Request $request, $id)
    {
        $student = Student::find($id);

        $student->status_record=$request->status_record;
        $student->save();
        return response()->json(['message' => 'Student updated successfully', 'student' => $student], 200);
    }    
}
