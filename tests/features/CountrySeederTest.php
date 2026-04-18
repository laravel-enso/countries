<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\Countries\Database\Seeders\CountrySeeder;
use LaravelEnso\Countries\Http\Resources\Country as CountryResource;
use LaravelEnso\Countries\Models\Country;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CountrySeederTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function serializes_country_id_name_and_currency_code(): void
    {
        $country = Country::factory()->make([
            'name' => 'Romania',
            'currency_code' => 'RON',
        ]);

        $this->assertSame([
            'id' => $country->id,
            'name' => 'Romania',
            'currencyCode' => 'RON',
        ], CountryResource::make($country)->resolve());
    }

    #[Test]
    public function country_seeder_populates_countries_table_from_dataset(): void
    {
        $seeder = new CountrySeeder();

        $this->assertSame(0, Country::count());

        $seeder->run();

        $this->assertSame($seeder->countries()->count(), Country::count());
        $this->assertDatabaseHas('countries', [
            'name' => 'Romania',
            'iso_3166_2' => 'RO',
            'iso_3166_3' => 'ROU',
        ]);
    }
}
