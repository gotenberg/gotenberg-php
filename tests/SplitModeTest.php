<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\SplitMode;
use PHPUnit\Framework\Attributes\Test;

final class SplitModeTest extends TestCase
{
    #[Test]
    public function it_creates_an_intervals_split_mode(): void
    {
        $mode = SplitMode::intervals(1);

        $this->assertSame('intervals', $mode->mode);
        $this->assertSame('1', $mode->span);
        $this->assertFalse($mode->unify);
    }

    #[Test]
    public function it_creates_a_pages_split_mode(): void
    {
        $mode = SplitMode::pages('1-2', true);

        $this->assertSame('pages', $mode->mode);
        $this->assertSame('1-2', $mode->span);
        $this->assertTrue($mode->unify);
    }
}
