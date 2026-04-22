<x-modal id="addBrandModal" title="TAMBAH MERK">
    <!-- Form -->
    <div class="p-6">
        <form action="{{ route('brands.store') }}" method="POST" id="addBrandForm" data-no-loading novalidate>
            @csrf
            <div class="space-y-4 max-w-[400px] mx-auto">
                <!-- Brand Input -->
                <div class="space-y-2">
                    <label class="block text-base font-semibold text-[#666666]">
                        Nama Merk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="brand_name" id="add_brand_name" required
                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                        placeholder="Ketik di sini">
                    <div class="error-message text-red-500 text-sm mt-1 hidden">Nama Merk harus diisi
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-modal>
