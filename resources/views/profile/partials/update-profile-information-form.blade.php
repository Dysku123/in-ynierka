<section>
    <h2 class="subheading">{{ __('Profile Information') }}</h2>
    <p class="muted mb-4">{{ __("Update your account's profile information and email address.") }}</p>

    <form method="post" action="{{ route('profile.update') }}" class="form">
        @csrf
        @method('patch')

        <div class="form-group">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="form-check">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <span class="muted">{{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>
