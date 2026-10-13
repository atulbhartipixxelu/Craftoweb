<?php

namespace App\Services;

use App\Mail\DriverNewRideMail;
use App\Mail\PassengerRideBookedMail;
use App\Models\Ride;
use Illuminate\Support\Facades\Mail;
use Throwable;

class RideBookingNotifier
{
    /**
     * @return array{passenger_email: bool, driver_email: bool}
     */
    public function sendEmails(Ride $ride): array
    {
        $ride->loadMissing(['driver', 'user']);

        $result = [
            'passenger_email' => false,
            'driver_email' => false,
        ];

        try {
            if ($ride->user?->email) {
                Mail::to($ride->user->email)->send(new PassengerRideBookedMail($ride));
                $result['passenger_email'] = true;
            }
        } catch (Throwable $e) {
            report($e);
        }

        try {
            if ($ride->driver?->email) {
                Mail::to($ride->driver->email)->send(new DriverNewRideMail($ride));
                $result['driver_email'] = true;
            }
        } catch (Throwable $e) {
            report($e);
        }

        return $result;
    }

    /**
     * Passenger opens WhatsApp to message the assigned driver (no Cloud API required).
     */
    public static function passengerToDriverWhatsappUrl(Ride $ride): ?string
    {
        $ride->loadMissing(['driver', 'user']);
        if (! $ride->driver) {
            return null;
        }

        $digits = self::digitsForWa($ride->driver->phone);
        if ($digits === '') {
            return null;
        }

        return 'https://wa.me/'.$digits.'?text='.rawurlencode(self::passengerMessageForDriver($ride));
    }

    /**
     * Driver opens WhatsApp to message the passenger (needs passenger phone on profile).
     */
    public static function driverToPassengerWhatsappUrl(Ride $ride): ?string
    {
        $ride->loadMissing(['user']);
        if (! $ride->user?->phone) {
            return null;
        }

        $digits = self::digitsForWa($ride->user->phone);
        if ($digits === '') {
            return null;
        }

        return 'https://wa.me/'.$digits.'?text='.rawurlencode(self::driverMessageForPassenger($ride));
    }

    private static function passengerMessageForDriver(Ride $ride): string
    {
        $ride->loadMissing('user');
        $name = $ride->user?->name ?? 'Passenger';
        $phone = $ride->user?->phone ?? '';

        return implode("\n", [
            'Hi — HimCab ride request.',
            'Pickup: '.$ride->pickup_address,
            'Drop: '.$ride->dropoff_address,
            'Est. fare: ₹'.number_format((float) $ride->fare_estimate, 2),
            'Passenger: '.$name.($phone !== '' ? ' ('.$phone.')' : ''),
            'Ride #'.$ride->id,
        ]);
    }

    private static function driverMessageForPassenger(Ride $ride): string
    {
        $ride->loadMissing('driver');
        $name = $ride->driver?->name ?? 'Driver';
        $plate = $ride->driver?->plate_number ?? '';

        return implode("\n", [
            'Hi — I am your HimCab driver '.$name.'.',
            'Vehicle: '.$plate,
            'On my way for ride #'.$ride->id.'.',
        ]);
    }

    /**
     * Normalize phone for wa.me (digits only, India 10-digit → 91 prefix).
     */
    public static function digitsForWa(?string $phone): string
    {
        if ($phone === null || $phone === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($digits) === 10) {
            return '91'.$digits;
        }

        if (str_starts_with($digits, '91') && strlen($digits) >= 12) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            $digits = ltrim($digits, '0');
            if (strlen($digits) === 10) {
                return '91'.$digits;
            }
        }

        return $digits;
    }
}
