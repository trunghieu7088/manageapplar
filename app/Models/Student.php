<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;



class Student extends Model
{
    use HasFactory, HasApiTokens;

    protected $appends = ['full_name_with_class','start_date_formatted'];

    protected function fullNameWithClass(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name . ' (' . $this->class . ')',
        );
    }

    protected function startDateFormatted(): Attribute
    {

        return Attribute::make(
            get: fn () =>  Carbon::parse($this->start_date)->format('d - m - Y'),
        );
    }

    public function studentFeeLists(): HasMany
    {
        return $this->hasMany(StudentFeeList::class, 'id_student');
    }

    protected static function booted(): void
    {
        static::updated(function (Student $student) {
            if ($student->isDirty('month_fee')) {
                // Update the fee_value in the related StudentFeeList records
                $student->studentFeeLists()->where('state', 'debt')->update(['fee_value' => $student->month_fee]);
            }
        });
    }
    
}
