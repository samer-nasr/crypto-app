<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Crypto Price History') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="font-bold mb-2 text-yellow-500">BTC Price Chart</h3>
                    <canvas id="btcChart" height="150"></canvas>
                </div>
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="font-bold mb-2 text-purple-600">ETH Price Chart</h3>
                    <canvas id="ethChart" height="150"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bitcoin Table -->
                @foreach($coins as $coin)
                @php($coinName = $coin->toArray()['data'][0]['coin'])
                <div class="bg-white shadow-xl rounded-2xl p-6">
                    <h3 class="text-xl font-bold mb-4 text-yellow-500">{{ $coinName }} ({{ $coin_codes[$coinName]}}) Price History</h3>
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold">Price (USD)</th>
                                <th class="px-4 py-2 text-left font-semibold">Fetched At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($coin->toArray()['data'] as $c)
                            <tr>
                                <td class="px-4 py-2 text-green-600 font-medium">${{ number_format($c['price'], 2) }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ \Carbon\Carbon::parse($c['created_at'])->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        <!-- {{ $coin->appends(['eth_page' => request('eth_page')])->links() }} -->
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <!-- ajax update each minute -->
    <script>
    /**
     * Fetches the chart data from the server.
     *
     * @returns {Promise<Object>}
     */
    async function fetchChartData() {
        const response = await fetch('/crypto/data');
        const data = await response.json();

        return data;
    }

    /**
     * Updates the chart with new data.
     *
     * @param {Chart} chart The chart to update.
     * @param {Array<string>} labels The new labels.
     * @param {Array<number>} dataset The new dataset.
     */
    function updateChart(chart, labels, dataset) {
        chart.data.labels = labels;
        chart.data.datasets[0].data = dataset;
        chart.update();
    }

    /**
     * Initializes the charts.
     */
    async function initCharts() {
        // Fetch the initial data
        const data = await fetchChartData();

        // Create the charts
        const btcCtx = document.getElementById('btcChart').getContext('2d');
        const ethCtx = document.getElementById('ethChart').getContext('2d');

        const btcChart = new Chart(btcCtx, {
            type: 'line',
            data: {
                labels: data.btc.labels,
                datasets: [{
                    label: 'BTC Price (USD)',
                    data: data.btc.data,
                    borderColor: '#f59e0b',
                    tension: 0.3,
                    fill: false
                }]
            }
        });

        const ethChart = new Chart(ethCtx, {
            type: 'line',
            data: {
                labels: data.eth.labels,
                datasets: [{
                    label: 'ETH Price (USD)',
                    data: data.eth.data,
                    borderColor: '#6366f1',
                    tension: 0.3,
                    fill: false
                }]
            }
        });

        // Refresh every 60 seconds
        setInterval(async () => {
            const newData = await fetchChartData();
            updateChart(btcChart, newData.btc.labels, newData.btc.data);
            updateChart(ethChart, newData.eth.labels, newData.eth.data);
        }, 60000);
    }

    document.addEventListener('DOMContentLoaded', initCharts);
</script>
    



</x-app-layout>