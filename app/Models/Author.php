<?php

namespace App\Models;

use App\Support\ImagePath;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'role', 'photo', 'credential', 'bio'])]
class Author extends Model
{
    use HasFactory;

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /** The uploaded portrait, or null when the byline should fall back to initials. */
    protected function photoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->photo ? ImagePath::url($this->photo, 'authors') : null);
    }

    /** "AS" — the default avatar when no portrait was uploaded. */
    protected function initials(): Attribute
    {
        return Attribute::get(fn () => collect(preg_split('/\s+/', trim((string) $this->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('') ?: '?');
    }

    /** "Capt. Wayan Sudira - Master Mariner" as shown in the editor's AUTHOR bar. */
    protected function signature(): Attribute
    {
        return Attribute::get(fn () => $this->role ? "{$this->name} - {$this->role}" : $this->name);
    }
}
