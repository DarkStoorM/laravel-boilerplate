<?php

namespace Tests\Route;

use App\Libs\Utils\NamedRoute;
use Tests\TestCase;

class RouteTest extends TestCase
{
    public function testIsIndexOk()
    {
        $response = $this->get(route(NamedRoute::GET_INDEX));

        $response->assertOk();
    }
}
