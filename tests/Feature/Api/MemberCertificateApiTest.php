<?php

namespace Tests\Feature\Api;

use App\Models\Household;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MemberCertificateApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_can_list_member_certificates(): void
    {
        $user = User::factory()->create();
        $household = Household::factory()->create();
        $member = Member::factory()->for($household)->create();
        
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/members/{$member->id}/certificates");

        $response->assertOk()
            ->assertJson([
                'baptism' => null,
                'first_communion' => null,
                'confirmation' => null,
            ]);
    }

    public function test_can_upload_certificate(): void
    {
        $household = Household::factory()->create();
        $member = Member::factory()->for($household)->create();
        
        Sanctum::actingAs($household);

        $file = UploadedFile::fake()->create('baptism_certificate.pdf', 100, 'application/pdf');

        $response = $this->postJson("/api/members/{$member->id}/certificates", [
            'certificate_type' => 'baptism',
            'file' => $file,
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'message',
                'certificate' => [
                    'id',
                    'name',
                    'file_name',
                    'mime_type',
                    'size',
                    'url',
                ],
            ]);

        $this->assertTrue($member->fresh()->hasMedia('baptism'));
    }

    public function test_can_download_certificate(): void
    {
        $household = Household::factory()->create();
        $member = Member::factory()->for($household)->create();
        
        // Add a certificate
        $file = UploadedFile::fake()->create('baptism_certificate.pdf', 100, 'application/pdf');
        $member->addMediaFromRequest('file')
            ->usingRequest(['file' => $file])
            ->toMediaCollection('baptism');

        Sanctum::actingAs($household);

        $response = $this->get("/api/members/{$member->id}/certificates/baptism/download");

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_can_show_certificate_details(): void
    {
        $household = Household::factory()->create();
        $member = Member::factory()->for($household)->create();
        
        // Add a certificate
        $file = UploadedFile::fake()->create('baptism_certificate.pdf', 100, 'application/pdf');
        $member->addMediaFromRequest('file')
            ->usingRequest(['file' => $file])
            ->toMediaCollection('baptism');

        Sanctum::actingAs($household);

        $response = $this->getJson("/api/members/{$member->id}/certificates/baptism");

        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'file_name',
                'mime_type',
                'size',
                'url',
            ]);
    }

    public function test_can_delete_certificate(): void
    {
        $household = Household::factory()->create();
        $member = Member::factory()->for($household)->create();
        
        // Add a certificate
        $file = UploadedFile::fake()->create('baptism_certificate.pdf', 100, 'application/pdf');
        $member->addMediaFromRequest('file')
            ->usingRequest(['file' => $file])
            ->toMediaCollection('baptism');

        Sanctum::actingAs($household);

        $response = $this->deleteJson("/api/members/{$member->id}/certificates/baptism");

        $response->assertOk()
            ->assertJson(['message' => 'Certificate deleted successfully']);

        $this->assertFalse($member->fresh()->hasMedia('baptism'));
    }

    public function test_upload_requires_authentication(): void
    {
        $member = Member::factory()->create();
        $file = UploadedFile::fake()->create('baptism_certificate.pdf', 100, 'application/pdf');

        $response = $this->postJson("/api/members/{$member->id}/certificates", [
            'certificate_type' => 'baptism',
            'file' => $file,
        ]);

        $response->assertUnauthorized();
    }

    public function test_upload_validates_certificate_type(): void
    {
        $household = Household::factory()->create();
        $member = Member::factory()->for($household)->create();
        
        Sanctum::actingAs($household);

        $file = UploadedFile::fake()->create('certificate.pdf', 100, 'application/pdf');

        $response = $this->postJson("/api/members/{$member->id}/certificates", [
            'certificate_type' => 'invalid_type',
            'file' => $file,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['certificate_type']);
    }

    public function test_upload_validates_file_type(): void
    {
        $household = Household::factory()->create();
        $member = Member::factory()->for($household)->create();
        
        Sanctum::actingAs($household);

        $file = UploadedFile::fake()->create('certificate.txt', 100, 'text/plain');

        $response = $this->postJson("/api/members/{$member->id}/certificates", [
            'certificate_type' => 'baptism',
            'file' => $file,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['file']);
    }

    public function test_returns_404_for_missing_certificate(): void
    {
        $household = Household::factory()->create();
        $member = Member::factory()->for($household)->create();
        
        Sanctum::actingAs($household);

        $response = $this->getJson("/api/members/{$member->id}/certificates/baptism");

        $response->assertNotFound()
            ->assertJson(['message' => 'Certificate not found']);
    }
}
