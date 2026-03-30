<?php

namespace LaravelEnso\Countries\Upgrades;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Helpers\Table;

class MediSoftId implements MigratesTable
{
    public function isMigrated(): bool
    {
        return Table::hasColumn('countries', 'medi_soft_id');
    }

    public function migrateTable(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->unsignedInteger('medi_soft_id')->nullable()
                ->after('sub_region_code');
        });
    }
}
