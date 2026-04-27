<div class="space-y-6">

    <x-page-header :title="$holiday ? 'Edit Holiday' : 'Create Holiday'" :description="$holiday ? 'Update holiday information and calendar.' : 'Create a new holiday or non-working day.'" />

    <form wire:submit.prevent="save">
        <flux:card class="space-y-6">

            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="name" type="text" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Date</flux:label>
                <flux:input wire:model="date" type="date" />
                <flux:error name="date" />
            </flux:field>

            <flux:field>
                <flux:checkbox wire:model="is_national" label="National Holiday" />
                <flux:error name="is_national" />
            </flux:field>

            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea wire:model="description" rows="3" />
                <flux:error name="description" />
            </flux:field>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ $holiday ? 'Update' : 'Create' }}
                </flux:button>
            </div>

        </flux:card>
    </form>

</div>
