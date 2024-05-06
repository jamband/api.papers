<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Site;

use Carbon\Carbon;
use Tests\TestCase;

class CsrfCookieTest extends TestCase
{
    private Carbon $carbon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->carbon = new Carbon();
    }

    public function testAccessControlHeaders(): void
    {
        $this->get('/csrf-cookie')
            ->assertHeader('access-control-allow-origin', $this->app['config']['app.frontend_origin'])
            ->assertHeader('access-control-allow-credentials', 'true');
    }

    public function testCsrfCookie(): void
    {
        $response = $this->get('/csrf-cookie');
        $setCookie = $response->headers->all('set-cookie');
        $this->assertCount(2, $setCookie);

        [$token, ] = $setCookie;

        $tokenValues = explode('; ', $token);
        $this->assertCount(5, $tokenValues);

        $this->assertMatchesRegularExpression('/\AXSRF-TOKEN=eyJpdiI.+\z/', $token);
        $this->assertContains('expires='.$this->expires(), $tokenValues);
        $this->assertContains('Max-Age=7200', $tokenValues);
        $this->assertContains('path=/', $tokenValues);
        $this->assertContains('samesite=lax', $tokenValues);
    }

    public function testSessionCookie(): void
    {
        $response = $this->get('/csrf-cookie');
        $setCookie = $response->headers->all('set-cookie');
        $this->assertCount(2, $setCookie);

        [, $session] = $setCookie;

        $sessionValues = explode('; ', $session);
        $this->assertCount(6, $sessionValues);

        $this->assertMatchesRegularExpression(
            '/\A'.str_replace('.', '', strtolower($this->app['config']['app.name'])).'_session=eyJpdiI.+\z/',
            $session
        );

        $this->assertContains('expires='.$this->expires(), $sessionValues);
        $this->assertContains('Max-Age=7200', $sessionValues);
        $this->assertContains('path=/', $sessionValues);
        $this->assertContains('httponly', $sessionValues);
        $this->assertContains('samesite=lax', $sessionValues);
    }

    public function testContent(): void
    {
        $this->get('/csrf-cookie')
            ->assertNoContent();
    }

    private function expires(): string
    {
        return $this->carbon
            ->addMinutes((int)$this->app['config']['session.lifetime'])
            ->format('D, d M Y H:i:s').' GMT';
    }
}
