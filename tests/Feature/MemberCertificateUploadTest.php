<?php

namespace Tests\Feature;

use App\Filament\Resources\Members\Pages\CreateMember;
use App\Filament\Resources\Members\Pages\EditMember;
use App\Models\Household;
use App\Models\Member;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MemberCertificateUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set the current panel to the admin panel
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        // Create and authenticate a user
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_can_upload_baptism_certificate_when_creating_member(): void
    {
        Storage::fake('public');

        $household = Household::factory()->create();
        $file = UploadedFile::fake()->create('baptism_certificate.pdf', 1000, 'application/pdf');

        Livewire::test(CreateMember::class)
            ->fillForm([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'household_id' => $household->id,
                'baptised' => true,
                'baptism_date' => '2000-01-01',
                'baptism_parish' => 'St. Mary\'s Church',
                'baptism_certificate' => [$file],
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $member = Member::where('first_name', 'John')->where('last_name', 'Doe')->first();

        $this->assertNotNull($member);
        $this->assertTrue($member->baptised);
        $this->assertCount(1, $member->getMedia('baptism_certificates'));

        $media = $member->getFirstMedia('baptism_certificates');
        $this->assertEquals('baptism_certificate.pdf', $media->name);
        $this->assertEquals('application/pdf', $media->mime_type);
    }

    public function test_can_upload_first_communion_certificate_when_editing_member(): void
    {
        Storage::fake('public');

        $member = Member::factory()->create([
            'first_communion' => true,
            'first_communion_date' => '2010-05-15',
        ]);

        $file = UploadedFile::fake()->image('first_communion_certificate.jpg');

        Livewire::test(EditMember::class, ['record' => $member->getRouteKey()])
            ->fillForm([
                'first_communion_certificate' => [$file],
            ])
            ->call('save')
            ->assertNotified();

        $member->refresh();

        $this->assertCount(1, $member->getMedia('first_communion_certificates'));

        $media = $member->getFirstMedia('first_communion_certificates');
        $this->assertEquals('first_communion_certificate.jpg', $media->name);
        $this->assertEquals('image/jpeg', $media->mime_type);
    }

    public function test_can_upload_confirmation_certificate(): void
    {
        Storage::fake('public');

        $member = Member::factory()->create([
            'confirmed' => true,
            'confirmation_date' => '2015-04-20',
        ]);

        $file = UploadedFile::fake()->create('confirmation_certificate.pdf', 2000, 'application/pdf');

        Livewire::test(EditMember::class, ['record' => $member->getRouteKey()])
            ->fillForm([
                'confirmation_certificate' => [$file],
            ])
            ->call('save')
            ->assertNotified();

        $member->refresh();

        $this->assertCount(1, $member->getMedia('confirmation_certificates'));

        $media = $member->getFirstMedia('confirmation_certificates');
        $this->assertEquals('confirmation_certificate.pdf', $media->name);
        $this->assertEquals('application/pdf', $media->mime_type);
    }

    public function test_accepts_multiple_file_types_for_certificates(): void
    {
        Storage::fake('public');

        $member = Member::factory()->create(['baptised' => true]);

        // Test PDF
        $pdfFile = UploadedFile::fake()->create('certificate.pdf', 1000, 'application/pdf');
        $member->addMediaFromString(file_get_contents($pdfFile->getRealPath()))
            ->usingName('certificate.pdf')
            ->usingFileName('certificate.pdf')
            ->toMediaCollection('baptism_certificates');

        // Test Image
        $member2 = Member::factory()->create(['first_communion' => true]);
        $imageFile = UploadedFile::fake()->image('certificate.png');
        $member2->addMediaFromString(file_get_contents($imageFile->getRealPath()))
            ->usingName('certificate.png')
            ->usingFileName('certificate.png')
            ->toMediaCollection('first_communion_certificates');

        $this->assertCount(1, $member->getMedia('baptism_certificates'));
        $this->assertCount(1, $member2->getMedia('first_communion_certificates'));
    }

    public function test_media_collections_only_allow_single_file(): void
    {
        Storage::fake('public');

        $member = Member::factory()->create(['baptised' => true]);

        // Add first file
        $file1 = UploadedFile::fake()->create('certificate1.pdf', 1000, 'application/pdf');
        $member->addMediaFromString(file_get_contents($file1->getRealPath()))
            ->usingName('certificate1.pdf')
            ->usingFileName('certificate1.pdf')
            ->toMediaCollection('baptism_certificates');

        // Add second file (should replace the first)
        $file2 = UploadedFile::fake()->create('certificate2.pdf', 1000, 'application/pdf');
        $member->addMediaFromString(file_get_contents($file2->getRealPath()))
            ->usingName('certificate2.pdf')
            ->usingFileName('certificate2.pdf')
            ->toMediaCollection('baptism_certificates');

        // Should only have one file (the latest one)
        $this->assertCount(1, $member->getMedia('baptism_certificates'));
        $this->assertEquals('certificate2.pdf', $member->getFirstMedia('baptism_certificates')->name);
    }
}
