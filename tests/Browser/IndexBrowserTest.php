<?php

namespace Tests\Browser;

use App\Libs\Utils\NamedRoute;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class IndexBrowserTest extends DuskTestCase
{
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit(route(NamedRoute::GET_INDEX))
                ->assertPathIs('/');
        });
    }
}
