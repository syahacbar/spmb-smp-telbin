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
        $startDate = (string) ($settings['jam_pelayanan_tanggal_mulai'] ?? '2026-07-01');
        $endDate = (string) ($settings['jam_pelayanan_tanggal_selesai'] ?? '2026-07-06');
        $now = CarbonImmutable::now(self::TIMEZONE);

        if (! $enabled) {
            return $this->openStatus($enabled, $start, $end, $startDate, $endDate, $now);
        }

        if (! $this->isValidDate($startDate) || ! $this->isValidDate($endDate) || ! preg_match('/^\d{2}:\d{2}$/', $start) || ! preg_match('/^\d{2}:\d{2}$/', $end)) {
            return $this->openStatus($enabled, $start, $end, $startDate, $endDate, $now);
        }

        $today = $now->toDateString();
        $withinDateRange = $today >= $startDate && $today <= $endDate;
        $currentTime = $now->format('H:i');
        $withinHours = $start <= $end
            ? $currentTime >= $start && $currentTime <= $end
            : $currentTime >= $start || $currentTime <= $end;

        $isOpen = $withinDateRange && $withinHours;

        return [
            'enabled' => $enabled,
            'open' => $isOpen,
            'start' => $start,
            'end' => $end,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'now' => $now,
            'message' => $isOpen
                ? 'Layanan pendaftaran sedang dibuka.'
                : $this->makeClosedMessage($settings, $start, $end, $startDate, $endDate),
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

    private function openStatus(bool $enabled, string $start, string $end, string $startDate, string $endDate, CarbonImmutable $now): array
    {
        return [
            'enabled' => $enabled,
            'open' => true,
            'start' => $start,
            'end' => $end,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'now' => $now,
            'message' => 'Layanan pendaftaran sedang dibuka.',
        ];
    }

    private function isValidDate(string $value): bool
    {
        if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $matches)) {
            return false;
        }

        return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }

    private function makeClosedMessage(array $settings, string $start, string $end, string $startDate, string $endDate): string
    {
        $customMessage = trim((string) ($settings['jam_pelayanan_pesan_tutup'] ?? ''));

        if ($customMessage !== '') {
            return $customMessage;
        }

        return 'Layanan pendaftaran dibuka pada tanggal '.
            $this->formatDate($startDate).
            ' sampai '.
            $this->formatDate($endDate).
            ', pukul '.
            str_replace(':', '.', $start).
            ' sampai '.
            str_replace(':', '.', $end).
            ' WIT.';
    }

    private function formatDate(string $value): string
    {
        if (! $this->isValidDate($value)) {
            return $value;
        }

        return CarbonImmutable::createFromFormat('Y-m-d', $value, self::TIMEZONE)->translatedFormat('d F Y');
    }
}
