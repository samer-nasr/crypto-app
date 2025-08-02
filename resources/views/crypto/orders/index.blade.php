<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-800 leading-tight ">
            {{ __('Crypto Order Page') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Section 1: Make Order --}}
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-4 text-blue-700">Place Order</h3>
                <form id="orderForm" method="POST" action="{{ route('orders.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Order Type</label>
                        <select name="type" id="orderType" class="w-full border-gray-300 rounded-lg mt-1">
                            <option value="Buy">Buy</option>
                            <option value="Sell">Sell</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Select Coin</label>
                        <select name="coin" id="coin" class="w-full border-gray-300 rounded-lg mt-1">
                            <option value="">Select Coin</option>
                            @foreach ($coins as $coin)
                            <option value="{{ $coin->id }}">{{ $coin->name }} ({{ $coin->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Price (USD)</label>
                        <input type="number" step="0.01" name="price" id="price" class="w-full border-gray-300 rounded-lg mt-1" required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Amount</label>
                        <input type="number" step="0.0001" name="quantity" id="amount" class="w-full border-gray-300 rounded-lg mt-1" required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Total</label>
                        <input type="text" id="total" name="counter_price" class="w-full border-gray-200 bg-gray-100 rounded-lg mt-1" readonly>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Submit Order
                    </button>
                </form>
            </div>

            {{-- Section 2: Order History --}}
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-4 text-green-700">Order History</h3>
                
                {{-- Example Table (replace with dynamic data) --}}
                <table class="w-full text-left border border-gray-200 text-sm table-auto">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-2 border-b">Type</th>
                            <th class="p-2 border-b">Coin</th>
                            <th class="p-2 border-b">Price</th>
                            <th class="p-2 border-b">Amount</th>
                            <th class="p-2 border-b">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Example Data (replace with @foreach) --}}
                        <tr class="hover:bg-gray-50">
                            <td class="p-2 text-green-600 font-semibold">Buy</td>
                            <td class="p-2">BTC</td>
                            <td class="p-2">$30,000</td>
                            <td class="p-2">0.01</td>
                            <td class="p-2">$300</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="p-2 text-red-600 font-semibold">Sell</td>
                            <td class="p-2">ETH</td>
                            <td class="p-2">$2,000</td>
                            <td class="p-2">0.5</td>
                            <td class="p-2">$1,000</td>
                        </tr>
                        {{-- @endforeach --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- jQuery for total calculation --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(function () {
            function updateTotal() {
                const price = parseFloat($('#price').val());
                const amount = parseFloat($('#amount').val());
                const total = (price * amount).toFixed(6);
                $('#total').val(isNaN(total) ? '' : total);
            }
            $('#price, #amount').on('input', updateTotal);
        });
    </script>
</x-app-layout>
