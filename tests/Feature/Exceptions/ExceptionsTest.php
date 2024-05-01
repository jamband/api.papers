<?php

declare(strict_types=1);

namespace Tests\Feature\Exceptions;

use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExceptionsTest extends TestCase
{
    private Router $router;
    private string $uri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = $this->app->make(Router::class);
        $this->uri = '/testing-'.Str::random(10);
    }

    public function testNotFound(): void
    {
        $this->get('/')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testMethodNotAllowed(): void
    {
        $this->router->middleware('web')
            ->get($this->uri);

        $this->post($this->uri)
            ->assertMethodNotAllowed()
            ->assertExactJson(['message' => 'Method Not Allowed.']);
    }
}
