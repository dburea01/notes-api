<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Library;
use App\Models\Note;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class NoteRepository
{
    public function getNotes(array $filters): Paginator
    {
        $query = Note::orderBy('note');

        $query->when(isset($filters['note']), function ($q) use ($filters) {
            return $q->where('note', 'like', '%'.$filters['note'].'%');
        });

        $query->when(isset($filters['color']), function ($q) use ($filters) {
            return $q->where('color', $filters['color']);
        });

        return $query->paginate();
    }

    public function insert(array $data): Note
    {
        $note = new Note();
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

    public function delete(Note $library): void
    {
        $library->delete();
    }
}
