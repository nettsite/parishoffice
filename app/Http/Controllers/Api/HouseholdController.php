<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Household;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class HouseholdController extends Controller
{
    private function logActivity(string $action, array $context = [])
    {
        if ($user = request()->user()) {
            $context['user_id'] = $user->id;
        }
        $context['ip'] = request()->ip();
        $context['user_agent'] = request()->userAgent();
        
        Log::debug("[Household] {$action}", $context);
    }

    /**
     * Get certificate information for a member
     */
    private function getMemberCertificateInfo($member, $certificateType)
    {
        $collectionName = $certificateType . '_certificates';
        $media = $member->getFirstMedia($collectionName);

        if (!$media) {
            return null;
        }

        return [
            'media_id' => $media->id,
            'file_name' => $media->file_name,
            'url' => url("api/members/{$member->id}/certificates/{$certificateType}/download"),
            'size' => $media->size,
            'mime_type' => $media->mime_type,
            'uploaded_at' => $media->created_at,
        ];
    }

    public function show(Request $request)
    {
        $household = $request->user();
        
        $this->logActivity('Viewing household details', [
            'household_id' => $household->id
        ]);

        $household->load(['members' => function($query) {
            $query->with('media');
        }]);

        // Add certificate information to each member
        $household->members->each(function($member) {
            $member->certificates = [
                'baptism' => $this->getMemberCertificateInfo($member, 'baptism'),
                'first_communion' => $this->getMemberCertificateInfo($member, 'first_communion'),
                'confirmation' => $this->getMemberCertificateInfo($member, 'confirmation'),
            ];
        });

        $this->logActivity('Household details retrieved', [
            'household_id' => $household->id,
            'member_count' => $household->members->count()
        ]);

        return response()->json([
            'household' => $household,
        ]);
    }

    public function update(Request $request)
    {
        $household = $request->user();
        
        $this->logActivity('Starting household update', [
            'household_id' => $household->id,
            'request_data' => $request->except(['password'])
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'province' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'phone' => 'nullable|string|max:20',
                'mobile' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
            ]);

            $this->logActivity('Update validation passed', [
                'household_id' => $household->id,
                'fields_to_update' => array_keys($validated)
            ]);

            $household->update($validated);

            $this->logActivity('Household updated successfully', [
                'household_id' => $household->id
            ]);

            return response()->json([
                'household' => $household,
            ]);
        } catch (\Exception $e) {
            $this->logActivity('Household update failed', [
                'household_id' => $household->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function destroy(Request $request)
    {
        $household = $request->user();
        
        $this->logActivity('Starting household deletion', [
            'household_id' => $household->id
        ]);

        try {
            $household->delete();
            
            $this->logActivity('Household deleted successfully', [
                'household_id' => $household->id
            ]);

            return response()->noContent();
        } catch (\Exception $e) {
            $this->logActivity('Household deletion failed', [
                'household_id' => $household->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function members(Request $request)
    {
        $household = $request->user();
        
        $this->logActivity('Retrieving household members', [
            'household_id' => $household->id
        ]);

        try {
            $members = $household->members()->with('media')->get();

            // Add certificate information to each member
            $members->each(function($member) {
                $member->certificates = [
                    'baptism' => $this->getMemberCertificateInfo($member, 'baptism'),
                    'first_communion' => $this->getMemberCertificateInfo($member, 'first_communion'),
                    'confirmation' => $this->getMemberCertificateInfo($member, 'confirmation'),
                ];
            });

            $this->logActivity('Members retrieved successfully', [
                'household_id' => $household->id,
                'member_count' => $members->count()
            ]);

            return response()->json([
                'members' => $members,
            ]);
        } catch (\Exception $e) {
            $this->logActivity('Failed to retrieve members', [
                'household_id' => $household->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
