<div>
    <input type="text" wire:model="username">
    <ul>
        @foreach ($users as $user)
            <li>{{ $user->username }}</li>
        @endforeach
    </ul>
</div>
