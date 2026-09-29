<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\UploadedFile;

abstract class TestCase extends BaseTestCase
{
    /**
     * A fake upload that passes the `image` validation rule.
     *
     * `UploadedFile::fake()->image()` draws a real picture and therefore needs the GD
     * extension, which is not compiled into every local PHP build. This keeps the
     * upload tests runnable everywhere by faking the size and mime type instead.
     */
    protected function fakeImage(string $name = 'photo.jpg', int $kilobytes = 64): UploadedFile
    {
        if (function_exists('imagecreatetruecolor')) {
            return UploadedFile::fake()->image($name);
        }

        return UploadedFile::fake()->create($name, $kilobytes, 'image/jpeg');
    }
}
