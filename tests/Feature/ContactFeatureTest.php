<?php

use App\Models\Contact;
use Illuminate\Support\Arr;


use function Pest\Laravel\postJson;
use function Pest\Laravel\get;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('creates a contact via API', function () {
    $payload = Contact::factory()->make()->toArray();

    postJson(route('contacts.store'), $payload)
        ->assertStatus(200)
        ->assertJson(['status' => 'success']);

    $this->assertDatabaseHas('contacts', ['email' => $payload['email']]);
});

it('downloads contacts as csv', function () {
    Contact::factory()->count(3)->create();

    get(route('contacts.export', 'csv'))
        ->assertStatus(200);
});
