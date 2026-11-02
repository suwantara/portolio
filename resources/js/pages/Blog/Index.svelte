<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ArrowRight from '@lucide/svelte/icons/arrow-right';
    import CursorGlow from '@/components/CursorGlow.svelte';
    import SeoHead from '@/components/SeoHead.svelte';
    import SocialLinks from '@/components/SocialLinks.svelte';
    import ThemeToggle from '@/components/ThemeToggle.svelte';
    import { cn } from '@/lib/utils';
    import type {
        BlogPostSummary,
        Paginated,
        PortfolioContent,
        PortfolioSeo,
    } from '@/types';

    let {
        portfolio,
        posts,
        seo,
    }: {
        portfolio: PortfolioContent;
        posts: Paginated<BlogPostSummary>;
        seo: PortfolioSeo;
    } = $props();

    const profile = $derived(portfolio.profile);
    const socials = $derived(portfolio.socials);
    const cta = $derived(portfolio.cta);
    const mailto = $derived(
        `mailto:${profile.email}?subject=${encodeURIComponent(cta.subject)}`,
    );

    const dateFormatter = new Intl.DateTimeFormat('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });

    const categoryLabels: Record<string, string> = {
        tutorial: 'Tutorial',
        tips: 'Tips',
        progress: 'Progress',
        notes: 'Notes',
    };

    const categoryStyles: Record<string, string> = {
        tutorial: 'bg-sky-500/10 text-sky-700 dark:text-sky-300',
        tips: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300',
        progress: 'bg-amber-500/10 text-amber-700 dark:text-amber-300',
        notes: 'bg-violet-500/10 text-violet-700 dark:text-violet-300',
    };

    function formatDate(value: string | null): string {
        return value ? dateFormatter.format(new Date(value)) : 'Unpublished';
    }

    const previous = $derived(posts.links[0] ?? null);
    const next = $derived(posts.links[posts.links.length - 1] ?? null);
</script>

<SeoHead {seo} />

<div
    class="relative isolate min-h-screen overflow-x-clip bg-[#f7f8fa] text-foreground dark:bg-[#07080c]"
>
    <CursorGlow />

    <div
        class="pointer-events-none absolute inset-0 -z-10 overflow-hidden"
        aria-hidden="true"
    >
        <div
            class="absolute inset-x-0 top-0 h-[760px] bg-[radial-gradient(80%_60%_at_50%_0%,rgba(147,197,253,0.38),transparent_72%)] dark:bg-[radial-gradient(80%_60%_at_50%_0%,rgba(37,99,235,0.2),transparent_72%)]"
        ></div>
        <div
            class="absolute inset-x-0 top-0 h-[760px] bg-[linear-gradient(to_right,rgba(15,23,42,0.045)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,0.045)_1px,transparent_1px)] [background-size:56px_56px] [mask-image:linear-gradient(to_bottom,black,transparent)] dark:bg-[linear-gradient(to_right,rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.05)_1px,transparent_1px)]"
        ></div>
    </div>

    <header class="fixed inset-x-0 top-4 z-50 px-4">
        <nav
            aria-label="Blog"
            class="liquid-glass mx-auto flex h-14 w-full max-w-6xl items-center justify-between gap-3 rounded-full pr-2 pl-3 sm:pl-4"
        >
            <Link
                href="/"
                class="flex items-center gap-2.5 rounded-full focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
            >
                <span
                    class="grid size-8 place-items-center rounded-full bg-foreground text-[13px] font-semibold text-background"
                >
                    X
                </span>
                <span class="text-[15px] font-semibold tracking-tight">
                    xuan<span class="text-[#0a66c2] dark:text-sky-400">.</span>
                </span>
            </Link>

            <div
                class="hidden items-center gap-1 text-sm font-medium text-muted-foreground sm:flex"
            >
                <Link
                    href="/"
                    class="rounded-full px-3.5 py-2 transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                >
                    Portfolio
                </Link>
                <Link
                    href="/gallery"
                    class="rounded-full px-3.5 py-2 transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                >
                    Gallery
                </Link>
                <span
                    class="rounded-full bg-muted px-3.5 py-2 text-foreground"
                    aria-current="page"
                >
                    Blog
                </span>
            </div>

            <div class="flex items-center gap-1.5">
                <ThemeToggle class="liquid-glass-control" />
                <a
                    href={mailto}
                    class="liquid-glass-accent inline-flex h-9 items-center gap-1.5 rounded-full px-4 text-sm font-semibold text-white transition hover:brightness-110 focus-visible:ring-2 focus-visible:ring-[#0a66c2] focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                >
                    Let's talk
                    <ArrowRight class="size-3.5" aria-hidden="true" />
                </a>
            </div>
        </nav>
    </header>

    <main class="mx-auto w-full max-w-6xl px-4 pt-28 pb-12 sm:px-6 sm:pt-32">
        <header class="mb-8 max-w-2xl">
            <p
                class="font-mono text-[10px] tracking-[0.2em] text-muted-foreground uppercase"
            >
                Portfolio — Journal
            </p>
            <h1
                class="mt-2 text-4xl font-semibold tracking-[-0.035em] sm:text-5xl"
            >
                Notes &amp; tutorials
            </h1>
            <p
                class="mt-3 max-w-xl text-sm leading-relaxed text-muted-foreground sm:text-[15px]"
            >
                Practical write-ups about building products, solving problems,
                and shipping projects from the first commit to the last detail.
            </p>
        </header>

        {#if posts.data.length > 0}
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                {#each posts.data as post (post.id)}
                    <Link
                        href={`/blog/${post.slug}`}
                        class="group flex min-w-0 flex-col overflow-hidden rounded-2xl border border-border/70 bg-card/80 shadow-[0_16px_50px_-40px_rgba(15,23,42,0.45)] backdrop-blur-sm transition duration-300 hover:-translate-y-1 hover:border-[#0a66c2]/30 hover:shadow-[0_20px_55px_-30px_rgba(10,102,194,0.35)] focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none dark:hover:border-sky-400/30"
                    >
                        <div
                            class="relative aspect-[16/9] overflow-hidden border-b border-border/60 bg-[radial-gradient(120%_120%_at_10%_0%,#dbeafe_0%,#eff6ff_50%,#f8fafc_100%)] dark:bg-[radial-gradient(120%_120%_at_10%_0%,#0b1d3f_0%,#0a1224_52%,#07080c_100%)]"
                        >
                            {#if post.cover_image}
                                <img
                                    src={post.cover_image}
                                    alt={`Cover image for ${post.title}`}
                                    class="size-full object-cover transition duration-500 group-hover:scale-[1.03]"
                                    loading="lazy"
                                />
                            {:else}
                                <div
                                    class="absolute inset-0 opacity-70 [background-image:linear-gradient(to_right,rgba(15,23,42,0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,0.06)_1px,transparent_1px)] [background-size:28px_28px] dark:[background-image:linear-gradient(to_right,rgba(255,255,255,0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.06)_1px,transparent_1px)]"
                                ></div>
                            {/if}
                            <span
                                class={cn(
                                    'absolute top-3 left-3 rounded-full px-2.5 py-1 text-[10px] font-semibold tracking-[0.1em] uppercase',
                                    categoryStyles[post.category] ??
                                        'bg-muted text-muted-foreground',
                                )}
                            >
                                {categoryLabels[post.category] ?? post.category}
                            </span>
                        </div>

                        <div class="flex flex-1 flex-col p-5">
                            <div
                                class="flex items-center gap-2 text-[11px] text-muted-foreground"
                            >
                                <span>{formatDate(post.published_at)}</span>
                                <span aria-hidden="true">·</span>
                                <span>{post.reading_minutes} min read</span>
                            </div>
                            <h2
                                class="mt-2 text-lg leading-snug font-semibold tracking-tight transition-colors group-hover:text-[#0a66c2] dark:group-hover:text-sky-400"
                            >
                                {post.title}
                            </h2>
                            <p
                                class="mt-2 line-clamp-3 text-sm leading-relaxed text-muted-foreground"
                            >
                                {post.excerpt || 'Read the full article.'}
                            </p>
                            <span
                                class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-[#0a66c2] dark:text-sky-400"
                            >
                                Read article
                                <ArrowRight
                                    class="size-3.5 transition-transform group-hover:translate-x-0.5"
                                    aria-hidden="true"
                                />
                            </span>
                        </div>
                    </Link>
                {/each}
            </div>

            {#if posts.last_page > 1}
                <div
                    class="mt-8 flex flex-wrap items-center justify-between gap-3 text-sm text-muted-foreground"
                >
                    <p>Page {posts.current_page} of {posts.last_page}</p>
                    <div class="flex items-center gap-2">
                        {#if previous?.url}
                            <Link
                                href={previous.url}
                                class="inline-flex h-9 items-center rounded-full border border-border bg-background/70 px-4 text-sm font-semibold text-foreground transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                            >
                                Previous
                            </Link>
                        {/if}
                        {#if next?.url}
                            <Link
                                href={next.url}
                                class="inline-flex h-9 items-center rounded-full border border-border bg-background/70 px-4 text-sm font-semibold text-foreground transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                            >
                                Next
                            </Link>
                        {/if}
                    </div>
                </div>
            {/if}
        {:else}
            <div
                class="rounded-2xl border border-dashed border-border/80 bg-card/50 px-6 py-16 text-center"
            >
                <p class="text-base font-semibold">No posts published yet.</p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Tutorials and project notes will appear here.
                </p>
                <Link
                    href="/"
                    class="mt-5 inline-flex h-10 items-center rounded-full border border-border bg-background/70 px-5 text-sm font-semibold text-foreground transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                >
                    Back to portfolio
                </Link>
            </div>
        {/if}
    </main>

    <footer
        class="mx-auto flex w-full max-w-6xl flex-col items-center justify-between gap-4 border-t border-border/60 px-4 py-8 text-center sm:flex-row sm:px-6 sm:text-left"
    >
        <p class="text-xs text-muted-foreground">
            © 2026 {profile.name}. {portfolio.footer_note}
        </p>
        <SocialLinks {socials} size="sm" />
    </footer>
</div>
