<div class="space-y-6">

    <x-page-header :title="$branch ? 'Edit Branch' : 'Create Branch'" :description="$branch
        ? 'Update branch information including location and operational radius.'
        : 'Create a new branch with location coordinates and operational radius.'" />

    <form wire:submit.prevent="save">
        <flux:card class="space-y-6">

            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="name" type="text" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Code</flux:label>
                <flux:input wire:model="code" type="text" />
                <flux:error name="code" />
            </flux:field>

            <flux:field>
                <flux:label>Latitude</flux:label>
                <flux:input wire:model="latitude" type="text" />
                <flux:error name="latitude" />
            </flux:field>

            <flux:field>
                <flux:label>Longitude</flux:label>
                <flux:input wire:model="longitude" type="text" />
                <flux:error name="longitude" />
            </flux:field>

            <flux:field>
                <flux:label>Radius (meters)</flux:label>
                <flux:input wire:model="radius" type="number" />
                <flux:error name="radius" />
            </flux:field>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ $branch ? 'Update' : 'Create' }}
                </flux:button>
            </div>

        </flux:card>
    </form>

</div>
