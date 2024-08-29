<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;




class StudentFeeList extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'student_fee_list';

    protected $appends = ['end_date_formatted','start_date_formatted','need_pay'];

    protected function startDateFormatted(): Attribute
    {

        return Attribute::make(
            get: fn () =>  Carbon::parse($this->start_date)->format('d - m - Y'),
        );
    }

    protected function endDateFormatted(): Attribute
    {

        return Attribute::make(
            get: fn () =>  Carbon::parse($this->end_date)->format('d - m - Y'),
        );
    }
    protected function needPay(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->state === 'debt' && Carbon::parse($this->start_date)->isPast(),
        );
    }


}
