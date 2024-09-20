<div>
    @assets
    <style>

#loading {
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 9999;
    width: 100vw;
    height: 100vh;
    background-color: rgba(192, 192, 192, 0.5);
    background-image: url("{{ url('/assets/hourglass.svg') }}");
    background-repeat: no-repeat;
    background-position: center;s
}

    </style>
    @endassets
    <div id="loading" class="preloader" wire:ignore></div>


    @script
    <script>

            Livewire.on('preloader', ({ postId }) => {
                    setTimeout(() =>{
                        document.querySelector('.preloader').style.display = 'none';
                    }, 500)
                });

            document.addEventListener('livewire:navigate', (event) => {
                document.querySelector('.preloader').style.display = 'block';
            }) 
            document.addEventListener('livewire:navigated', () => {
                setTimeout(() =>{
                        document.querySelector('.preloader').style.display = 'none';
                    }, 500)
            })

    </script>
    @endscript
</div>