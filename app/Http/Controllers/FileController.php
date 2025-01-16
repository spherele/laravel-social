<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUpload\StoreFileUploadRequest;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function upload(StoreFileUploadRequest $request)
    {
        $file = $request->file('file');
        $path = $file->store('uploads', 'public');

        return response()->json([
            'file_url' => Storage::url($path),
            'file_type' => $file->getMimeType(),
        ]);
    }
}
