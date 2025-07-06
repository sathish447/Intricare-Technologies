<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model {
    use HasFactory;
    use HasFactory;
    protected $fillable = ['name','type'];

    public function values() {
        return $this->hasMany(ContactCustomFieldValue::class);
    }
}