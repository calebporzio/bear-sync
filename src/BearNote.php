<?php

namespace BearSync;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Connectors\ConnectionFactory;

class BearNote extends Model
{
    protected $appends = ['content'];

    public static function searchByTitle($query)
    {
        return static::where('title', 'like', '%'.$query.'%')->get();
    }

    public function contentWithReplacedImages($callback)
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

    public static function resolveConnection($connection = null)
    {
        return app(ConnectionFactory::class)->make([
            'driver' => 'sqlite',
            'database' => static::getBearDatabasePath(),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($builder) {
            $builder->fromRaw("(select Z_PK as id, ZTITLE as title, ZTEXT as raw_content from ZSFNOTE) as bear_notes");
        });
    }

    public static function getBearDatabasePath()
    {
        throw_unless(
            File::exists($bearDb = static::getBearPath().'/database.sqlite'),
            new \Exception('Bear: Can\'t find Bear database')
        );

        return $bearDb;
    }

    public static function getBearPath()
    {
        if ($bearPathFromEnv = env('BEAR_PATH')) return $bearPathFromEnv;

        // Static cache in case of multiple calls per execution.
        static $bearPath;
        if ($bearPath) return $bearPath;

        preg_match('/\/Users\/([a-zA-Z0-9\s]*)\//', base_path(), $match);

        $user = throw_unless($match[1] ?? false, new \Exception('Bear: Can\'t find system\'s user'));

        $bearPath = collect(File::directories('/Users/'.$user.'/Library/Group Containers/'))->first(function ($directory) {
            return preg_match('/shinyfrog\.bear/', $directory);
        });

        return throw_unless($bearPath = $bearPath.'/Application Data', new \Exception('Bear: Can\'t find Bear in system'));
    }
}

// Dump of a raw Bear Note for reference:

//  "Z_PK" => "1",
//  "Z_ENT" => "6",
//  "Z_OPT" => "3",
//  "ZARCHIVED" => "0",
//  "ZENCRYPTED" => "0",
//  "ZHASFILES" => "0",
//  "ZHASIMAGES" => "0",
//  "ZHASSOURCECODE" => "0",
//  "ZLOCKED" => "0",
//  "ZORDER" => "0",
//  "ZPERMANENTLYDELETED" => "0",
//  "ZPINNED" => "0",
//  "ZSHOWNINTODAYWIDGET" => "0",
//  "ZSKIPSYNC" => "0",
//  "ZTODOCOMPLETED" => "0",
//  "ZTODOINCOMPLETED" => "0",
//  "ZTRASHED" => "0",
//  "ZFOLDER" => null,
//  "ZSERVERDATA" => "170",
//  "ZARCHIVEDDATE" => null,
//  "ZCONFLICTUNIQUEIDENTIFIERDATE" => null,
//  "ZCREATIONDATE" => "568330572.643313",
//  "ZLOCKEDDATE" => null,
//  "ZMODIFICATIONDATE" => "568330886.35148",
//  "ZORDERDATE" => null,
//  "ZPINNEDDATE" => null,
//  "ZTRASHEDDATE" => null,
//  "ZCONFLICTUNIQUEIDENTIFIER" => null,
//  "ZLASTEDITINGDEVICE" => "Caleb’s MacBook Pro",
//  "ZSUBTITLE" => "- Worst Practices - Actual Professional Programmers - No Plans To Merge -",
//  "ZTEXT" => """
//    # TPT Names\n
//    - Worst Practices\n
//    - Actual Professional Programmers\n
//    - No Plans To Merge\n
//    -
//    """,
//  "ZTITLE" => "TPT Names",
//  "ZUNIQUEIDENTIFIER" => "36804850-C28E-4C3D-9727-54914AECA1E5-14822-00034472F55D9128",
//  "ZVECTORCLOCK" => b"bplist00Ñ\x01\x02_\x10$5DD216E2-38FA-5DDC-9420-AB2B33408CDF\x10\x05\x08\v2\0\0\0\0\0\0\x01\x01\0\0\0\0\0\0\0\x03\0\0\0\0\0\0\0\0\0\0\0\0\0\0\04",
