<?php

namespace LaravelEnso\Countries\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Helpers\Traits\ActiveState;
use LaravelEnso\Helpers\Traits\AvoidsDeletionConflicts;
use LaravelEnso\Rememberable\Traits\Rememberable;

class Country extends Model
{
    use ActiveState, AvoidsDeletionConflicts, HasFactory, Rememberable;

    protected $guarded = ['id'];

    protected $rememberableKeys = ['id', 'iso_3166_2', 'name'];

    public function regionLabel(): string
    {
        return match ($this->name) {
            'Romania' => __('County'),
            'United States' => __('State'),
            default => __('Region'),
        };
    }
}
