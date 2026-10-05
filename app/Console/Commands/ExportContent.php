<?php

namespace App\Console\Commands;

use App\Support\ContentSnapshot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportContent extends Command
{
    protected $signature = 'content:export';

    protected $description = 'Write the catalogue to database/content/content.json so it can be committed and shared';

    public function handle(): int
    {
        $data = ContentSnapshot::collect();
        $path = ContentSnapshot::path();

        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n");

        $this->table(
            ['Table', 'Rows'],
            collect($data)->map(fn (array $rows, string $table) => [$table, count($rows)])->values()->all(),
        );

        $this->info('Written to '.str_replace(base_path().DIRECTORY_SEPARATOR, '', $path));
        $this->line('Commit that file together with storage/app/public/uploads so the images travel with it.');

        return self::SUCCESS;
    }
}
