<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\Countries\Models\Country;
use LaravelEnso\Users\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    #[Test]
    public function returns_county_for_romania_region_label(): void
    {
        $country = Country::factory()->make(['name' => 'Romania']);

        $this->assertSame(__('County'), $country->regionLabel());
    }

    #[Test]
    public function returns_state_for_united_states_region_label(): void
    {
        $country = Country::factory()->make(['name' => 'United States']);

        $this->assertSame(__('State'), $country->regionLabel());
    }

    #[Test]
    public function returns_region_for_other_countries_region_label(): void
    {
        $country = Country::factory()->make(['name' => 'France']);

        $this->assertSame(__('Region'), $country->regionLabel());
    }

    #[Test]
    public function returns_only_active_countries(): void
    {
        $this->actingAs(User::first());

        $active = Country::factory()->create([
            'name' => 'Active Country',
            'iso_3166_3' => 'ACT',
            'is_active' => true,
        ]);

        Country::factory()->create([
            'name' => 'Inactive Country',
            'iso_3166_3' => 'INA',
            'is_active' => false,
        ]);

        $this->getJson(route('core.countries.options', [], false))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $active->id,
                'name' => $active->name,
                'currencyCode' => $active->currency_code,
            ])
            ->assertJsonMissing(['name' => 'Inactive Country']);
    }

    #[Test]
    public function can_search_countries_by_name(): void
    {
        $this->actingAs(User::first());

        $country = Country::factory()->create([
            'name' => 'Romania',
            'iso_3166_3' => 'ROU',
            'is_active' => true,
        ]);

        Country::factory()->create([
            'name' => 'Germany',
            'iso_3166_3' => 'DEU',
            'is_active' => true,
        ]);

        $this->getJson(route('core.countries.options', [
            'query' => 'Roman',
        ], false))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $country->id,
                'name' => 'Romania',
                'currencyCode' => $country->currency_code,
            ])
            ->assertJsonMissing(['name' => 'Germany']);
    }

    #[Test]
    public function can_search_countries_by_iso_3166_3(): void
    {
        $this->actingAs(User::first());

        $country = Country::factory()->create([
            'name' => 'Romania',
            'iso_3166_3' => 'ROU',
            'is_active' => true,
        ]);

        Country::factory()->create([
            'name' => 'Germany',
            'iso_3166_3' => 'DEU',
            'is_active' => true,
        ]);

        $this->getJson(route('core.countries.options', [
            'query' => 'ROU',
        ], false))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $country->id,
                'name' => 'Romania',
                'currencyCode' => $country->currency_code,
            ])
            ->assertJsonMissing(['name' => 'Germany']);
    }

    #[Test]
    public function requires_authentication(): void
    {
        $this->getJson(route('core.countries.options', [], false))
            ->assertUnauthorized();
    }
}
