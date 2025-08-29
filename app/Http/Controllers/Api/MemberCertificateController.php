<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UploadCertificateRequest;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MemberCertificateController extends Controller
{
    /**
     * Upload a certificate for a member
     */
    public function upload(UploadCertificateRequest $request, Member $member): JsonResponse
    {
        try {
            $certificateType = $request->input('certificate_type');
            $file = $request->file('file');

            // Determine the collection name based on certificate type
            $collectionName = $certificateType.'_certificates';

            // Add the file to the member's media collection
            $media = $member
                ->addMediaFromRequest('file')
                ->toMediaCollection($collectionName);

            return response()->json([
                'success' => true,
                'message' => 'Certificate uploaded successfully',
                'data' => [
                    'media_id' => $media->id,
                    'file_name' => $media->file_name,
                    'original_name' => $media->name,
                    'size' => $media->size,
                    'mime_type' => $media->mime_type,
                    'collection' => $media->collection_name,
                    'url' => $media->getUrl(),
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload certificate',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download a certificate
     */
    public function download(Member $member, string $certificateType): BinaryFileResponse|JsonResponse
    {
        try {
            // Validate certificate type
            if (! in_array($certificateType, ['baptism', 'first_communion', 'confirmation'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid certificate type',
                ], 400);
            }

            $collectionName = $certificateType.'_certificates';
            $media = $member->getFirstMedia($collectionName);

            if (! $media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found',
                ], 404);
            }

            if (! $media->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate file not found on disk',
                ], 404);
            }

            // Get the file path
            $filePath = $media->getPath();

            // Return the file as a download response
            return response()->download($filePath, $media->name, [
                'Content-Type' => $media->mime_type,
                'Content-Disposition' => 'attachment; filename="'.$media->name.'"',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download certificate',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get certificate information for a member
     */
    public function show(Member $member, string $certificateType): JsonResponse
    {
        try {
            // Validate certificate type
            if (! in_array($certificateType, ['baptism', 'first_communion', 'confirmation'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid certificate type',
                ], 400);
            }

            $collectionName = $certificateType.'_certificates';
            $media = $member->getFirstMedia($collectionName);

            if (! $media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'media_id' => $media->id,
                    'file_name' => $media->file_name,
                    'original_name' => $media->name,
                    'size' => $media->size,
                    'mime_type' => $media->mime_type,
                    'collection' => $media->collection_name,
                    'url' => $media->getUrl(),
                    'uploaded_at' => $media->created_at,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve certificate information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a certificate
     */
    public function destroy(Member $member, string $certificateType): JsonResponse
    {
        try {
            // Validate certificate type
            if (! in_array($certificateType, ['baptism', 'first_communion', 'confirmation'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid certificate type',
                ], 400);
            }

            $collectionName = $certificateType.'_certificates';
            $media = $member->getFirstMedia($collectionName);

            if (! $media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found',
                ], 404);
            }

            // Delete the media file
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Certificate deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete certificate',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all certificates for a member
     */
    public function index(Member $member): JsonResponse
    {
        try {
            $certificates = [];
            $types = ['baptism', 'first_communion', 'confirmation'];

            foreach ($types as $type) {
                $collectionName = $type.'_certificates';
                $media = $member->getFirstMedia($collectionName);

                $certificates[$type] = $media ? [
                    'media_id' => $media->id,
                    'file_name' => $media->file_name,
                    'original_name' => $media->name,
                    'size' => $media->size,
                    'mime_type' => $media->mime_type,
                    'collection' => $media->collection_name,
                    'url' => $media->getUrl(),
                    'uploaded_at' => $media->created_at,
                ] : null;
            }

            return response()->json([
                'success' => true,
                'data' => $certificates,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve certificates',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
