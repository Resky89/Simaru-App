@if(hasPermission('asset-subcategory:create'))
    <x-modal id="addSubCategoryModal" title="TAMBAH KATEGORI">
        <!-- Form -->
        <div class="p-6">
            <form id="createSubCategoryForm" action="{{ route('categories.store') }}" method="POST" data-no-loading novalidate>
                @csrf
                <div class="space-y-4 max-w-[400px] mx-auto">
                    <!-- Category Dropdown -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">
                            Tipe Aset <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="asset_type" id="add_asset_type"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] appearance-none focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 bg-white cursor-pointer"
                                required>
                                <option value="">Pilih Tipe</option>
                                <option value="medical">Medis</option>
                                <option value="non_medical">Non Medis</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                <svg class="w-4 h-4 text-[#203268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden">Tipe harus dipilih</div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="subcategory_name" id="add_subcategory_name"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Ketik di sini" required>
                        <div class="error-message text-red-500 text-sm mt-1 hidden">Kategori harus diisi</div>
                    </div>

                    <!-- Description Field -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">
                            Deskripsi
                        </label>
                        <textarea name="description" id="add_description"
                            class="w-full p-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Ketik deskripsi di sini" rows="3"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
@endif
