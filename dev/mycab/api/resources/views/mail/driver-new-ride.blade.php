<x-mail::message>
# New HimCab ride

Passenger ne aapki cab choose ki hai. Driver dashboard me jaakar ride accept ya reject karein.

**Ride #{{ $ride->id }}** · {{ strtoupper($ride->vehicle_type) }} · **₹{{ number_format((float) $ride->fare_estimate, 2) }}** (estimate)

---

**Pickup**  
{{ $ride->pickup_address }}

**Drop-off**  
{{ $ride->dropoff_address }}

@if($ride->distance_km)
Approx. distance: **{{ number_format((float) $ride->distance_km, 1) }} km**
@endif

---

**Passenger**  
{{ $ride->user?->name ?? '—' }}  
@if($ride->user?->email)
{{ $ride->user->email }}
@endif
@if($ride->user?->phone)
Phone: {{ $ride->user->phone }}
@endif

@if(!empty($driverToPassengerWa))
<x-mail::button :url="$driverToPassengerWa">
WhatsApp passenger
</x-mail::button>

_Open this on your phone to start a WhatsApp chat with the passenger (if they shared a phone number on their profile)._
@else
_Passenger has not added a phone number yet — use email reply or call through dispatch if needed._
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
