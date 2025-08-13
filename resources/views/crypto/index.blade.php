<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Crypto Price History') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                @foreach($coin_codes as $coin)
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="font-bold mb-2 text-yellow-500">{{ $coin }} Price Chart</h3>
                    <canvas id="{{ $coin }}" height="150"></canvas>
                </div>
                @endforeach
                <!-- <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="font-bold mb-2 text-yellow-500">BTC Price Chart</h3>
                    <canvas id="BTC" height="150"></canvas>
                </div>
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="font-bold mb-2 text-purple-600">ETH Price Chart</h3>
                    <canvas id="ETH" height="150"></canvas>
                </div> -->
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
                                <td class="px-4 py-2 text-gray-600">{{ \Carbon\Carbon::parse($c['open_time'])->diffForHumans() }}</td>
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- ajax update each minute -->
    <script>
        const coins = @json($coin_codes);
        /**
         * Fetches the chart data from the server.
         *
         * @returns {Promise<Object>}
         */
        function fetchChartData() {
            return $.get('/crypto/data').then(function(response) {
                return response;
            });
        }

        const chartsMap = {}; // Store chart instances here

        function initCharts() {
            // Fetch the initial data
            fetchChartData().then(function(data) {
                // Create the charts dynamically
                $.each(coins, function(index, value) {
                    const coinId = value.toLowerCase();
                    const ctx = $('#' + value)[0].getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data[coinId].labels,
                            datasets: [{
                                label: `${value} Price (USD)`,
                                data: data[coinId].data,
                                borderColor: value === 'BTC' ? '#f59e0b' : '#6366f1', // optional color logic
                                tension: 0.3,
                                fill: false
                            }]
                        }
                    });
                    chartsMap[coinId] = chart; // Save chart reference
                });

                // Set interval to update all charts
                setInterval(function() {
                    fetchChartData().then(function(newData) {
                        $.each(chartsMap, function(coinId, chartInstance) {
                            updateChart(chartInstance, newData[coinId].labels, newData[coinId].data);
                        });
                    });
                }, 60000);
            });
        }

        // Chart update function
        function updateChart(chart, labels, dataset) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = dataset;
            chart.update();
        }

        $(document).ready(initCharts);
    </script>




</x-app-layout>