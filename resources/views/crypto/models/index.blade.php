<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-800 leading-tight ">
            {{ __('Machine Learning Models') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">


            <div class="bg-white p-6 rounded-xl shadow-md">
                <table class="w-full text-left border border-gray-200 text-sm table-auto">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-2 border-b">Model Name</th>
                            <th class="p-2 border-b">NB. Records</th>
                            <th class="p-2 border-b">Symbol</th>
                            <th class="p-2 border-b">Last Trained At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($models as $model)
                        <tr class="hover:bg-gray-50">
                            <td class="p-2">{{ $model->model_name}}</td>
                            <td class="p-2">{{ $model->records }}</td>
                            <td class="p-2">{{ $model->symbol }}</td>
                            <td class="p-2">{{ $model->last_record_time ? \Carbon\Carbon::parse($model->last_record_time)->format('Y-m-d') : 'N/A'}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold mb-4 text-blue-700">Train Model</h3>
                    <form id="orderForm" method="POST" action="{{ route('models.store') }}">
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
                            <label class="block font-medium text-gray-700">Select Label Time</label>
                            <select name="label_time" id="label_time" class="w-full border-gray-300 rounded-lg mt-1">
                                <option value="">Select Label Time</option>
                                @foreach ($label_times as $label_time => $time)
                                <option value="{{ $time }}">{{ $label_time }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-gray-700">Last Date</label>
                            <input type="date"  name="last_date" id="last_date" class="w-full border-gray-300 rounded-lg mt-1" >
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Train Model
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</x-app-layout>