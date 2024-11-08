<?php

namespace Tests\Feature\Http\Controllers;

use Tests\ControllerTestCase;

class PingControllerTest extends ControllerTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_ping_endpoint_structure(): void
    {
        $response = $this->get(route('v1.ping.show'));

        $this->assertShowStructure($response, []);
    }
}
