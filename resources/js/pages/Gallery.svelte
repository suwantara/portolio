<script lang="ts">
    import { Link, usePoll } from '@inertiajs/svelte';
    import ArrowLeft from '@lucide/svelte/icons/arrow-left';
    import Plus from '@lucide/svelte/icons/plus';
    import SeoHead from '@/components/SeoHead.svelte';
    import CursorGlow from '@/components/CursorGlow.svelte';
    import GalleryGrid from '@/components/GalleryGrid.svelte';
    import SocialLinks from '@/components/SocialLinks.svelte';
    import ThemeToggle from '@/components/ThemeToggle.svelte';
    import type { PortfolioContent, PortfolioSeo } from '@/types';

    let {
        portfolio,
        seo,
    }: { portfolio: PortfolioContent; seo: PortfolioSeo } = $props();

    usePoll(5000, { only: ['portfolio'] });

    const profile = $derived(portfolio.profile);
    const socials = $derived(portfolio.socials);
    const cta = $derived(portfolio.cta);
    const items = $derived(portfolio.gallery.filter((item) => item.image));

    const pageSize = 12;
    let visibleCount = $state(pageSize);

    const visibleItems = $derived(items.slice(0, visibleCount));
    const hasMore = $derived(visibleCount < items.length);
    const mailto = $derived(
        `mailto:${profile.email}?subject=${encodeURIComponent(cta.subject)}`,
    );

    function loadMore(): void {
        visibleCount = Math.min(visibleCount + pageSize, items.length);
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
            class="absolute inset-x-0 top-0 h-[720px] bg-[radial-gradient(80%_60%_at_50%_0%,rgba(147,197,253,0.38),transparent_72%)] dark:bg-[radial-gradient(80%_60%_at_50%_0%,rgba(37,99,235,0.2),transparent_72%)]"
        ></div>
        <div
            class="absolute inset-x-0 top-0 h-[720px] bg-[linear-gradient(to_right,rgba(15,23,42,0.045)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,0.045)_1px,transparent_1px)] [background-size:56px_56px] [mask-image:linear-gradient(to_bottom,black,transparent)] dark:bg-[linear-gradient(to_right,rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.05)_1px,transparent_1px)]"
        ></div>
    </div>

    <header class="fixed inset-x-0 top-4 z-50 px-4">
        <nav
            aria-label="Gallery"
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
                    Back to portfolio
                </Link>
                <Link
                    href="/blog"
                    class="rounded-full px-3.5 py-2 transition-colors hover:bg-muted hover:text-foreground"
                >
                    Blog
                </Link>
            </div>

            <div class="flex items-center gap-1.5">
                <ThemeToggle class="liquid-glass-control" />
                <a
                    href={mailto}
                    class="liquid-glass-accent inline-flex h-9 items-center rounded-full px-4 text-sm font-semibold text-white transition hover:brightness-110 focus-visible:ring-2 focus-visible:ring-[#0a66c2] focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                >
                    Let's talk
                </a>
            </div>
        </nav>
    </header>

    <main
        class="mx-auto w-full max-w-6xl px-4 pt-28 pb-12 sm:px-6 sm:pt-32"
    >
        <div
            class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
        >
            <div>
                <p
                    class="font-mono text-[10px] tracking-[0.2em] text-muted-foreground uppercase"
                >
                    Portfolio — Gallery
                </p>
                <h1
                    class="mt-2 text-4xl font-semibold tracking-[-0.035em] sm:text-5xl"
                >
                    Gallery
                </h1>
                <p
                    class="mt-3 max-w-xl text-sm leading-relaxed text-muted-foreground sm:text-[15px]"
                >
                    A collection of frames, studies, and moments from recent
                    work. {items.length} images and counting.
                </p>
            </div>

            <Link
                href="/"
                class="inline-flex h-10 w-fit items-center gap-2 rounded-full border border-border bg-background/70 px-5 text-sm font-semibold text-foreground transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
            >
                <ArrowLeft class="size-4" aria-hidden="true" />
                Back to profile
            </Link>
        </div>

        {#if items.length > 0}
            <GalleryGrid items={visibleItems} columns={3} />

            <div class="mt-8 flex flex-col items-center gap-3">
                <p
                    class="font-mono text-[10px] tracking-[0.16em] text-muted-foreground uppercase"
                >
                    Showing {visibleItems.length} of {items.length}
                </p>

                {#if hasMore}
                    <button
                        type="button"
                        onclick={loadMore}
                        class="inline-flex h-10 items-center gap-2 rounded-full border border-border bg-background/70 px-5 text-sm font-semibold text-foreground transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                    >
                        <Plus class="size-4" aria-hidden="true" />
                        Load more images
                    </button>
                {/if}
            </div>
        {:else}
            <div
                class="rounded-2xl border border-dashed border-border/80 bg-card/50 px-6 py-16 text-center"
            >
                <p class="text-base font-semibold">No images yet.</p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Add gallery images through the portfolio CMS.
                </p>
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
