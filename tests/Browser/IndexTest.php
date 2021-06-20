<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class IndexTest extends DuskTestCase
{
    /**
     * Asserts that user visiting the index route will see a piece of text that
     */
    public function test_userCanSeeHelloOnMainPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route("index"))
                ->assertSee('hello');
        });
    }
}
