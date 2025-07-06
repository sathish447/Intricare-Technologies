<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContactExport;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $q = Contact::query();
    
        // Standard filters
        if ($req->filled('name'))   $q->where('name','like',"%{$req->name}%");
        if ($req->filled('email'))  $q->where('email','like',"%{$req->email}%");
        if ($req->filled('gender')) $q->where('gender',$req->gender);
    
        // Optional filter by custom field
        if ($req->filled('field') && $req->filled('fv')) {
            $q->whereHas('customValues', function($sub) use ($req){
                $sub->whereHas('field', fn($qq)=>$qq->where('name',$req->field))
                    ->where('value','like',"%{$req->fv}%");
            });
        }
    
        $contacts = $q->latest()->paginate(10);
    
        if ($req->ajax()) {
            return view('contacts.partials.table', compact('contacts'))->render();
        }

        $customFields = CustomField::all();
        return view('contacts.index', compact('contacts','customFields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreContactRequest $r)
    {
        $data = $r->except('_token','custom');
        // handle files
        if($r->hasFile('profile_image')){
            $data['profile_image'] = $r->file('profile_image')->store('profiles');
        }
        if($r->hasFile('additional_file')){
            $data['additional_file'] = $r->file('additional_file')->store('files');
        }
        $contact = Contact::create($data);
        $this->saveCustom($contact,$r->custom ?? []);
        return response()->json(['status'=>'success','message'=>'Contact created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        $contact->load('customValues.field');
        return response()->json($contact);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateContactRequest $r, Contact $contact)
    {
        $data = $r->except('_token','custom');
        if($r->hasFile('profile_image')){
            // delete old
            if($contact->profile_image) Storage::delete($contact->profile_image);
            $data['profile_image'] = $r->file('profile_image')->store('profiles');
        }
        if($r->hasFile('additional_file')){
            if($contact->additional_file) Storage::delete($contact->additional_file);
            $data['additional_file'] = $r->file('additional_file')->store('files');
        }
        $contact->update($data);
        $this->saveCustom($contact,$r->custom ?? []);
        return response()->json(['status'=>'success','message'=>'Contact updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json(['status'=>'success','message'=>'Contact deleted successfully']);
    }

    public function merge(Request $r)
    {
        $master    = Contact::findOrFail($r->master_id);
        $secondary = Contact::findOrFail($r->secondary_id);

        // merge standard data (emails / phones)
        foreach (['email','phone'] as $field){
            if($secondary->$field && $secondary->$field !== $master->$field){
                $master->$field = $master->$field ? $master->$field.','.$secondary->$field : $secondary->$field;
            }
        }
        $master->save();

        // merge custom fields
        foreach ($secondary->customValues as $cv){
            $exists = $master->customValues()->where('custom_field_id',$cv->custom_field_id)->first();
            if(!$exists){
                $cv->contact_id = $master->id; $cv->save();
            }elseif($exists->value !== $cv->value){
                $exists->value .= ' | '.$cv->value; $exists->save();
            }
        }

        $secondary->delete();
        return response()->json(['status'=>'success','message'=>'Contacts merged']);
    }

    public function export(string $type)
    {
        abort_unless(in_array($type,['csv','xlsx']),404);
        $file = 'contacts_'.now()->format('Ymd_His').'.'.$type;
        return Excel::download(new ContactExport, $file);
    }

    private function saveCustom(Contact $contact, array $pairs){
        foreach ($pairs as $fieldId=>$value) {
            $contact->customValues()->updateOrCreate(
                ['custom_field_id'=>$fieldId],
                ['value'=>$value]
            );
        }
    }
}
