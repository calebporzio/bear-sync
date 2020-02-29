<?php

namespace BearSync;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class BearNote extends Model
{
    use UsesBearsDatabaseConnection, ResolvesNoteTagPivot;

    protected $appends = ['content'];

    public function tags()
    {
        return $this->belongsToMany(
            BearTag::class,
            $this->getNoteTagPivotTable(),
            $this->getNoteColumn(),
            $this->getTagColumn()
        );
    }

    public static function searchByTitle($query)
    {
        return static::where('title', 'like', '%'.$query.'%')->get();
    }

    public function getContentAndStoreImages($callback)
    {
        // Check the note's content for images:
        preg_match_all('/\[image:.*\]/', $this->content, $matches);

        $replaceStack = [];

        foreach ($matches[0] as $match) {
            if (empty($match)) continue;

            $originalPath = Str::after(Str::beforeLast($match, ']'), '[image:');
            $originalFullPath = static::getBearPath().'/Local Files/Note Images/'.$originalPath;

            $newFileName = crc32($originalPath).'.'.Str::afterLast($originalPath, '.');

            $newFile = $callback($originalFullPath, $newFileName);

            $replaceStack[$match] = '![]('.$newFile.')';
        }

        return str_replace(array_keys($replaceStack), array_values($replaceStack), $this->content);
    }

    public function getContentAttribute()
    {
        return Str::after($this->raw_content, $this->title);
    }

    public function getChecksumAttribute()
    {
        return crc32($this->content);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($builder) {
            $builder->fromRaw("(select Z_PK as id, ZTITLE as title, ZTEXT as raw_content, Z_ENT as pivot_column_id from ZSFNOTE) as bear_notes");
        });
    }
}
