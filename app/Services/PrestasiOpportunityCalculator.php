<?php

namespace App\Services;

use Illuminate\Support\Collection;

class PrestasiOpportunityCalculator
{
    public function score(?float $matematika, ?float $bahasaIndonesia): ?float
    {
        if ($matematika === null || $bahasaIndonesia === null) {
            return null;
        }

        return round(($matematika + $bahasaIndonesia) / 2, 2);
    }

    public function rank(float $studentScore, Collection $submittedScores): array
    {
        $scores = $submittedScores
            ->filter(fn ($score) => $score !== null)
            ->map(fn ($score) => (float) $score)
            ->push($studentScore)
            ->sortDesc()
            ->values();

        return [
            'rank' => $scores->search(fn (float $score) => $score <= $studentScore) + 1,
            'total' => $scores->count(),
        ];
    }

    public function cutoff(Collection $scores, int $quota): ?float
    {
        if ($quota <= 0 || $scores->count() < $quota) {
            return null;
        }

        return round((float) $scores->sortDesc()->values()->get($quota - 1), 2);
    }

    public function estimate(float $studentScore, ?float $cutoff, int $quota, int $applicants): array
    {
        if ($quota <= 0) {
            return ['percentage' => 5, 'label' => 'Rendah', 'level' => 'low'];
        }

        $pressure = $applicants / $quota;

        if ($cutoff === null) {
            $percentage = $pressure < 0.75 ? 85 : ($pressure < 1 ? 72 : 58);
        } else {
            $scoreComponent = 50 + (($studentScore - $cutoff) * 6);
            $pressureAdjustment = max(-20, min(12, (1 - $pressure) * 18));
            $percentage = (int) round(max(5, min(95, $scoreComponent + $pressureAdjustment)));
        }

        return [
            'percentage' => $percentage,
            'label' => $percentage >= 70 ? 'Tinggi' : ($percentage >= 40 ? 'Sedang' : 'Rendah'),
            'level' => $percentage >= 70 ? 'high' : ($percentage >= 40 ? 'medium' : 'low'),
        ];
    }
}
