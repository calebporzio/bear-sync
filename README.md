# Bear-Sync
Access your Bear notes from Laravel.

## Important Note:
This package accesses the Bear notes on your local machine. This is not meant to be used on a remote server.

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
