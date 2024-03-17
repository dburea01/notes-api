<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Repositories\NoteRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class NoteController extends Controller
{
    private NoteRepository $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $notes = $this->noteRepository->getNotes($request->all());

        return NoteResource::collection($notes);
    }

    public function store(StoreNoteRequest $request): NoteResource | RedirectResponse
    {
        try {
            $note = $this->noteRepository->insert($request->all());

            return new NoteResource($note);
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Note $note): void
    {
        //
    }

    public function update(UpdateNoteRequest $request, Note $note): NoteResource | RedirectResponse
    {
        try {
            $note = $this->noteRepository->update($note, $request->all());

            return new NoteResource($note);
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Note $note): Response | RedirectResponse
    {
        try {
            $this->noteRepository->delete($note);

            return response()->noContent();
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
