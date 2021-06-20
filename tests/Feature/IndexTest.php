<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
  /**
   * Asserts that the main page will not throw any errors upon visiting
   */
  public function test_userCanVisitMainPage(): void
  {
    $response = $this->get('/');

    $response->assertStatus(200);
  }
}
