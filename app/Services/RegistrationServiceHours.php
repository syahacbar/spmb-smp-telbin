<?php

namespace App\Services;

use App\Models\PengaturanSpmb;
use Carbon\CarbonImmutable;

class RegistrationServiceHours
{
    private const TIMEZONE = 'Asia/Jayapura';

    public function status(): array
    {
        $settings = PengaturanSpmb::allSettings();
        $enabled = (bool) (int) ($settings['jam_pelayanan_aktif'] ?? '0');
        $start = (string) ($settings['jam_pelayanan_mulai'] ?? '08:00');
        $end = (string) ($settings['jam_pelayanan_selesai'] ?? '14:00');
        $days = $this->allowedDays((string) ($settings['jam_pelayanan_hari'] ?? '1,2,3,4,5,6,7'));
        $now = CarbonImmutable::now(self::TIMEZONE);

        if (! $enabled) {
            return $this->openStatus($enabled, $start, $end, $days, $now);
        }

        if (! preg_match('/^\d{2}:\d{2}$/', $start) || ! preg_match('/^\d{2}:\d{2}$/', $end)) {
            return $this->openStatus($enabled, $start, $end, $days, $now);
        }

        $todayAllowed = in_array((int) $now->isoWeekday(), $days, true);
        $currentTime = $now->format('H:i');
        $withinHours = $start <= $end
            ? $currentTime >= $start && $currentTime <= $end
            : $currentTime >= $start || $currentTime <= $end;

        $isOpen = $todayAllowed && $withinHours;

        return [
            'enabled' => $enabled,
            'open' => $isOpen,
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'now' => $now,
            'message' => $isOpen
                ? 'Layanan pendaftaran sedang dibuka.'
                : $this->makeClosedMessage($settings, $start, $end),
        ];
    }

    public function isOpen(): bool
    {
        return (bool) $this->status()['open'];
    }

    public function closedMessage(): string
    {
        return (string) $this->status()['message'];
    }

    private function openStatus(bool $enabled, string $start, string $end, array $days, CarbonImmutable $now): array
    {
        return [
            'enabled' => $enabled,
            'open' => true,
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'now' => $now,
            'message' => 'Layanan pendaftaran sedang dibuka.',
        ];
    }

    private function allowedDays(string $value): array
    {
        $days = collect(explode(',', $value))
            ->map(fn (string $day): int => (int) trim($day))
            ->filter(fn (int $day): bool => $day >= 1 && $day <= 7)
            ->unique()
            ->values()
            ->all();

        return $days ?: [1, 2, 3, 4, 5, 6, 7];
    }

    private function makeClosedMessage(array $settings, string $start, string $end): string
    {
        $customMessage = trim((string) ($settings['jam_pelayanan_pesan_tutup'] ?? ''));

        if ($customMessage !== '') {
            return $customMessage;
        }

        return 'Layanan pendaftaran dibuka pukul '.
            str_replace(':', '.', $start).
            ' sampai '.
            str_replace(':', '.', $end).
            ' WIT.';
    }
}
