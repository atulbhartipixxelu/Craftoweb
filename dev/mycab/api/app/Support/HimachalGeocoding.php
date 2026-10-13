<?php

namespace App\Support;

class HimachalGeocoding
{
    /** Approximate bounding box: Himachal Pradesh, India (lon min, lat min, lon max, lat max). */
    public const VIEWBOX = '75.5,30.4,79.6,33.6';

    public static function searchQuery(string $q): string
    {
        $q = trim($q);
        if ($q === '') {
            return $q;
        }

        $lower = mb_strtolower($q);
        if (
            str_contains($lower, 'himachal')
            || str_contains($lower, 'himachal pradesh')
            || str_contains($lower, 'hp,')
            || str_contains($lower, ', hp')
        ) {
            return $q;
        }

        return $q.', Himachal Pradesh, India';
    }

    /**
     * @param  array<int, array{label: string, lat: float, lng: float}>  $rows
     * @return array<int, array{label: string, lat: float, lng: float}>
     */
    public static function rankResults(string $q, array $rows): array
    {
        $tokens = array_filter(preg_split('/[\s,]+/u', mb_strtolower($q)) ?: [], fn (string $t): bool => mb_strlen($t) >= 3);

        return collect($rows)
            ->sortByDesc(function (array $row) use ($tokens): int {
                $label = mb_strtolower($row['label']);
                $score = 0;
                if (str_contains($label, 'himachal')) {
                    $score += 4;
                }
                foreach ($tokens as $token) {
                    if (str_contains($label, $token)) {
                        $score += 10;
                    }
                }
                if (str_contains($label, 'chandigarh')) {
                    $score -= 8;
                }
                if (str_contains($label, 'punjab') && ! str_contains($label, 'himachal')) {
                    $score -= 4;
                }

                return $score;
            })
            ->values()
            ->all();
    }
}
