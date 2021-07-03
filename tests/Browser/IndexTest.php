<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class IndexTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function tearDown(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
        });
        parent::tearDown();
    }

    /**
     * Asserts that user visiting the index route will see a piece of text that
     * is hardcoded for now and serves only as an example to check if Browser Tests
     * are working correctly
     * 
     * @group index
     */
    public function test_user_can_see_hello_on_main_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route("index"))
                ->assertSee('hello');
        });
    }

    /**
     * @group index
     * @group authentication
     */
    public function test_browserCanAuthenticate(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->assertAuthenticatedAs($this->user);
        });
    }
}
