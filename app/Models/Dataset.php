<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;

class Dataset extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];
    public array $translatable = ['name', 'description'];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function dimensions(): BelongsToMany
    {
        return $this->belongsToMany(Dimension::class);
    }

    public function indicators(): BelongsToMany
    {
        return $this->belongsToMany(Indicator::class);
    }

    public function years(): BelongsToMany
    {
        return $this->belongsToMany(Year::class);
    }

    public function observations()
    {
        try {
            return DB::table($this->fact_table)
                //->where('indicator_id', $this->indicator_id)
                ->where('dataset_id', $this->id)
                ->count();
        } catch (\Exception $exception) {
            return 0;
        }
    }
}
