# Bear-Sync
This package provides an Eloquent model called `BearNote` that can access your local Bear notes without any configuration at all.

## Install
`composer require calebporzio/bear-sync`

## Use
```php
use BearSync;

// Search all your Bear notes.
$notes = BearNote::searchByTitle('Some Note Title');

// Find a specific one:
$note = BearNote::whereTitle('Some Note Title')->first();

// Access it's contents
$note->id; // Bear's note id.
$note->title;
$note->content;
$note->checksum; // A checksum of the note's content, so you can detect updates.

// Fetch it's content and replace/store images:
$note->getContentAndStoreImages(function ($originalPath, $newFileName) {
    $publicFileName = "/images/{$newFileName}";

    // Copy the image and store it locally (presumably in a public directory)
    \File::copy($originalPath, public_path($publicFileName));

    // Return the file path to be referenced in the Bear note's markdown:
    // ![]($publicFileName)
    return $publicFileName;
});
```
