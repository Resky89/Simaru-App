@props(['id', 'title', 'maxWidth' => 'sm:max-w-[500px]', 'modalClass' => ''])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden {{ $modalClass }}">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full {{ $maxWidth }} scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="{{ $id }}Content">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268] uppercase">{{ $title }}</h2>
                    <button type="button" class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</div>
