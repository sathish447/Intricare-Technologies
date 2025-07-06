<?php

namespace App\Exports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $contacts = Contact::with('customValues.field')->get();

        return $contacts->map(function ($c) {
            $row = $c->only(['name', 'email', 'phone', 'gender']);
            foreach ($c->customValues as $cv) {
                $row['cf_' . $cv->field->name] = $cv->value;
            }
            return $row;
        });
    }

    public function headings(): array
    {
        $base = ['name', 'email', 'phone', 'gender'];
        $first = Contact::with('customValues.field')->first();
        if ($first) {
            foreach ($first->customValues as $cv) {
                $base[] = 'cf_' . $cv->field->name;
            }
        }
        return $base;
    }
}
