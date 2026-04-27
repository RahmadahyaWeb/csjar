<?php

// app/Livewire/Pages/Payroll/Form.php

use App\Models\Attendance;
use App\Models\Payroll;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Payroll Detail')] class extends Component
{
    public Payroll $payroll;

    public function mount(Payroll $payroll)
    {
        $this->payroll = $payroll->load('user', 'details');
    }

    public function approve()
    {
        if ($this->payroll->status !== 'draft') {
            return;
        }

        $this->payroll->update([
            'status' => 'approved',
        ]);

        Attendance::where('user_id', $this->payroll->user_id)
            ->whereBetween('date', [
                $this->payroll->start_date,
                $this->payroll->end_date,
            ])
            ->update([
                'is_locked' => true,
            ]);
    }

    public function markAsPaid()
    {
        if ($this->payroll->status !== 'approved') {
            return;
        }

        $this->payroll->update([
            'status' => 'paid',
        ]);
    }
};
