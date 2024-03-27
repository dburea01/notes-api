<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Note;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

class NoteRepository
{
    public function getNotes(Organization $organization, array $filters): Paginator
    {
        $query = Note::where('organization_id', $organization->id)->orderBy('note');

        $query->when(isset($filters['note']), function ($q) use ($filters) {
            return $q->where('note', 'like', '%'.$filters['note'].'%');
        });

        $query->when(isset($filters['color']), function ($q) use ($filters) {
            return $q->where('color', $filters['color']);
        });

        return $query->paginate();
    }

    public function insert(Organization $organization, User $user, array $data): Note
    {
        $note = new Note();
        $note->organization_id = $organization->id;
        $note->user_id = $user->id;
        $note->fill($data);
        $note->save();

        return $note;
    }

    public function update(Note $note, array $data): Note
    {
        $note->fill($data);
        $note->save();

        return $note;
    }

    public function delete(Note $note): void
    {
        $note->delete();
    }
}
