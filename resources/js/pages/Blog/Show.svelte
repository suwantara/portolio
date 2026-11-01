<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ArrowLeft from '@lucide/svelte/icons/arrow-left';
    import ArrowRight from '@lucide/svelte/icons/arrow-right';
    import CursorGlow from '@/components/CursorGlow.svelte';
    import SeoHead from '@/components/SeoHead.svelte';
    import SocialLinks from '@/components/SocialLinks.svelte';
    import ThemeToggle from '@/components/ThemeToggle.svelte';
    import type { BlogPost, PortfolioContent, PortfolioSeo } from '@/types';

    let {
        portfolio,
        post,
        seo,
    }: {
        portfolio: PortfolioContent;
        post: BlogPost;
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
        month: 'long',
        year: 'numeric',
    });

    const categoryLabels: Record<string, string> = {
        tutorial: 'Tutorial',
        tips: 'Tips',
        progress: 'Progress',
        notes: 'Notes',
    };

    function formatDate(value: string | null): string {
        return value ? dateFormatter.format(new Date(value)) : '';
    }
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
            class="absolute inset-x-0 top-0 h-[620px] bg-[radial-gradient(80%_60%_at_50%_0%,rgba(147,197,253,0.38),transparent_72%)] dark:bg-[radial-gradient(80%_60%_at_50%_0%,rgba(37,99,235,0.2),transparent_72%)]"
        ></div>
        <div
            class="absolute inset-x-0 top-0 h-[620px] bg-[linear-gradient(to_right,rgba(15,23,42,0.045)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,0.045)_1px,transparent_1px)] [background-size:56px_56px] [mask-image:linear-gradient(to_bottom,black,transparent)] dark:bg-[linear-gradient(to_right,rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.05)_1px,transparent_1px)]"
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
                    class="rounded-full px-3.5 py-2 transition-colors hover:bg-muted hover:text-foreground"
                >
                    Portfolio
                </Link>
                <Link
                    href="/gallery"
                    class="rounded-full px-3.5 py-2 transition-colors hover:bg-muted hover:text-foreground"
                >
                    Gallery
                </Link>
                <Link
                    href="/blog"
                    class="rounded-full bg-muted px-3.5 py-2 text-foreground"
                >
                    Blog
                </Link>
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

    <main class="mx-auto w-full max-w-4xl px-4 pt-28 pb-12 sm:px-6 sm:pt-32">
        <Link
            href="/blog"
            class="inline-flex items-center gap-1.5 text-xs font-medium text-muted-foreground transition-colors hover:text-foreground"
        >
            <ArrowLeft class="size-3.5" aria-hidden="true" />
            Back to blog
        </Link>

        <article class="mt-8">
            <header>
                <span
                    class="inline-flex rounded-full bg-[#0a66c2]/10 px-3 py-1 text-[10px] font-semibold tracking-[0.14em] text-[#0a66c2] uppercase dark:bg-sky-400/10 dark:text-sky-300"
                >
                    {categoryLabels[post.category] ?? post.category}
                </span>
                <h1
                    class="mt-4 text-4xl leading-[1.08] font-semibold tracking-[-0.04em] sm:text-5xl"
                >
                    {post.title}
                </h1>
                {#if post.excerpt}
                    <p
                        class="mt-4 text-base leading-relaxed text-muted-foreground sm:text-lg"
                    >
                        {post.excerpt}
                    </p>
                {/if}
                <div
                    class="mt-5 flex flex-wrap items-center gap-x-3 gap-y-1.5 border-y border-border/60 py-3 text-xs text-muted-foreground"
                >
                    <span>{profile.name}</span>
                    <span aria-hidden="true">·</span>
                    <span>{formatDate(post.published_at)}</span>
                    <span aria-hidden="true">·</span>
                    <span>{post.reading_minutes} min read</span>
                </div>
            </header>

            {#if post.cover_image}
                <img
                    src={post.cover_image}
                    alt={post.title}
                    class="mt-7 aspect-[16/9] w-full rounded-2xl border border-border/70 object-cover shadow-sm"
                />
            {/if}

            <div class="blog-content mt-8">
                {@html post.html}
            </div>

            <footer
                class="mt-12 rounded-2xl border border-border/70 bg-card/80 p-6 sm:flex sm:items-center sm:justify-between sm:gap-6 sm:p-7"
            >
                <div>
                    <p class="text-sm font-semibold">Have a question?</p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Send a note — I usually reply within a day.
                    </p>
                </div>
                <a
                    href={mailto}
                    class="mt-4 inline-flex h-10 items-center gap-2 rounded-full bg-[#0a66c2] px-5 text-sm font-semibold text-white transition-colors hover:bg-[#0958a8] sm:mt-0"
                >
                    Email me
                    <ArrowRight class="size-4" aria-hidden="true" />
                </a>
            </footer>
        </article>
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
