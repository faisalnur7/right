<div id="productSkeleton" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    @for ($i = 0; $i < 8; $i++)
        <div class="card bg-white shadow-xl animate-pulse">
            <figure class="px-4 pt-10 flex justify-center">
                <div class="bg-gray-300 rounded-xl w-full h-50"></div>
            </figure>
            <div class="card-body flex flex-col items-center text-center">
                <div class="h-4 bg-gray-300 rounded w-3/4 mb-4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/3 mb-4"></div>
                <div class="card-actions mt-3 flex gap-2">
                    <div class="bg-gray-300 rounded w-24 h-8"></div>
                    <div class="bg-gray-300 rounded w-24 h-8"></div>
                </div>
            </div>
        </div>
    @endfor
</div>
