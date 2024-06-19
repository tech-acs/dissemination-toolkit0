<?php

namespace App\Models;

use App\Enums\CensusTableTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Translatable\HasTranslations;

/**
 * @method static create(array $only)
 */
class CensusTable extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];
    public $translatable = ['title', 'description'];
    protected $casts = [
        'dataset_type' => CensusTableTypeEnum::class
    ];
    protected $fillable = [
        'title',
        'description',
        'producer',
        'publisher',
        'published_date',
        'published',
        'data_source',
        'file_name',
        'file_size',
        'file_type',
        'file_path',
        'comment',
        'user_id',
        'dataset_type'
    ];

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class);
    }
    public function scopePublished($query)
    {
        return $query->wherePublished(true);
    }
}
