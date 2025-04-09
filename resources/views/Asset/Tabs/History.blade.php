<div class="p-0" id="assetHistoryContainer">
    <div class="flex justify-center items-center p-6" id="historyLoading">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#203268]"></div>
        <span class="ml-2 text-gray-600">Loading history data...</span>
    </div>

    <div id="historyContent" class="hidden">
        <!-- History content will be loaded here -->
    </div>

    <div id="historyError" class="hidden p-6 text-center">
        <div class="text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p id="errorMessage">Failed to load asset history.</p>
            <button class="mt-2 px-4 py-2 bg-[#203268] text-white rounded-lg" onclick="loadAssetHistory()">
                Try Again
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAssetHistory();
});

function loadAssetHistory() {
    const assetId = '{{ $asset["asset_id"] ?? "" }}';

    if (!assetId) {
        showError('Asset ID not found.');
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
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!data.status) {
                throw new Error(data.message || 'Failed to load history data');
            }

            renderHistoryData(data.data);

            // Hide loading, show content
            document.getElementById('historyLoading').classList.add('hidden');
            document.getElementById('historyContent').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error loading asset history:', error);
            showError(error.message || 'Failed to load asset history');
        });
}

function renderHistoryData(data) {
    const historyContent = document.getElementById('historyContent');

    if (!data || !data.histories || data.histories.length === 0) {
        historyContent.innerHTML = `
            <div class="p-6 text-center text-gray-500">
                No history records found for this asset.
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
                                        <span class="font-semibold">${history.user?.employee?.name || 'Unknown User'}</span> ${history.message}
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
    let hours = date.getHours();
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12; // Convert 0 to 12

    return `${hours}:${minutes} ${ampm}`;
}

function showError(message) {
    document.getElementById('historyLoading').classList.add('hidden');
    document.getElementById('historyContent').classList.add('hidden');

    const errorElement = document.getElementById('historyError');
    const errorMessageElement = document.getElementById('errorMessage');

    errorMessageElement.textContent = message || 'Failed to load asset history.';
    errorElement.classList.remove('hidden');
}
</script>
