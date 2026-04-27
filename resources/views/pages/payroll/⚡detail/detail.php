<?php

// app/Livewire/Pages/Payroll/Form.php

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
