<?php

use App\Models\EmployeeAssignment;
use App\Models\UserFace;
use App\Services\AttendanceService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('My Attendance')] class extends Component
{
    public $latitude;

    public $longitude;

    public function mount()
    {
        if (! UserFace::where('user_id', auth()->id())->exists()) {
            return redirect()->route('face.setup');
        }
        // init kosong, akan diisi dari JS
        $this->latitude = null;
        $this->longitude = null;
    }

    #[Computed()]
    public function state()
    {
        return app(AttendanceService::class)
            ->getState(Auth::id());
    }

    #[Computed()]
    public function assignment()
    {
        return EmployeeAssignment::with('branch')
            ->where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();
    }

    #[Computed()]
    public function officeLat()
    {
        return $this->assignment?->branch?->latitude;
    }

    #[Computed()]
    public function officeLng()
    {
        return $this->assignment?->branch?->longitude;
    }

    #[Computed()]
    public function officeRadius()
    {
        return $this->assignment?->branch?->radius;
    }

    #[Computed()]
    public function distance()
    {
        if (! $this->latitude || ! $this->longitude) {
            return 0;
        }

        if (! $this->officeLat || ! $this->officeLng) {
            return 0;
        }

        return $this->calculateDistance(
            $this->latitude,
            $this->longitude,
            $this->officeLat,
            $this->officeLng
        );
    }

    public function setLocation($lat, $lng)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
    }

    public function checkIn()
    {
        try {

            if (! $this->latitude || ! $this->longitude) {
                throw new Exception('Waiting for GPS location...');
            }

            app(AttendanceService::class)
                ->checkIn(Auth::id(), $this->latitude, $this->longitude);

            Flux::toast(
                heading: 'Success',
                text: 'Check-in successful',
                variant: 'success'
            );

        } catch (Throwable $e) {
            Flux::toast(
                heading: 'Failed',
                text: $e->getMessage(),
                variant: 'danger'
            );
        }
    }

    public function checkOut()
    {
        try {

            if (! $this->latitude || ! $this->longitude) {
                throw new Exception('Waiting for GPS location...');
            }

            app(AttendanceService::class)
                ->checkOut(Auth::id(), $this->latitude, $this->longitude);

            Flux::toast(
                heading: 'Success',
                text: 'Check-out successful',
                variant: 'success'
            );

        } catch (Throwable $e) {
            Flux::toast(
                heading: 'Failed',
                text: $e->getMessage(),
                variant: 'danger'
            );
        }
    }

    public function startBreak()
    {
        try {

            if (! $this->latitude || ! $this->longitude) {
                throw new Exception('Waiting for GPS location...');
            }

            app(AttendanceService::class)
                ->startBreak(Auth::id(), $this->latitude, $this->longitude);

            Flux::toast(
                heading: 'Success',
                text: 'Break started',
                variant: 'success'
            );

        } catch (Throwable $e) {
            Flux::toast(
                heading: 'Failed',
                text: $e->getMessage(),
                variant: 'danger'
            );
        }
    }

    public function endBreak()
    {
        try {

            if (! $this->latitude || ! $this->longitude) {
                throw new Exception('Waiting for GPS location...');
            }

            app(AttendanceService::class)
                ->endBreak(Auth::id(), $this->latitude, $this->longitude);

            Flux::toast(
                heading: 'Success',
                text: 'Break ended',
                variant: 'success'
            );

        } catch (Throwable $e) {
            Flux::toast(
                heading: 'Failed',
                text: $e->getMessage(),
                variant: 'danger'
            );
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) *
            pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }
};
