<?php

use Illuminate\Support\Carbon;

if (!function_exists('formatSecondsToTime')) {
    /**
     * Преобразование секунд в формат "часы и минуты".
     *
     * @param int $seconds
     * @return string
     */
    function formatSecondsToTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        $formattedTime = '';
        if ($hours > 0) {
            $formattedTime .= "{$hours} ч ";
        }
        if ($minutes > 0 || $hours > 0) {
            $formattedTime .= "{$minutes} мин ";
        }

        $formattedTime .= "{$remainingSeconds} сек";

        return trim($formattedTime);
    }
}

if (!function_exists('formatCustomDate')) {
    /**
     * Преобразование секунд в формат сегодня, вчера и так далее.
     *
     * @param int $seconds
     * @return string
     */
    function formatCustomDate($timestamp) {
        $date = Carbon::createFromTimestamp($timestamp);
        $now = Carbon::now();

        if ($date->isToday()) {
            return 'сегодня ' . $date->format('H:i:s');
        }

        if ($date->isYesterday()) {
            return 'вчера ' . $date->format('H:i:s');
        }

        if ($date->year === $now->year) {
            return $date->format('d M H:i:s');
        }

        return $date->format('d.m.Y H:i:s');
    }
}
