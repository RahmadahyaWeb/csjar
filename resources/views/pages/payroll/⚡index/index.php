<?php

// app/Livewire/Pages/Payroll/Index.php

use App\Models\Payroll;
use App\Models\User;
use App\Services\PayrollService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Payroll')] class extends Component
{
    use WithPagination;

    public $startDate;

    public $endDate;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
    }

    #[Computed]
    public function payrolls()
    {
        return Payroll::with('user')
            ->whereDate('start_date', '>=', $this->startDate)
            ->whereDate('end_date', '<=', $this->endDate)
            ->latest('start_date')
            ->paginate(10);
    }

    public function generate()
    {
        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super_admin');
        })->pluck('id');

        foreach ($users as $userId) {
            app(PayrollService::class)
                ->generatePeriod($userId, $this->startDate, $this->endDate);
        }

        $this->dispatch('notify', 'Payroll generated');
    }
};
