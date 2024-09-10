{{-- <x-filament-panels::page.simple> --}}
    <div>
 
        @assets
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.10.1/lottie.min.js"></script>
       
        @endassets
       
       
        <div id="lottie-animation" class="hidden sm:block" style="width: 300px; height: 300px;position: absolute;left:13%;top:2%;"></div>
       
       
        @script
        <script>
            let baseurl = "{{ url('/') }}";
            console.log("baseurl",baseurl+'/assets/Animation - 1725940025355.json')
            let animation = lottie.loadAnimation({
            container: document.getElementById('lottie-animation'), // the container to render the animation
            renderer: 'svg', // use 'svg', 'canvas', or 'html'
            loop: true, // make it loop
            autoplay: true, // start automatically
            path: baseurl+'/assets/Animation - 1725940025355.json' // the path to the animation JSON file
        });
        </script>
        @endscript
       
       
        @if (filament()->hasRegistration())
            <x-slot name="subheading">
                {{ __('filament-panels::pages/auth/login.actions.register.before') }}
       
                {{ $this->registerAction }}
            </x-slot>
        @endif
       
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}
       
        <x-filament-panels::form id="form" wire:submit="authenticate">
            {{ $this->form }}
       
            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>
       
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
       
       </div>
       {{-- </x-filament-panels::page.simple> --}}
       