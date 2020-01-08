<?php

namespace BearSync;

use Illuminate\Database\Eloquent\Model;

class BearTag extends Model
{
    use UsesBearsDatabaseConnection;

    public static function searchByTitle($query)
    {
        return static::where('title', 'like', '%'.$query.'%')->get();
    }

    public function notes()
    {
        return $this->belongsToMany(BearNote::class, 'Z_6TAGS', 'Z_13TAGS', 'Z_6NOTES');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($builder) {
            $builder->fromRaw("(select Z_PK as id, ZTITLE as title from ZSFNOTETAG) as bear_tags");
        });
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
