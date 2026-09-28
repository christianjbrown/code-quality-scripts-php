<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Coverage;

final readonly class Metric implements MetricInterface
{
    public function __construct(
        private string $name,
        private int $covered,
        private int $total,
    ) {
    }

    public function getCovered(): int
    {
        return $this->covered;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function meets(float $minimumPercent): bool
    {
        if (0 === $this->total) {
            return true;
        }

        return $this->covered * 100 >= $minimumPercent * $this->total;
    }
}
