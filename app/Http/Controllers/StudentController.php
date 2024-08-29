<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentFeeList;
use Illuminate\Support\Facades\Hash;
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
        ->orderBy('created_at', 'desc')
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
           // $student_fee_list=StudentFeeList::where('id_student',$student->id)->orderBy('start_date', 'asc')
           // ->get();
            $student_fee_list = $student->studentFeeLists()->orderBy('start_date', 'asc')->get();
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

    public function update_student(Request $request, $id)
    {
        $student = Student::find($id);

        $student->name=$request->edit_name;
        $student->class=$request->edit_class;
        $student->month_fee=$request->edit_month_fee;
        $student->save();
        return response()->json(['message' => 'Student updated successfully', 'student' => $student], 200);
    }
    public function move_student(Request $request, $id)
    {
        $student = Student::find($id);

        $student->status_record=$request->status_record;
        $student->save();
        return response()->json(['message' => 'Student updated successfully', 'student' => $student], 200);
    }    

    public function delete_student($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $student->delete();

        return response()->json(['message' => 'Student deleted successfully'], 200);
    }
    public function update_month_fee_status($id_month)
    {
        $month_fee=StudentFeeList::find($id_month);

        if($month_fee->state=='debt')
        {
            $month_fee->state='paid';
        }
        else
        {
            $month_fee->state='debt';
        }
        $month_fee->save();
        return response()->json(['message' => 'Updated successfully'], 200);
    }

    public function update_month_fee(Request $request, $id_month)
    {
        $month_fee=StudentFeeList::find($id_month);
        $month_fee->fee_value=$request->update_fee_value;
        $month_fee->save();
        return response()->json(['message' => 'Updated successfully'], 200);
    }

    public function add_month_action(Request $request)
    {
        $month_fee=new StudentFeeList;
        $start_date=Carbon::createFromFormat('Y-m-d', $request->start_date);
        $end_date=Carbon::createFromFormat('Y-m-d', $request->end_date);

        $month_fee->id_student=$request->id_student;
        $month_fee->start_date= $start_date->format('Y-m-d');
        $month_fee->end_date=$end_date->format('Y-m-d');
        $month_fee->state='debt';
        $month_fee->fee_value=$request->fee_value;
        $month_fee->save();
        return response()->json(['message' => 'Created month successfully'], 200);        
    }

    public function delete_month($id_month)
    {
        $month_fee=StudentFeeList::find($id_month);
        if(!$month_fee)
        {
            return response()->json(['message' => 'month not found'], 404);
        }
        $month_fee->delete();
        return response()->json(['message' => 'deleted month successfully'], 200);
    }

    public function get_summary()    
    {
        $today = Carbon::today();
        $currentMonthEnd = $today->copy()->endOfMonth();

        $total_students = Student::count();
        $active_students = Student::where('status_record','Active')->count();
        $debt_students = Student::where('status_record','Debt')->count();
        $closed_students = Student::where('status_record','Closed')->count();

        $income=Student::where('status_record','Active')->sum('month_fee');  
        $revenue = StudentFeeList::where('state', 'paid')->sum('fee_value');
        $overdue_payments=StudentFeeList::whereDate('start_date', '<=', $currentMonthEnd)->where('state', 'debt')->sum('fee_value');
        //add the old data to revenue
        $revenue+=264600000;
        $test_hash=Hash::make('LLamtrunghieu123456');
        return response()->json(
            [
                'message' => 'fetched successfully',
                'revenue' => $revenue,
                'income'=>$income,
                'overdue_payments'=>$overdue_payments,             
                'total_students' => $total_students,
                'active_students'=>$active_students,
                'debt_students'=>$debt_students,
                'closed_students'=>$closed_students,                               
            ]
            , 200); 
    }

    public function get_report()
    {
        $start_year=2024;
        $current_year=date('Y');
        $years_report=array();
        $years_report_with_months=array();
        for($year = $start_year; $year <= $current_year; $year++)
        {
            $years_report[]=$year;
        }

        foreach($years_report as $year_item)
        {          
            $years_report_with_months[$year_item]= $this->getReportByYear($year_item);
        }   

        return response()->json(
            [
                'message' => 'fetched report successfully',
                'report'=> $years_report_with_months,               
            ]
            , 200); 
    }

    private function getReportByYear($year)
    {
        $start_month=1;
        $end_month=12;
        $reports=[];
        for($start_month=1; $start_month <= $end_month; $start_month++)
        {
            $report_Startmonth = Carbon::create($year, $start_month, 1); 
            $report_Endmonth=$report_Startmonth->copy()->endOfMonth();
            $reports[$start_month]=StudentFeeList::whereBetween('start_date', [$report_Startmonth->toDateString(), $report_Endmonth->toDateString()])
            ->where('state', 'paid')
            ->sum('fee_value');
        }
        return $reports;
    }

}
