# DevDojo Blog

A drop-in **blog** for Laravel — posts, categories, and a Filament admin — designed to be
headless on the front-end so it slots into any theme. It ships the `Post` and `Category`
models (with caching), migrations, two Filament resources, and category/author wiring.

`devdojo/blog` is one of the feature packages bundled by
[`devdojo/foundation`](https://github.com/thedevdojo/foundation), but it works perfectly
well **standalone** in any Laravel app.

---

## Table of contents

- [Requirements](#requirements)
- [How it works](#how-it-works)
- [Installation](#installation)
- [Configuration](#configuration)
- [The data model](#the-data-model)
- [Creating posts & categories](#creating-posts--categories)
- [Querying the blog (rendering a front-end)](#querying-the-blog-rendering-a-front-end)
- [Categories & caching](#categories--caching)
- [Authors](#authors)
- [Filament admin](#filament-admin)
- [Factories & seeding](#factories--seeding)
- [Using with DevDojo Foundation](#using-with-devdojo-foundation)
- [Configuration reference](#configuration-reference)
- [FAQ / troubleshooting](#faq--troubleshooting)

---

## Requirements

| Requirement | Notes |
| --- | --- |
| PHP `^8.2` | |
| Laravel `^10 / ^11 / ^12` | |
| `filament/filament` `^4` *(optional)* | Only needed for the bundled Post & Category admin resources. |

The blog is **front-end agnostic**: it provides the models and admin, and leaves the public
pages to your application/theme. There are no required view dependencies.

---

## How it works

```
┌───────────────────────────────────────────────────────────┐
│ devdojo/blog                                              │
│                                                           │
│  Category ──hasMany──▶ Post ──belongsTo──▶ User (your app) │
│      ▲                   │     (author_id)                │
│      └──belongsTo────────┘                                │
│                                                           │
│  Models (Post, Category)  •  Migrations                   │
│  Filament: PostResource + CategoryResource (BlogPlugin)   │
└───────────────────────────────────────────────────────────┘
```

- A **Post** belongs to a **Category** and to an **author** (your `User` model, via
  `author_id`).
- A **Category** can be nested (`parent_id`) and exposes a cached collection helper.
- The package is **headless** for the front-end — you render `/blog` pages however you like
  (Folio, controllers, Livewire) using the models. The bundled Filament resources give you
  the authoring/admin side out of the box.

---

## Installation

```bash
composer require devdojo/blog
```

Publish the migrations and config, then migrate:

```bash
php artisan vendor:publish --tag=blog:migrations
php artisan vendor:publish --tag=blog:config
php artisan migrate
```

> Migrations are **publish-only** (not auto-loaded) so the `posts` and `categories` tables
> live in your app's `database/migrations` and are yours to edit.

If your `User` model isn't discoverable from `config('auth.providers.users.model')`, set it
explicitly so posts can resolve their author:

```env
BLOG_USER_MODEL="App\\Models\\User"
```

Then (optionally) [register the Filament admin](#filament-admin).

---

## Configuration

Publishing `blog:config` writes `config/devdojo/blog/settings.php` (config key
`devdojo.blog.settings`):

```php
return [
    // The host User model used as a post's author. Null → auth.providers.users.model.
    'user_model' => env('BLOG_USER_MODEL'),
];
```

---

## The data model

### `categories`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | increments | |
| `parent_id` | unsigned int, nullable | self-referencing (`onDelete: set null`) |
| `order` | int, default `1` | |
| `name` | string | |
| `slug` | string, **unique** | |
| timestamps | | |

### `posts`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | increments | |
| `author_id` | unsigned big int | FK → `users.id` |
| `category_id` | unsigned int, nullable | FK → `categories.id` (`onDelete: set null`) |
| `title` | string(191) | |
| `seo_title` | string(191), nullable | |
| `excerpt` | text, nullable | |
| `body` | text | |
| `image` | string(191), nullable | path on the configured disk |
| `slug` | string(191), **unique** | |
| `meta_description` | text, nullable | |
| `meta_keywords` | text, nullable | |
| `status` | enum `PUBLISHED` / `DRAFT` / `PENDING` | default `DRAFT` |
| `featured` | boolean | default `false` |
| timestamps | | |

Both models use `$guarded = []` (mass-assignable). The `Post` model's `image()` resolves the
URL on `config('filament.default_filesystem_disk')`.

---

## Creating posts & categories

```php
use Devdojo\Blog\Models\Category;
use Devdojo\Blog\Models\Post;

$marketing = Category::create([
    'name' => 'Marketing',
    'slug' => 'marketing',
    'order' => 1,
]);

$post = Post::create([
    'author_id'   => $user->id,
    'category_id' => $marketing->id,
    'title'       => 'Best ways to market your application',
    'slug'        => 'best-ways-to-market-your-application',
    'excerpt'     => 'A short summary…',
    'body'        => '<p>The full post body (HTML)…</p>',
    'image'       => 'posts/cover.jpg',
    'status'      => 'PUBLISHED',
    'featured'    => true,
]);
```

---

## Querying the blog (rendering a front-end)

The package doesn't impose routes or views — render the blog however your app prefers.
Everything you need is on the models:

```php
use Devdojo\Blog\Models\Post;
use Devdojo\Blog\Models\Category;

// Index: latest published posts, paginated
$posts = Post::where('status', 'PUBLISHED')
    ->orderByDesc('created_at')
    ->paginate(6);

// A single post by slug
$post = Post::where('slug', $slug)->firstOrFail();

// Posts within a category
$category = Category::where('slug', $categorySlug)->firstOrFail();
$posts = $category->posts()->where('status', 'PUBLISHED')->paginate(6);
```

Handy accessors on a `Post`:

```php
$post->link();      // "/blog/{category-slug}/{post-slug}"
$post->image();     // full URL to the cover image (on the default Filament disk)
$post->user;        // the author (User)
$post->category;    // the Category
```

A minimal Folio page example:

```php
// resources/views/pages/blog/index.blade.php
<?php
use function Laravel\Folio\name;
use Devdojo\Blog\Models\Post;

name('blog');
$posts = Post::where('status', 'PUBLISHED')->latest()->paginate(6);
?>

<x-layout>
    @foreach ($posts as $post)
        <article>
            <a href="{{ $post->link() }}">{{ $post->title }}</a>
            <p>{{ $post->excerpt }}</p>
        </article>
    @endforeach
    {{ $posts->links() }}
</x-layout>
```

---

## Categories & caching

`Category` provides a cached collection helper (cached for one hour):

```php
Category::getAllCached();   // Collection of all categories (cached)
Category::clearCache();     // bust the cache after create/update/delete
$category->posts;           // HasMany Post
$category->parent_id;       // nested categories (self-referencing)
```

Use `getAllCached()` for things like a category nav that renders on every page; call
`clearCache()` whenever categories change.

---

## Authors

A post's author is your application's `User` model, related via `author_id`:

```php
$post->user;                  // the author
$user->id === $post->author_id;

// A user's posts (no relation is added to your User model by default):
Post::where('author_id', $user->id)->get();
```

The author model is resolved from `config('devdojo.blog.settings.user_model')`, falling back
to `config('auth.providers.users.model')`.

---

## Filament admin

If you use Filament, register the plugin in your panel to get **Posts** (`/admin/posts`)
and **Categories** (`/admin/categories`) resources:

```php
use Devdojo\Blog\Filament\BlogPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(BlogPlugin::make());
}
```

- **PostResource** — title (auto-slug), body (RichEditor with file attachments), excerpt,
  cover image upload, SEO title, author & category selects, meta fields, status, and
  featured toggle.
- **CategoryResource** — name, slug, parent category, and order.

Filament is an optional dependency — the package's models/migrations work without it.

---

## Factories & seeding

The models support Laravel factories. `Post::factory()` and `Category::factory()` resolve to
`Database\Factories\PostFactory` and `Database\Factories\CategoryFactory` — the standard app
factory namespace — so define them there (or publish your own) to seed demo content:

```php
Post::factory()->count(10)->create();
Category::factory()->create(['name' => 'Tutorials', 'slug' => 'tutorials']);
```

A `PostFactory` typically assigns a random existing `User` as `author_id` and a random
`Category` as `category_id`, and sets `status => 'PUBLISHED'`.

---

## Using with DevDojo Foundation

When the [`devdojo/foundation`](https://github.com/thedevdojo/foundation) metapackage is
installed, the blog's **Filament admin self-gates** on its feature flag:

```php
// config/foundation.php
'features' => [
    'blog' => true,   // flip to false (or toggle at /foundation/setup) to hide the admin
],
```

When `blog` is disabled, the Post & Category Filament resources are not registered. The
models, migrations, and any front-end pages you built remain available (your front-end
pages are app-owned, so gate them in your own routing if desired). Migrations always run, so
toggling is lossless.

Standalone (no Foundation present), the flag is absent and the blog defaults to **on**.

---

## Configuration reference

### `config/devdojo/blog/settings.php`

```php
return [
    'user_model' => env('BLOG_USER_MODEL'), // null → auth.providers.users.model
];
```

### Publish tags

| Tag | Publishes to |
| --- | --- |
| `blog:config` | `config/devdojo/blog/settings.php` |
| `blog:migrations` | `database/migrations` |

---

## FAQ / troubleshooting

**Where are the `/blog` pages?**
The package is headless on the front-end. Render the pages with the models (see
[Querying the blog](#querying-the-blog-rendering-a-front-end)); in the Wave starter kit they
ship as theme Folio pages you can edit.

**`$post->image()` returns the wrong URL.**
It uses `config('filament.default_filesystem_disk')`. Set that disk (e.g. `public`) and run
`php artisan storage:link`.

**Author select is empty / posts have no author.**
Ensure `config('devdojo.blog.settings.user_model')` (or `auth.providers.users.model`) points
at a real model and that users exist.

**Where do `posts` / `categories` tables come from?**
They're publish-only migrations — run
`php artisan vendor:publish --tag=blog:migrations && php artisan migrate`.

---

## License

MIT © [DevDojo](https://devdojo.com)
