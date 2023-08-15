<?php

use App\Models\Post;
use function Livewire\Volt\{with, state, rules, mount, usesPagination};

usesPagination();

state([
    'numResults' => 6, 
    'results' => null, 
    'total' => '',
    'finished' => false,
    'route_prefix' => 'blg',
    'sortOrder' => 'desc'
]);

mount(function (){
    $this->results = $this->numResults;
    $this->total = Post::count();
});

$loadMore = function(){
    $this->results += $this->numResults;
    if ($this->results >= $this->total) {
        $this->finished = true;
    }
};

with(fn () => [
    'posts' => Post::query()
                ->where('status', 'published')
                ->orderBy('created_at', $this->sortOrder)
                ->with('users')
                ->paginate($this->results)
]);


?>

<x-layouts.app>
    <div class="relative mx-auto bg-black {{ config('blog.styles.container_max_width') }}">
        @if(Request::path() != '/')
            <section class="relative antialiased flex bg-gray-950 w-screen flex-col overflow-hidden">
                
                <header class="relative z-20 mx-auto flex h-24 w-full max-w-7xl items-center justify-between px-6">
                    <div class="relative text-white flex items-center space-x-5">
                        <a href="/" class="flex items-center space-x-2">
                            <svg class="w-auto h-5" viewBox="0 0 948 755" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M947.537 68.78V63.21C947.537 28.333 919.265.06 884.39.06s-63.148 28.272-63.148 63.148v5.572c0 34.875 28.273 63.147 63.148 63.147s63.147-28.272 63.147-63.147Z" fill="white"></path><path d="M884.39 218.292c-34.875 0-63.148 28.272-63.148 63.147v337.096a9.286 9.286 0 0 1-9.286 9.287h-89.489a9.287 9.287 0 0 1-8.35-5.223L430.295 39.426a69.995 69.995 0 0 0-125.946.148L79.074 505.241h.483L17.94 632.335 7.415 653.78c-22.715 46.277 10.97 100.337 62.522 100.337h331.751c42.128 0 71.5-41.791 57.23-81.429-8.688-24.133-31.581-40.223-57.23-40.223H156.689l201.864-415.284c4.073-8.381 16.022-8.359 20.063.038l240.721 500.087a65.005 65.005 0 0 0 58.572 36.811h199.98c38.466 0 69.648-31.183 69.648-69.648v-403.03c0-34.875-28.272-63.147-63.147-63.147Z" fill="white"></path></svg>
                            <span class="text-xs text-gray-800  bg-white px-1.5 py-1 leading-none uppercase font-bold rounded">jobs</span>
                        </a>
                    </div>
                    <div class="relative text-white space-x-5">

                        <a href="/blog" class="text-sm px-5 py-3 hover:bg-white/10 rounded-md">Blog</a>
                        <a href="#_" class="w-auto flex-shrink-0 rounded-md bg-opacity-50 ring-1 ring-white/20 hover:bg-white/10 sm:px-5 px-4 py-3 text-xs sm:text-sm font-medium text-white">Keep Me Posted</a>
                    </div>
                </header>
            </section>
        @endif

        @volt('blog.index')
            <div class="w-full relative">
                <header class="mx-auto max-w-3xl mb-8 md:mb-16 text-center text-white"><h1 class="leading-tighter font-bold font-heading md:text-5xl text-4xl tracking-tighter">The A.I. News Blog</h1><div class="mx-auto dark:text-slate-400 font-medium md:mt-3 mt-2 text-gray-500 text-xl">Find out all the latest news around A.I. We stay up-to-date with the latest technologies and AI news so you don't have to.</div></header>
                <div class="px-10 pb-10 mx-auto lg:max-w-7xl sm:max-w-xl md:max-w-full sm:pb-16">
                    <div class="flex flex-col space-y-10">

                        @foreach ($posts as $post)
                            <article class="mx-auto gap-6 text-white grid max-w-md md:gap-8 md:max-w-none md:grid-cols-2">
                                <a href="/{{ $this->route_prefix }}/{{ $post->slug }}" class="block">
                                    <div class="relative overflow-hidden bg-gray-400 dark:bg-slate-700 h-0 lg:pb-[56.25%] md:h-72 md:pb-[75%] pb-[56.25%] rounded shadow-lg">
                                        @if ($post->image)
                                            <img src="@if(str_starts_with($post->image, 'https') || str_starts_with($post->image, 'http')){{ $post->image }}@else{{ asset('storage/' . $post->image) }}@endif"
                                                height="506.25" width="900" class="rounded bg-gray-400 dark:bg-slate-700 shadow-lg absolute h-full inset-0 mb-6 object-cover w-full"
                                                style="object-fit:cover;object-position:center;max-width:900px;max-height:506.25px;aspect-ratio:1.7777777777777777;width:100%"
                                                alt="{{ $post->title }}">
                                        @else
                                            <div
                                                class="flex items-center justify-center w-full h-56 transition-all duration-300 ease-out sm:h-64 group-hover:scale-110">
                                                <svg class="w-10 h-10 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M19 13H5v-2h14v2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @endif    
                                    </div>
                                </a>
                                <div class="mt-2">
                                    <header>
                                        <div class="mb-1"><span class="text-sm"><svg class="dark:text-gray-400 -mt-0.5 h-3.5 inline-block w-3.5" data-icon="tabler:clock" height="1em" viewBox="0 0 24 24" width="1em"><symbol id="ai:tabler:clock"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0-18 0"></path><path d="M12 7v5l3 3"></path></g></symbol><use xlink:href="#ai:tabler:clock"></use></svg> <time class="inline-block" datetime="Mon Jul 17 2023 00:00:00 GMT+0000 (Coordinated Universal Time)">Jul 17, 2023</time> · <a class="capitalize hover:underline" href="/category/documentation">category</a></span></div>
                                        <h2 class="mb-2 dark:text-slate-300 font-bold font-heading leading-tight sm:text-2xl text-xl"><a class="dark:hover:text-blue-700 duration-200 ease-in hover:text-primary transition" href="/astrowind-template-in-depth">{{ $post->title }}</a></h2>
                                    </header>
                                    <p class="text-muted dark:text-slate-400 flex-grow text-lg">{{ substr(strip_tags($post->body), 0, 200) }}@if(strlen(strip_tags($post->body)) > 200){{ '...' }}@endif</p>
                                    <footer class="mt-5">
                                        <ul class="text-sm">
                                            <li class="mb-2 font-medium bg-gray-700 dark:bg-slate-700 inline-block lowercase mr-2 px-2 py-0.5 rtl:ml-2 rtl:mr-0"><a class="text-muted dark:hover:text-gray-200 dark:text-slate-300 hover:text-primary" href="/tag/astro">tag1</a></li>
                                            <li class="mb-2 font-medium bg-gray-700 dark:bg-slate-700 inline-block lowercase mr-2 px-2 py-0.5 rtl:ml-2 rtl:mr-0"><a class="text-muted dark:hover:text-gray-200 dark:text-slate-300 hover:text-primary" href="/tag/tailwind-css">tag2</a></li>
                                            <li class="mb-2 font-medium bg-gray-700 dark:bg-slate-700 inline-block lowercase mr-2 px-2 py-0.5 rtl:ml-2 rtl:mr-0"><a class="text-muted dark:hover:text-gray-200 dark:text-slate-300 hover:text-primary" href="/tag/front-end">tag3</a></li>
                                        </ul>
                                    </footer>
                                </div>
                            </article>
                        @endforeach

                    </div>

                    <div class="flex items-center justify-center w-full pt-10 sm:pt-16">
                        @if (!$finished)
                            <button wire:click="loadMore"
                                class="inline-flex tracking-wide uppercase text-xs items-center justify-center px-5 py-2.5 font-semibold text-gray-100 hover:text-white bg-gray-800 border border-transparent rounded-md shadow-sm hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">Load
                                More</button>
                        @else
                            <p class="text-sm text-gray-600">No more posts.</p>
                        @endif
                    </div>

                </div>
        @endvolt
    </div>
</x-layouts.app>