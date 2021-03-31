<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Activities') }}

            <form action="{{ route('activity.sync', 'strava') }}" class="inline float-right">
              @csrf
              <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-600 focus:outline-none focus:border-yellow-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">@lang('Sync Strava')</button>
            </form>
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
    </div>

</div>
</x-app-layout>
