# Bear-Sync
This package provides an Eloquent model called `BearNote` that can access your local Bear notes without any configuration at all.

## Install
`composer require calebporzio/bear-sync`

## Use
```php
// Search all your Bear notes.
$notes = BearSync\BearNote::searchByTitle('Some Note Title');

// Find a specific note.
$note = BearSync\BearNote::whereTitle('Some Note Title')->first();

// Access the note's contents.
$note->id; // Bear's note id.
$note->title;
$note->content;
$note->checksum; // A checksum of the note's content, so you can detect updates.

// Fetch it's content and replace/store images.
$note->getContentAndStoreImages(function ($originalPath, $newFileName) {
    $publicFileName = "/images/{$newFileName}";

    // Copy the image and store it locally (presumably in a public directory).
    \File::copy($originalPath, public_path($publicFileName));

    // Return the file path to be referenced in the Bear note's markdown.
    // ![]($publicFileName)
    return $publicFileName;
});
```

Reminder, `BearNote` is a plain Eloquent model. Feel free to extend it, and add relationships to your own models:

```php
<?php

namespace App;

use BearSync\BearNote as BaseBearNote;

class BearNote extends BaseBearNote
{
    public function post()
    {
        return $this->hasOne(App\Post::class);
    }
}
```
