<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** The footer's "Boat Booking" column, which used to be four dead "#" links. */
class FooterLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_footer_column_points_at_real_destinations(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        foreach ([route('home').'#about', route('home').'#why', route('boats.schedules'), route('privacy')] as $href) {
            $this->assertStringContainsString('href="'.e($href).'"', $html);
        }

        $this->assertStringNotContainsString('href="#"', $html, 'A footer link still goes nowhere.');
    }

    public function test_the_home_page_carries_the_anchors_those_links_jump_to(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('id="about"', false)
            ->assertSee('id="why"', false);
    }

    public function test_the_privacy_policy_is_published_and_names_the_law_it_follows(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('Kebijakan Privasi')
            ->assertSee('Undang-Undang Nomor 27 Tahun 2022');
    }

    public function test_the_policy_shows_the_contact_details_the_console_manages(): void
    {
        config(['penida.booking.email' => 'halo@penidagili.test', 'penida.booking.whatsapp' => '628111222333']);

        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('halo@penidagili.test')
            ->assertSee('628111222333');
    }
}
