<?php

use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// Route::get('/', function () {
//     return view('welcome');
// });

// Custom download route for media files to force download instead of opening in browser
Route::get('/media/{media}/download', function (Media $media) {
    return response()->download($media->getPath(), $media->file_name, [
        'Content-Type' => $media->mime_type,
        'Content-Disposition' => 'attachment; filename="'.$media->file_name.'"',
    ]);
})->name('media.download');
