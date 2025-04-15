<div class="p-0" id="assetHistoryContainer">
    <div class="flex justify-center items-center p-6" id="historyLoading">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#203268]"></div>
        <span class="ml-2 text-gray-600">Memuat data riwayat...</span>
    </div>

    <div id="historyContent" class="hidden">
        <!-- History content will be loaded here -->
    </div>

    <div id="historyError" class="hidden p-6 text-center">
        <div class="text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p id="errorMessage">Gagal memuat data riwayat.</p>
            <button class="mt-2 px-4 py-2 bg-[#203268] text-white rounded-lg" onclick="loadAssetHistory()">
                Coba Lagi
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        loadAssetHistory();
    });

    function loadAssetHistory() {
        const assetId = '{{ $asset["asset_id"] ?? "" }}';

        if (!assetId) {
            showError('ID Aset tidak ditemukan.');
            return;
        }

        // Show loading, hide other elements
        document.getElementById('historyLoading').classList.remove('hidden');
        document.getElementById('historyContent').classList.add('hidden');
        document.getElementById('historyError').classList.add('hidden');

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Fetch asset history data using the full URL
        fetch('{{ url("/asset-histories") }}/' + assetId, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Respons jaringan tidak baik');
                }
                return response.json();
            })
            .then(data => {
                if (!data.status) {
                    throw new Error(data.message || 'Gagal memuat data riwayat');
                }

                renderHistoryData(data.data);

                // Hide loading, show content
                document.getElementById('historyLoading').classList.add('hidden');
                document.getElementById('historyContent').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error loading asset history:', error);
                showError(error.message || 'Gagal memuat riwayat aset');
            });
    }

    function renderHistoryData(data) {
        const historyContent = document.getElementById('historyContent');

        if (!data || !data.histories || data.histories.length === 0) {
            historyContent.innerHTML = `
            <div class="p-6 text-center text-gray-500">
                Tidak ada riwayat untuk asset ini.
            </div>
        `;
            return;
        }

        // Group histories by date
        const groupedHistories = groupHistoriesByDate(data.histories);

        // Generate HTML for grouped histories
        let html = '';

        for (const [date, histories] of Object.entries(groupedHistories)) {
            const { day, month, year } = formatDate(date);

            html += `
            <div class="flex border-b">
                <!-- Date sidebar -->
                <div class="w-20 bg-white flex flex-col items-center justify-start py-4 border-r">
                    <div class="text-3xl font-bold">${day}</div>
                    <div class="text-sm text-gray-500">${month}/${year}</div>
                </div>

                <!-- History entries -->
                <div class="flex-1 bg-white">
                    ${histories.map(history => {
                const time = formatTime(history.action_date);
                return `
                            <div class="border-b py-3 px-4">
                                <div class="flex items-start">
                                    <div class="w-20 text-sm text-gray-500">${time}</div>
                                    <div class="flex-1">
                                        <span class="font-semibold">${history.user?.employee?.name || 'Pengguna Tidak Dikenal'}</span> ${translateStatusMessage(history.message)}
                                    </div>
                                </div>
                            </div>
                        `;
            }).join('')}
                </div>
            </div>
        `;
        }

        historyContent.innerHTML = html;
    }

    function groupHistoriesByDate(histories) {
        const grouped = {};

        histories.forEach(history => {
            const date = new Date(history.action_date).toISOString().split('T')[0];

            if (!grouped[date]) {
                grouped[date] = [];
            }

            grouped[date].push(history);
        });

        // Sort dates in descending order (newest first)
        return Object.keys(grouped)
            .sort((a, b) => new Date(b) - new Date(a))
            .reduce((obj, key) => {
                obj[key] = grouped[key];
                return obj;
            }, {});
    }

    function formatDate(dateString) {
        const date = new Date(dateString);

        return {
            day: date.getDate().toString().padStart(2, '0'),
            month: (date.getMonth() + 1).toString().padStart(2, '0'),
            year: date.getFullYear().toString().slice(-2)
        };
    }

    function formatTime(dateString) {
        const date = new Date(dateString);
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');

        return `${hours}:${minutes}`;
    }

    function translateStatusMessage(message) {
        // Colors based on AssetDetail.blade.php
        const statusColors = {
            'Tersedia': '#659B09',      // Available - green
            'Dipinjam': '#F59E0B',      // Checked out - amber/yellow
            'Hilang': '#EF4444',        // Lost - red
            'Ditemukan': '#659B09',     // Found - green (same as available)
            'Dihapuskan': '#ACC3EF'     // Dispose - light blue
        };

        // Translate status terms first
        let translatedMessage = message
            .replace(/changed status from/g, 'mengubah status dari')
            .replace(/to/g, 'menjadi')
            .replace(/Checked out/gi, 'Dipinjam')
            .replace(/check out/gi, 'Dipinjam')
            .replace(/Returned/gi, 'Tersedia')
            .replace(/Available/gi, 'Tersedia')
            .replace(/Lost/gi, 'Hilang')
            .replace(/Found/gi, 'Ditemukan')
            .replace(/Disposed/gi, 'Dihapuskan')
            .replace(/Admin System/g, 'Sistem Admin');

        // Apply colors to status terms
        Object.keys(statusColors).forEach(status => {
            const regex = new RegExp(`\\b${status}\\b`, 'g');
            translatedMessage = translatedMessage.replace(
                regex,
                `<span style="color: ${statusColors[status]}; font-weight: bold;">${status}</span>`
            );
        });

        return translatedMessage;
    }

    function showError(message) {
        document.getElementById('historyLoading').classList.add('hidden');
        document.getElementById('historyContent').classList.add('hidden');

        const errorElement = document.getElementById('historyError');
        const errorMessageElement = document.getElementById('errorMessage');

        errorMessageElement.textContent = message || 'Gagal memuat riwayat aset.';
        errorElement.classList.remove('hidden');
    }
</script>