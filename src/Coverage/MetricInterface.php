<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Coverage;

/**
 * One line of a PHPUnit coverage summary, e.g. `Lines: 99.50% (199/200)`.
 */
interface MetricInterface
{
    public function getCovered(): int;

    public function getName(): string;

    public function getTotal(): int;

    /**
     * Whether the covered share reaches the given percentage. Compared on the raw
     * counts, never on PHPUnit's rounded percentage, so 1999/2000 fails a 100% floor
     * even though the report prints it as 100.00%. A metric with nothing to cover
     * always meets it.
     */
    public function meets(float $minimumPercent): bool;
}
