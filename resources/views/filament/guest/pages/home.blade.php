<x-filament-panels::page>

@assets
<link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
@endassets

<div class="hero bg-base-200">
    <div class="hero-content flex-col lg:flex-row-reverse">
      <img
        src="https://img.daisyui.com/images/stock/photo-1635805737707-575885ab0820.webp"
        class="max-w-sm rounded-lg shadow-2xl" />
      <div>
        <h1 class="text-5xl font-bold">Box Office News!</h1>
        <p class="py-6">
            Pengurusan Bisnes Online
            Termudah & Automatik
        </p>
        <a class="btn btn-primary text-white" href="{{ url('/main/register')}}" wire:navigate>Daftar Gratis</a>
      </div>
    </div>
  </div>

  

</x-filament-panels::page>
