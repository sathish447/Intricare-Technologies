<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model {
    use HasFactory, SoftDeletes;
    protected $fillable = ['name','email','phone','gender','profile_image','additional_file'];

    public function customValues() {
        return $this->hasMany(ContactCustomFieldValue::class);
    }
}