<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner un livre</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-lg mx-auto my-10 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-center mb-6">Scanner un livre 📚</h1>
        
        <form action="{{ route('process-image') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Photo du livre
                </label>
                
                <input type="file" 
                       name="image" 
                       accept="image/*" 
                       capture="environment"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                       required>
            </div>

            <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Scanner le livre 📸
            </button>
        </form>

        @if(session('error'))
            <div class="mt-4 p-4 text-red-700 bg-red-100 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mt-4 p-4 text-green-700 bg-green-100 rounded-md">
                {{ session('success') }}
            </div>
        @endif
    </div>
</body>
</html> 