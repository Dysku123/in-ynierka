<section>
    <h2 class="subheading">{{ __('Delete Account') }}</h2>
    <p class="muted mb-4">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>

    <form method="post" action="{{ route('profile.destroy') }}" class="form"
        onsubmit="return confirm('{{ __('Are you sure you want to delete your account?') }}')">
        @csrf
        @method('delete')

        <div class="form-group">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" name="password" type="password" placeholder="{{ __('Password') }}" />
            <x-input-error :messages="$errors->userDeletion->get('password')" />
        </div>

        <x-danger-button>{{ __('Delete Account') }}</x-danger-button>
    </form>
</section>
