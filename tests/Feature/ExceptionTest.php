<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ExceptionTest extends TestCase
{
    public function testNotFound(): void
    {
        $data['message'] = 'Not Found.';

        $this->getJson('/')
            ->assertNotFound()
            ->assertExactJson($data);
    }
}
