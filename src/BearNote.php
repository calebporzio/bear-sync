<?php

namespace BearSync;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class BearNote extends Model
{
    use UsesBearsDatabaseConnection;

    protected $appends = ['content'];

    public function tags()
    {
        return $this->belongsToMany(BearTag::class, 'Z_6TAGS', 'Z_6NOTES', 'Z_13TAGS');
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

        foreach ($matches as $match) {
            if (empty($match)) continue;

            $originalPath = Str::after(Str::beforeLast($match[0], ']'), '[image:');
            $originalFullPath = static::getBearPath().'/Local Files/Note Images/'.$originalPath;

            $newFileName = crc32($originalPath).'.'.Str::afterLast($originalPath, '.');

            $newFile = $callback($originalFullPath, $newFileName);

            $replaceStack[$match[0]] = '![]('.$newFile.')';
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
            $builder->fromRaw("(select Z_PK as id, ZTITLE as title, ZTEXT as raw_content from ZSFNOTE) as bear_notes");
        });
    }
}
