<x-layouts.app>
    <article class="relative w-full h-screen bg-white prose prose-lg mx-auto">
        <div class="heading py-6 md:py-12 lg:w-10/12 md:text-center mx-auto">

            <div class="flex items-center justify-center mt-4">
                <h1 class="heading text-4xl md:text-6xl font-bold font-sans md:leading-tight">
                    {{ $post->title }}
                </h1>

                <h2 class="text-xl text-gray-600 mt-2">{{ $post->excerpt }}</h2>
            </div>
            @if ($post->image)
                <img src="@if(str_starts_with($post->image, 'https') || str_starts_with($post->image, 'http')){{ $post->image }}@else{{ asset('storage/' . $post->image) }}@endif" alt="{{ $post->title }}" class="w-full mt-4 mx-auto">
            @endif

            <div class="flex items-center justify-center mt-4">
                <div class="ml-2">
                    <p class="text-gray-600 text-sm">Posted on {{ $post->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="flex items-center justify-center mt-4">
                <div class="mt-4 text-lg text-gray-600">
                    {!! $post->body !!}
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
