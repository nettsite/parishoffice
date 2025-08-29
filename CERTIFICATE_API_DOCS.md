# Member Certificates API Documentation

## Authentication
All certificate endpoints require authentication using Laravel Sanctum tokens. Include the token in the Authorization header:
```
Authorization: Bearer YOUR_TOKEN_HERE
```

## Base URL
All endpoints are prefixed with `/api/`

## Endpoints

### 1. List All Certificates for a Member
**GET** `/api/members/{member_id}/certificates`

Returns all certificates (baptism, first communion, confirmation) for a specific member.

**Response:**
```json
{
  "success": true,
  "data": {
    "baptism": {
      "media_id": 1,
      "file_name": "01K3TWP85FSWNPNC5QWXH33E3P.jpeg",
      "original_name": "baptism_cert.jpg",
      "size": 82367,
      "mime_type": "image/jpeg",
      "collection": "baptism_certificates",
      "url": "http://matthew/storage/1/01K3TWP85FSWNPNC5QWXH33E3P.jpeg",
      "uploaded_at": "2025-08-29T14:26:00.000000Z"
    },
    "first_communion": null,
    "confirmation": null
  }
}
```

### 2. Upload a Certificate
**POST** `/api/members/{member_id}/certificates`

Upload a certificate file for a member.

**Parameters:**
- `certificate_type` (required): One of `baptism`, `first_communion`, or `confirmation`
- `file` (required): The certificate file (PDF, JPEG, JPG, PNG, GIF, WebP, max 10MB)

**Example using cURL:**
```bash
curl -X POST http://matthew/api/members/1/certificates \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "certificate_type=baptism" \
  -F "file=@/path/to/certificate.pdf"
```

**Response:**
```json
{
  "success": true,
  "message": "Certificate uploaded successfully",
  "data": {
    "media_id": 1,
    "file_name": "01K3TWP85FSWNPNC5QWXH33E3P.pdf",
    "original_name": "baptism_certificate.pdf",
    "size": 245760,
    "mime_type": "application/pdf",
    "collection": "baptism_certificates",
    "url": "http://matthew/storage/1/01K3TWP85FSWNPNC5QWXH33E3P.pdf"
  }
}
```

### 3. Get Certificate Information
**GET** `/api/members/{member_id}/certificates/{certificate_type}`

Get information about a specific certificate.

**Parameters:**
- `certificate_type`: One of `baptism`, `first_communion`, or `confirmation`

**Response:**
```json
{
  "success": true,
  "data": {
    "media_id": 1,
    "file_name": "01K3TWP85FSWNPNC5QWXH33E3P.pdf",
    "original_name": "baptism_certificate.pdf",
    "size": 245760,
    "mime_type": "application/pdf",
    "collection": "baptism_certificates",
    "url": "http://matthew/storage/1/01K3TWP85FSWNPNC5QWXH33E3P.pdf",
    "uploaded_at": "2025-08-29T14:26:00.000000Z"
  }
}
```

### 4. Download a Certificate
**GET** `/api/members/{member_id}/certificates/{certificate_type}/download`

Download a certificate file.

**Parameters:**
- `certificate_type`: One of `baptism`, `first_communion`, or `confirmation`

**Example using cURL:**
```bash
curl -X GET http://matthew/api/members/1/certificates/baptism/download \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o baptism_certificate.pdf
```

**Response:** Binary file download with proper headers for downloading.

### 5. Delete a Certificate
**DELETE** `/api/members/{member_id}/certificates/{certificate_type}`

Delete a certificate file.

**Parameters:**
- `certificate_type`: One of `baptism`, `first_communion`, or `confirmation`

**Response:**
```json
{
  "success": true,
  "message": "Certificate deleted successfully"
}
```

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "certificate_type": ["Certificate type is required."],
    "file": ["The certificate must be a PDF, JPEG, JPG, PNG, GIF, or WebP file."]
  }
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Certificate not found"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Failed to upload certificate",
  "error": "Detailed error message"
}
```

## WordPress Plugin Integration

For WordPress plugin integration, you'll typically:

1. **Authentication**: First authenticate to get a token
2. **Upload**: Use the upload endpoint to send certificate files
3. **Download**: Use the download endpoint to retrieve files
4. **List**: Use the list endpoint to show available certificates

### Example PHP Code for WordPress Plugin:

```php
// Upload a certificate
$response = wp_remote_post('http://matthew/api/members/1/certificates', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
    ],
    'body' => [
        'certificate_type' => 'baptism',
    ],
    'files' => [
        'file' => $file_path,
    ],
]);

// Download a certificate
$response = wp_remote_get('http://matthew/api/members/1/certificates/baptism/download', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
    ],
]);
```
