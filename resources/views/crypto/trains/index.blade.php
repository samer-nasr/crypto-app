<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-800 leading-tight ">
            {{ __('Crypto Train Configuration') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-4 text-green-700">Trains List</h3>


                <table class="w-full text-left border border-gray-200 text-sm table-auto">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-2 border-b">Name</th>
                            <th class="p-2 border-b">Features</th>
                            <th class="p-2 border-b">Created At</th>
                            <th class="p-2 border-b">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trains as $train)
                        <tr>
                            <td class="p-2 border-b">{{ $train->name }}</td>
                            <td class="p-2 border-b">
                                @php($features = json_decode($train->features))
                                <ul>
                                    @foreach ($features as $feature)
                                    <li>-{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="p-2 border-b">{{ $train->created_at }}</td>
                            <td class="p-2">
                                <form method="POST" action="{{ route('trains.destroy', $train->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-4 text-blue-700">Add Train Configuration</h3>

                <form id="orderForm" method="POST" action="{{ route('trains.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Train Name</label>
                        <input type="text" name="name" id="name" class="w-full border-gray-300 rounded-lg mt-1" required>
                    </div>

                    @php($features = config('constants.features'))
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Select Features</label>
                        @foreach ($features as $feature)
                        <div class="flex items-center my-2 md:w-1/4">
                            <input type="checkbox" name="features[]" value="{{ $feature }}" id="feature-{{ $feature }}" class="border-gray-300  mt-1 me-2">
                            <label for="feature-{{ $feature }}">{{ $feature }}</label>
                        </div>
                        @endforeach
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Create Train
                    </button>
                </form>
            </div>

        </div>
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-4 text-green-700">Data Labeling History</h3>


                <table class="w-full text-left border border-gray-200 text-sm table-auto">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-2 border-b">Symbol</th>
                            <th class="p-2 border-b">Threshold</th>
                            <th class="p-2 border-b">Labeled At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($labeled_datas as $labeled)
                        <tr>
                            <td class="p-2 border-b">{{ $labeled->symbol }}</td>
                            <td class="p-2 border-b">{{ $labeled->threshold }}</td>
                            <td class="p-2 border-b">{{ \Carbon\Carbon::parse($labeled->created_at)->timezone('Asia/beirut')->format('d-m-Y H:i')  }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-4 text-blue-700">Add Label Configuration</h3>

                <form id="labelForm" method="POST" action="{{ route('labels.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Select Symbol</label>
                        <select name="symbol" id="symbol" class="w-full border-gray-300 rounded-lg mt-1">
                            <option value="">Select Symbol</option>
                            @foreach ($symbols as $symbol)
                            <option value="{{ $symbol->symbol }}">{{ $symbol->symbol }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Threshold</label>
                        <input type="number" step="0.001" name="threshold" id="threshold" class="w-full border-gray-300 rounded-lg mt-1" required>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Label Data
                    </button>
                </form>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>

    </script>
</x-app-layout>