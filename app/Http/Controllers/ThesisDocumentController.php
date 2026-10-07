<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Thesis;
use App\Models\ThesisDocument;
use App\Models\ThesisDocumentVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ThesisDocumentController extends Controller
{
    public function store(Request $request, Thesis $thesis): RedirectResponse
    {
        Gate::authorize('update', $thesis);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'string', 'max:100'],
            'file' => ['required', 'file', 'mimes:pdf,docx', 'max:20480'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $path = $request->file('file')->store('theses/'.$thesis->id.'/documents');
        $document = $thesis->documents()->create([
            'uploaded_by' => $request->user()->id,
            'name' => $validated['name'],
            'document_type' => $validated['document_type'],
            'current_version' => '1.0',
        ]);

        $document->versions()->create([
            'uploaded_by' => $request->user()->id,
            'version' => '1.0',
            'file_path' => $path,
            'file_size' => $request->file('file')->getSize(),
            'notes' => $validated['notes'] ?? null,
        ]);
        ActivityLog::record('document.uploaded', $thesis, ['document_id' => $document->id, 'version' => '1.0']);

        return back()->with('status', 'Document uploaded successfully.');
    }

    public function storeVersion(Request $request, Thesis $thesis, ThesisDocument $document): RedirectResponse
    {
        Gate::authorize('update', $thesis);

        abort_unless($document->thesis_id === $thesis->id, 404);

        $validated = $request->validate([
            'version' => ['required', 'string', 'max:20'],
            'file' => ['required', 'file', 'mimes:pdf,docx', 'max:20480'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $path = $request->file('file')->store('theses/'.$thesis->id.'/documents');
        $document->versions()->create([
            'uploaded_by' => $request->user()->id,
            'version' => $validated['version'],
            'file_path' => $path,
            'file_size' => $request->file('file')->getSize(),
            'notes' => $validated['notes'] ?? null,
        ]);
        $document->update(['current_version' => $validated['version']]);
        ActivityLog::record('document.version_uploaded', $thesis, ['document_id' => $document->id, 'version' => $validated['version']]);

        return back()->with('status', 'Document version uploaded successfully.');
    }

    public function download(Thesis $thesis, ThesisDocumentVersion $version): StreamedResponse
    {
        Gate::authorize('view', $thesis);

        abort_unless($version->document->thesis_id === $thesis->id, 404);

        return Storage::download($version->file_path, $version->document->name.'-'.$version->version.'.'.pathinfo($version->file_path, PATHINFO_EXTENSION));
    }
}
