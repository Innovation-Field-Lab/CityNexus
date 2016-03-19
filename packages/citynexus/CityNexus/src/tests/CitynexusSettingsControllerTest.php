<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CitynexusSettingsControllerTest extends TestCase
{
    use WithoutMiddleware;
    use DatabaseTransactions;

    /**
     *
     * Test Tabler Index page
     *
     */
    public function testCreateUser ()
    {
        $this->visit('/register')
            ->type('John', 'first_name')
            ->type('Doe', 'last_name')
            ->type('john.doe@gmail.com', 'email')
            ->press('submit')
            ->seePageIs('/citynexus/settings');
    }

}
