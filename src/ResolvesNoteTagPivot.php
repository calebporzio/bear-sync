<?php

namespace BearSync;

trait ResolvesNoteTagPivot
{
    public function getNotePivotColumnIdAttribute()
    {
        return BearNote::pivotColumnId();
    }
    
    public function getTagPivotColumnIdAttribute()
    {
        return BearTag::pivotColumnId();
    }

    protected function getNoteTagPivotTable()
    {
        return "Z_{$this->notePivotColumnId}TAGS";
    }

    protected function getTagColumn()
    {
        return "Z_{$this->tagPivotColumnId}TAGS";
    }

    protected function getNoteColumn()
    {
        return "Z_{$this->notePivotColumnId}NOTES";
    }

    public function scopePivotColumnId($query)
    {
        return $query->distinct()
            ->select('pivot_column_id')
            ->first()
            ->pivot_column_id;
    }
}
