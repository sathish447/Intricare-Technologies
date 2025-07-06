@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Contact Management</h1>
        <button id="addContactBtn" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">Add Contact</button>
    </div>

    <!-- Search Filters -->
    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="text" id="searchName" class="border p-2" placeholder="Search by Name">
        <input type="text" id="searchEmail" class="border p-2" placeholder="Search by Email">
        <select id="filterGender" class="border p-2">
            <option value="">All Genders</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>
        <select id="filterField" class="border p-2">
            <option value="">Any Custom Field</option>
            @foreach($customFields as $f)
              <option value="{{ $f->name }}">{{ $f->name }}</option>
            @endforeach
        </select>
        <input type="text" id="filterFv" class="border p-2" placeholder="Custom value">
    </div>

    <!-- Contact List -->
    <div id="contactList">
        @include('contacts.partials.list', ['contacts' => $contacts])
    </div>
</div>

<!-- Modal -->
<div id="contactModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
    <div class="bg-white p-6 rounded-lg w-full max-w-xl max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-semibold mb-4" id="modalTitle">Add Contact</h2>
        <form id="contactForm">
            @csrf
            <input type="hidden" name="contact_id" id="contact_id">

            <div class="mb-4">
                <label class="block mb-1 font-medium">Name</label>
                <input type="text" name="name" id="name" class="w-full border px-3 py-2 rounded" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Email</label>
                <input type="email" name="email" id="email" class="w-full border px-3 py-2 rounded" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Phone</label>
                <input type="text" name="phone" id="phone" class="w-full border px-3 py-2 rounded" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Gender</label>
                <div class="flex items-center gap-6 mt-1">
                     <label class="inline-flex items-center gap-1"><input type="radio" name="gender" value="male"> Male</label>
                     <label class="inline-flex items-center gap-1"><input type="radio" name="gender" value="female"> Female</label>
                 </div>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Profile Image</label>
                <input type="file" name="profile_image" id="profile_image" class="w-full">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Additional File</label>
                <input type="file" name="additional_file" id="additional_file" class="w-full">
            </div>

            <!-- Dynamic Custom Fields -->
            <div id="customFields">
                @foreach($customFields as $field)
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">{{ $field->name }}</label>
                        <input type="{{ $field->type }}" name="custom[{{ $field->id }}]" class="w-full border px-3 py-2 rounded">
                    </div>
                @endforeach
            </div>

            <div class="mt-4 text-right">
                <button type="button" class="bg-gray-300 px-4 py-2 rounded" onclick="closeModal()">Cancel</button>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showMsg(msg,color='green'){
        const div=document.createElement('div');
        div.className=`fixed top-4 right-4 bg-${color}-600 text-white px-4 py-2 rounded shadow`;
        div.innerText=msg;
        document.body.appendChild(div);
        setTimeout(()=>div.remove(),3500);
    }

    const contactModal = document.getElementById('contactModal');
    const addBtn = document.getElementById('addContactBtn');

    addBtn.addEventListener('click', () => {
        document.getElementById('contactForm').reset();
        document.getElementById('contact_id').value = '';
        document.getElementById('modalTitle').innerText = 'Add Contact';
        contactModal.classList.remove('hidden');
    });

    function closeModal() {
        contactModal.classList.add('hidden');
    }

    document.getElementById('searchName').addEventListener('input', filterContacts);
    document.getElementById('searchEmail').addEventListener('input', filterContacts);
    document.getElementById('filterGender').addEventListener('change', filterContacts);
