<x-layouts.marketing>

    <x-ui.marketing.breadcrumbs :crumbs="[
        [
            'href' => '/blog',
            'text' => 'Blog'
        ],
        [
            'text' => $post->title
        ]
    ]" />

    <article class="relative w-full h-auto mx-auto prose-sm prose md:prose-2xl dark:prose-invert">
        <div class="py-6 mx-auto heading md:py-12 lg:w-full md:text-center">

            <div class="flex flex-col items-center justify-center mt-4 mb-0">
                <h1 class="!mb-0 font-sans text-4xl font-bold heading md:text-6xl dark:text-white md:leading-tight">
                    {{ $post->title }}
                </h1>
            </div>

            <div class="flex items-center justify-center">
                <div class="ml-2">
                    <p class="text-sm text-gray-600 dark:text-gray-500">Posted on {{ $post->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            @if ($post->image)
                <img src="@if(str_starts_with($post->image, 'https') || str_starts_with($post->image, 'http')){{ $post->image }}@else{{ asset('storage/' . $post->image) }}@endif" alt="{{ $post->title }}" class="w-full mx-auto mt-4">
            @endif

            <div class="flex items-center justify-center mt-4 text-left">
                <div class="max-w-full -mt-5 text-lg text-gray-600 whitespace-pre-line dark:text-gray-300">
                    {!! $post->body !!}
                </div>
            </div>
        </div>
    </article>
</x-layouts.marketing>
