<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The monitoring host may read the AI-MEMORY screens without a login; nobody
 * else, and nothing else, gets that pass.
 */
class AiMemoryMonitorAccessTest extends TestCase
{
    private const MONITOR = '170.233.230.254';

    protected function setUp(): void
    {
        parent::setUp();
        config(['aimemory.monitor_ips' => [self::MONITOR]]);
    }

    public function test_the_monitor_address_opens_ai_memory_without_a_login(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => self::MONITOR])
            ->get('/admin/ai-memory')
            ->assertOk();
    }

    public function test_another_address_is_sent_to_the_login(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])
            ->get('/admin/ai-memory')
            ->assertRedirect(route('login'));
    }

    public function test_a_forged_forwarded_header_does_not_grant_the_pass(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])
            ->withHeaders(['X-Forwarded-For' => self::MONITOR])
            ->get('/admin/ai-memory')
            ->assertRedirect(route('login'));
    }

    public function test_the_pass_does_not_reach_the_rest_of_the_admin(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => self::MONITOR])
            ->get('/admin')
            ->assertRedirect(route('login'));
    }

    public function test_an_empty_list_grants_nothing(): void
    {
        config(['aimemory.monitor_ips' => []]);

        $this->withServerVariables(['REMOTE_ADDR' => self::MONITOR])
            ->get('/admin/ai-memory')
            ->assertRedirect(route('login'));
    }
}
