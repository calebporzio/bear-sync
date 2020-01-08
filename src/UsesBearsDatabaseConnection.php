<?php

namespace BearSync;

use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Facades\File;

trait UsesBearsDatabaseConnection
{
    public static function resolveConnection($connection = null)
    {
        return app(ConnectionFactory::class)->make([
            'driver' => 'sqlite',
            'database' => static::getBearDatabasePath(),
        ]);
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
