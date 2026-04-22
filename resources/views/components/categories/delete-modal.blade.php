@if(hasPermission('asset-subcategory:delete'))
    <x-modal id="deleteSubCategoryModal" title="HAPUS KATEGORI">
        <!-- Form -->
        <div class="p-6">
            <div class="space-y-6 max-w-[400px] mx-auto">
                <div class="flex flex-col items-center">
                    <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus kategori
                        ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="flex gap-3">
                    <button
                        class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                        Batal
                    </button>
                    <form id="deleteSubCategoryForm" action="" method="POST" data-no-loading class="w-1/2">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="deleteSubCategoryId" name="subcategory_id">
                        <button type="submit"
                            class="w-full h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </x-modal>
@endif
