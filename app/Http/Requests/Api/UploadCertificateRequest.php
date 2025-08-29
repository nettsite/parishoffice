<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UploadCertificateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'certificate_type' => 'required|in:baptism,first_communion,confirmation',
            'file' => 'required|file|mimes:pdf,jpeg,jpg,png,gif,webp|max:10240', // 10MB max
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'certificate_type.required' => 'Certificate type is required.',
            'certificate_type.in' => 'Certificate type must be one of: baptism, first_communion, confirmation.',
            'file.required' => 'A certificate file is required.',
            'file.file' => 'The uploaded file is not valid.',
            'file.mimes' => 'The certificate must be a PDF, JPEG, JPG, PNG, GIF, or WebP file.',
            'file.max' => 'The certificate file size cannot exceed 10MB.',
        ];
    }
}
