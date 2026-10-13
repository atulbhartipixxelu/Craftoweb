<x-mail::message>
# Ride request sent

Your HimCab ride request is saved. Driver dashboard se accept/reject karega.

**Ride #{{ $ride->id }}** · {{ strtoupper($ride->vehicle_type) }} · **₹{{ number_format((float) $ride->fare_estimate, 2) }}** (estimate) · **{{ $ride->status }}**

---

**Pickup**  
{{ $ride->pickup_address }}

**Drop-off**  
{{ $ride->dropoff_address }}

@if($ride->driver)
**Driver**  
{{ $ride->driver->name }} · {{ $ride->driver->plate_number }} · {{ $ride->driver->phone }}

@if(!empty($passengerToDriverWa))
<x-mail::button :url="$passengerToDriverWa">
WhatsApp your driver
</x-mail::button>

_Use this link on your phone to open WhatsApp with a ready message for your driver._
@endif
@else
We are still matching a driver for you — check **My rides** in the app for updates.
@endif

Thanks for riding with {{ config('app.name') }}.
</x-mail::message>
