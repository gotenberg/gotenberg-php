<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\HrtimeIndex;
use PHPUnit\Framework\Attributes\Test;

use function sprintf;

final class HrtimeIndexTest extends TestCase
{
    #[Test]
    public function it_creates_alphabetical_ordered_indexes(): void
    {
        $index   = new HrtimeIndex();
        $indexes = [];

        for ($i = 0; $i < 100; $i++) {
            $indexes[$i] = $index->create();

            if ($i === 0) {
                continue;
            }

            $result = $indexes[$i] > $indexes[$i - 1];

            $this->assertTrue(
                $result,
                sprintf('Expected index "%s" to be greater than "%s"', $indexes[$i], $indexes[$i - 1]),
            );
        }
    }
}
