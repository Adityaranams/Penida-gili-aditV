<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\User;

/**
 * What the console's bell shows: reservations as they arrive.
 *
 * There is no broadcasting in this project, so "live" here means the header
 * polls resources/js/admin-header.js every half minute. That is fast enough
 * for a booking desk and costs one small query, where websockets would need
 * a server process the host does not run.
 */
class AdminNotifications
{
    /** How many bookings the dropdown lists at once. */
    public const LIMIT = 8;

    /** Bookings this admin has not looked at yet. */
    public static function unreadCount(User $user): int
    {
        return self::query($user)->count();
    }

    /** The newest bookings, each flagged with whether this admin has seen it. */
    public static function items(User $user): array
    {
        return Booking::query()
            ->with('bookable')
            ->latest('created_at')
            ->limit(self::LIMIT)
            ->get()
            ->map(fn (Booking $booking) => [
                'id' => $booking->id,
                'reference' => $booking->reference,
                'title' => $booking->customer_name,
                'detail' => self::detail($booking),
                'amount' => Money::idr($booking->total, 'Rp.'),
                'when' => $booking->created_at->diffForHumans(short: true),
                'unread' => self::isUnread($user, $booking),
                'url' => route('admin.report', ['q' => $booking->reference]),
            ])
            ->all();
    }

    /** Clear this admin's dot; everyone else keeps theirs. */
    public static function markSeen(User $user): void
    {
        $user->forceFill(['notifications_seen_at' => now()])->save();
    }

    private static function query(User $user)
    {
        return Booking::query()
            ->when($user->notifications_seen_at, fn ($q, $seen) => $q->where('created_at', '>', $seen));
    }

    private static function isUnread(User $user, Booking $booking): bool
    {
        return $user->notifications_seen_at === null
            || $booking->created_at->greaterThan($user->notifications_seen_at);
    }

    /** "Sanur → Nusa Penida", "Semabu Hills · Deluxe", or the activity's name. */
    private static function detail(Booking $booking): string
    {
        $row = $booking->toReportRow();

        return $row['from'] === $row['to']
            ? $row['to']
            : trim($row['from'].' → '.$row['to'], ' →');
    }
}
