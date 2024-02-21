<?php

use App\Filament\Administration\Resources\InternshipAgreementResource;
use App\Models\InternshipAgreement;
use Illuminate\Foundation\Testing\RefreshDatabase;

// it('has internshipagreementresource page', function () {
//     $response = $this->get('/internshipagreementresource');

//     $response->assertStatus(200);
// });

// test('internshipagreementresource', function () {
//     expect(true)->toBeTrue();
// });

uses(RefreshDatabase::class);

it('returns the correct model label', function () {
    expect(InternshipAgreementResource::getModelLabel())->toBe(__('Internship Agreement'));
});

it('returns the correct plural model label', function () {
    expect(InternshipAgreementResource::getPluralModelLabel())->toBe(__('Internship Agreements'));
});

it('returns the correct globally searchable attributes', function () {
    expect(InternshipAgreementResource::getGloballySearchableAttributes())->toBe(['title', 'organization_name', 'student.full_name', 'id_pfe']);
});

it('returns the correct navigation badge', function () {
    InternshipAgreement::factory()->count(5)->create();
    expect(InternshipAgreementResource::getNavigationBadge())->toBe('5');
});
