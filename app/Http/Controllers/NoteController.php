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

    public function index(Request $request, Organization $organization): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Note::class, $organization]);
        $notes = $this->noteRepository->getNotes($organization, $request->all());

        return NoteResource::collection($notes);
    }

    public function store(StoreNoteRequest $request, Organization $organization): NoteResource|JsonResponse
    {
        $this->authorize('create', [Note::class, $organization]);

        try {
            /** @var User $user */
            $user = $request->user();

            $note = $this->noteRepository->insert($organization, $user, $request->all());

            return new NoteResource($note);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }

    public function show(Request $request, Organization $organization, Note $note): NoteResource
    {
        $this->authorize('view', [$note, $organization]);

        return new NoteResource($note);
    }

    public function update(UpdateNoteRequest $request, Organization $organization, Note $note): NoteResource|JsonResponse
    {
        $this->authorize('update', [Note::class, $organization]);

        try {
            $note = $this->noteRepository->update($note, $request->all());

            return new NoteResource($note);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }

    public function destroy(Request $request, Organization $organization, Note $note): Response|JsonResponse
    {
        $this->authorize('delete', [Note::class, $organization]);

        try {
            $this->noteRepository->delete($note);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }
}
