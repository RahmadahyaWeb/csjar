{{-- resources/views/pdf/payroll-slip.blade.php --}}

<h2>Payroll Slip</h2>

<p>Name: {{ $payroll->user->name }}</p>
<p>Period: {{ $payroll->start_date }} - {{ $payroll->end_date }}</p>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th>Component</th>
        <th>Type</th>
        <th>Amount</th>
    </tr>

    @foreach ($payroll->details as $d)
        <tr>
            <td>{{ $d->component_name }}</td>
            <td>{{ $d->type }}</td>
            <td>{{ number_format($d->amount) }}</td>
        </tr>
    @endforeach
</table>

<h3>Net Salary: {{ number_format($payroll->net_salary) }}</h3>
