<?php

namespace JeffersonGoncalves\UsPrivacyAssessments\Enums;

use InvalidArgumentException;

/**
 * Likelihood (1-3) x impact (1-3): 1-2 low, 3-4 medium, 6-9 high.
 */
enum RiskLevel: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public static function score(int $likelihood, int $impact): self
    {
        if ($likelihood < 1 || $likelihood > 3 || $impact < 1 || $impact > 3) {
            throw new InvalidArgumentException("Likelihood and impact go from 1 to 3, got {$likelihood} x {$impact}.");
        }

        return match (true) {
            $likelihood * $impact >= 6 => self::High,
            $likelihood * $impact >= 3 => self::Medium,
            default => self::Low,
        };
    }

    public function weight(): int
    {
        return match ($this) {
            self::Low => 1,
            self::Medium => 2,
            self::High => 3,
        };
    }
}
