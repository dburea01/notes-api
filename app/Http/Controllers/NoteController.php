<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\NoteRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class NoteController extends Controller
{
    private NoteRepository $noteRepository;

    use AuthorizesRequests;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    /**
     * Get the list of notes for an organization.
     *
     * The list can be filtered by name
     */
    public function index(Request $request, Organization $organization): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Note::class, $organization]);
        $notes = $this->noteRepository->getNotes($organization, $request->all());

        return NoteResource::collection($notes);
    }

    /**
     * Store a new note
     */
    public function store(StoreNoteRequest $request, Organization $organization): NoteResource|JsonResponse
    {
        // see the authorization in the storeNoteRequest
        // $this->authorize('create', [Note::class, $organization]);

        try {
            /** @var User $user */
            $user = $request->user();

            $note = $this->noteRepository->insert($organization, $user, $request->all());

            return new NoteResource($note);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }

    /**
     * Display a note
     */
    public function show(Request $request, Organization $organization, Note $note): NoteResource
    {
        $this->authorize('view', [$note, $organization]);

        return new NoteResource($note);
    }

    /**
     * Update a note
     */
    public function update(UpdateNoteRequest $request, Organization $organization, Note $note): NoteResource|JsonResponse
    {
        // see the authorization in the storeNoteRequest
        // $this->authorize('update', [Note::class, $organization]);

        try {
            $note = $this->noteRepository->update($note, $request->all());

            return new NoteResource($note);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }

    /**
     * Delete a note
     */
    public function destroy(Request $request, Organization $organization, Note $note): Response|JsonResponse
    {
        $this->authorize('delete', [$note, $organization]);

        try {
            $this->noteRepository->delete($note);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }
}