document.getElementById('filterField').addEventListener('change', filterContacts);
document.getElementById('filterFv').addEventListener('input', filterContacts);

    function filterContacts() {
        const qs = new URLSearchParams({name:searchName.value,email:searchEmail.value,gender:filterGender.value,field:filterField.value,fv:filterFv.value}).toString();
        fetch(`/contacts?${qs}`, {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
            .then(res => res.text())
            .then(html => document.getElementById('contactList').innerHTML = html);
    }

    // delegate edit, delete, merge buttons since rows are re-rendered
    document.addEventListener('click', function(e){
        // delete
        if(e.target.classList.contains('btn-delete')){
            if(!confirm('Delete this contact?')) return;
            fetch(e.target.dataset.url, {
                headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
                method:'DELETE',
                headers:{'X-CSRF-TOKEN':document.querySelector('input[name="_token"]').value}
            }).then(r=>r.json()).then(d=>{if(d.status==='success'){showMsg(d.message);filterContacts();}});
        }
        // edit
        if(e.target.classList.contains('btn-edit')){
            const id = e.target.dataset.id;
            fetch(`/contacts/${id}`, {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
              .then(r=>r.json())
              .then(c=>{
                  document.getElementById('modalTitle').innerText = 'Edit Contact';
                  document.getElementById('contact_id').value = c.id;
                  document.getElementById('name').value = c.name;
                  document.getElementById('email').value = c.email;
                  document.getElementById('phone').value = c.phone;
                  document.querySelectorAll('input[name="gender"]').forEach(r=>r.checked=r.value===c.gender);
                  if(c.custom_values){
                      c.custom_values.forEach(v=>{
                        const inp=document.querySelector(`[name=\"custom[${v.custom_field_id}]\"]`);
                        if(inp) inp.value=v.value;
                      });
                  }
                  contactModal.classList.remove('hidden');
              });
        }
    });

    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        let contactId = formData.get('contact_id');
        let method = 'POST';
        if(contactId){
            formData.append('_method','PUT');
        }
        let url = contactId ? `/contacts/${contactId}` : '/contacts';

        fetch(url, {
            headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('input[name="_token"]').value},
            method: method,
            body: formData
        })
        .then(async response => {
            if(response.status===422){
                const d=await response.json();
                const errs=Object.values(d.errors).flat().join('\n');
                showMsg(errs,'red');
                throw new Error('validation');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                showMsg(data.message);
                closeModal();
                filterContacts();
            }
        });
    });
/* ---------- merge UI ---------- */
const mergeModal=document.createElement('div');
mergeModal.id='mergeModal';
mergeModal.className='fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden';
mergeModal.innerHTML=`<div class=\"bg-white p-6 rounded-lg w-full max-w-md\">
 <h2 class=\"text-xl font-semibold mb-4\">Merge Contacts</h2>
 <p class=\"mb-2\">Choose master contact:</p>
 <select id=\"masterSelect\" class=\"w-full border p-2 mb-4\"></select>
 <div class=\"text-right space-x-2\">
   <button id=\"mergeCancel\" class=\"px-4 py-2 bg-gray-300 rounded\">Cancel</button>
   <button id=\"mergeConfirm\" class=\"px-4 py-2 bg-blue-600 text-white rounded\">Merge</button>
 </div>
</div>`;
document.body.appendChild(mergeModal);
const masterSelect=document.getElementById('masterSelect');
let secondaryId=null;

document.addEventListener('click',e=>{
 if(e.target.classList.contains('btn-merge')){
   secondaryId=e.target.dataset.id;
   masterSelect.innerHTML='';
   document.querySelectorAll('#contactList tr').forEach(tr=>{
     const id=tr.querySelector('.btn-edit')?.dataset.id;
     const name=tr.children[1]?.innerText;
     if(id && id!==secondaryId){ masterSelect.innerHTML+=`<option value=\"${id}\">${name}</option>`; }
   });
   mergeModal.classList.remove('hidden');
 }
});

document.getElementById('mergeCancel').onclick=()=>mergeModal.classList.add('hidden');
document.getElementById('mergeConfirm').onclick=()=>{
   fetch('{{ route('contacts.merge') }}',{
       method:'POST',
       headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('input[name="_token"]').value},
       body:JSON.stringify({master_id:masterSelect.value,secondary_id:secondaryId})
   }).then(r=>r.json()).then(d=>{if(d.status==='success'){showMsg(d.message);mergeModal.classList.add('hidden');filterContacts();}});
};
</script>
@endsection
