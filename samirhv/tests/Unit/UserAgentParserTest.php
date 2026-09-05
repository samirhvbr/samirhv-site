<?php

namespace Tests\Unit;

use App\Services\UserAgentParser;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Who is a bot, and what a real visitor is browsing with.
 *
 * Every "legitimate" figure in the audit — visits, unique visitors, downloads
 * today — is filtered on `is_bot = false`. A parser that lets crawlers through
 * does not break anything; it just quietly inflates every number on the panel,
 * which is worse, because the numbers still look plausible.
 */
class UserAgentParserTest extends TestCase
{
    private UserAgentParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new UserAgentParser;
    }

    public static function bots(): array
    {
        return [
            'Googlebot' => ['Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'],
            'Bingbot' => ['Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'],
            'GPTBot' => ['Mozilla/5.0 (compatible; GPTBot/1.0; +https://openai.com/gptbot)'],
            'ClaudeBot' => ['Mozilla/5.0 (compatible; ClaudeBot/1.0; +claudebot@anthropic.com)'],
            'curl' => ['curl/8.5.0'],
            'wget' => ['Wget/1.21.3'],
            'python-requests' => ['python-requests/2.31.0'],
            'WhatsApp preview' => ['WhatsApp/2.23.20.0'],
            'headless Chrome' => ['Mozilla/5.0 (X11; Linux x86_64) HeadlessChrome/131.0.0.0'],
            'uptime monitor' => ['Mozilla/5.0 (compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)'],
        ];
    }

    public static function humans(): array
    {
        return [
            'Chrome on Windows' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'],
            'Safari on macOS' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.1.15'],
            'Firefox on Ubuntu' => ['Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:133.0) Gecko/20100101 Firefox/133.0'],
            'Safari on iPhone' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1'],
        ];
    }

    #[DataProvider('bots')]
    public function test_a_bot_is_recognised(string $ua): void
    {
        $out = $this->parser->parse($ua);

        $this->assertTrue($out['is_bot'], $ua);
        $this->assertSame('bot', $out['device'], $ua);
    }

    #[DataProvider('humans')]
    public function test_a_browser_is_not_called_a_bot(string $ua): void
    {
        $out = $this->parser->parse($ua);

        $this->assertFalse($out['is_bot'], $ua);
        $this->assertNotSame('bot', $out['device'], $ua);
    }

    /** An empty user-agent is a script often enough that it counts as one. */
    public function test_an_empty_user_agent_is_a_bot(): void
    {
        foreach ([null, '', '   '] as $nothing) {
            $this->assertTrue($this->parser->parse($nothing)['is_bot'], var_export($nothing, true));
        }
    }

    /**
     * The point of checking the IP at all: a crawler that dresses its
     * user-agent as Chrome is still a crawler.
     */
    public function test_a_crawler_ip_outranks_a_convincing_user_agent(): void
    {
        $chrome = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/131.0.0.0 Safari/537.36';

        $this->assertTrue($this->parser->parse($chrome, '66.249.66.1')['is_bot'], 'Googlebot range');
        $this->assertTrue($this->parser->parse($chrome, '2001:4860:4801:10::1')['is_bot'], 'Googlebot IPv6');
        $this->assertFalse($this->parser->parse($chrome, '189.4.10.7')['is_bot'], 'an ordinary address');
    }

    /** A CIDR check that ignores the mask would swallow whole neighbourhoods. */
    public function test_the_range_check_respects_its_mask(): void
    {
        $chrome = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/131.0.0.0 Safari/537.36';

        $this->assertTrue($this->parser->ipIsCrawler('66.249.95.255'), 'last address of 66.249.64.0/19');
        $this->assertFalse($this->parser->ipIsCrawler('66.249.96.0'), 'first address outside it');
        $this->assertFalse($this->parser->parse($chrome, '66.250.64.1')['is_bot']);
    }

    /** Malformed input must answer false, not throw, on a live request path. */
    public function test_a_malformed_address_is_simply_not_a_crawler(): void
    {
        foreach ([null, '', 'not-an-ip', '999.999.999.999', '::'] as $rubbish) {
            $this->assertFalse($this->parser->ipIsCrawler($rubbish), var_export($rubbish, true));
        }
    }

    public function test_it_names_the_browser_and_the_system(): void
    {
        $out = $this->parser->parse('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/131.0.0.0 Safari/537.36');

        $this->assertNotSame('', $out['browser']);
        $this->assertNotSame('', $out['os']);
    }

    /** The shape is written into two tables; it must never come back short. */
    public function test_it_always_returns_the_whole_shape(): void
    {
        foreach ([null, '', 'curl/8.5.0', 'Mozilla/5.0 (X11; Linux x86_64) Firefox/133.0'] as $ua) {
            $out = $this->parser->parse($ua, '10.0.0.1');

            $this->assertSame(['is_bot', 'device', 'browser', 'os'], array_keys($out));
            $this->assertIsBool($out['is_bot']);
            foreach (['device', 'browser', 'os'] as $field) {
                $this->assertIsString($out[$field]);
            }
        }
    }
}
