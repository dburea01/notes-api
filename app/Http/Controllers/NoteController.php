<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\NoteRepository;
use Illuminate\Http\JsonResponse;
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

    public function index(Request $request, Organization $organization): AnonymousResourceCollection
    {
        $this->authorize($request->user(), 'viewAny', $organization, Note::class);

        $notes = $this->noteRepository->getNotes($organization, $request->all());

        return NoteResource::collection($notes);
    }

    public function store(StoreNoteRequest $request, Organization $organization): NoteResource|JsonResponse
    {
        $this->authorize($request->user(), 'create', $organization, Note::class);

        try {
            /* @phpstan-ignore-next-line */
            $note = $this->noteRepository->insert($organization, $request->user(), $request->all());

            return new NoteResource($note);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }

    }

    public function show(Note $note): void
    {
        //
    }

    public function update(UpdateNoteRequest $request, Organization $organization, Note $note): NoteResource|JsonResponse
    {
        $this->authorize($request->user(), 'update', $organization, $note);

        try {
            $note = $this->noteRepository->update($note, $request->all());

            return new NoteResource($note);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }

    public function destroy(Request $request, Organization $organization, Note $note): Response|JsonResponse
    {
        $this->authorize($request->user(), 'delete', $organization, $note);

        try {
            $this->noteRepository->delete($note);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }

    public function authorize(?User $user, string $action, Organization $organization, mixed $classOrModel): void
    {
        if ($user && $user->cannot($action, [$classOrModel, $organization])) {
            abort(403);
        }

    }
}
