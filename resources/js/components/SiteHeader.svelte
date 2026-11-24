<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ArrowRight from '@lucide/svelte/icons/arrow-right';
    import Menu from '@lucide/svelte/icons/menu';
    import X from '@lucide/svelte/icons/x';
    import BaliClockRails from '@/components/BaliClockRails.svelte';
    import ThemeToggle from '@/components/ThemeToggle.svelte';
    import { cn } from '@/lib/utils';
    import type { PortfolioContent } from '@/types';

    type Page = 'home' | 'gallery' | 'blog' | 'post' | 'work';

    let {
        portfolio,
        current = 'home',
    }: { portfolio: PortfolioContent; current?: Page } = $props();

    const profile = $derived(portfolio.profile);
    const mailto = $derived(
        `mailto:${profile.email}?subject=${encodeURIComponent(portfolio.cta.subject)}`,
    );

    const navLinks = $derived([
        {
            label: 'About',
            href: current === 'home' ? '#about' : '/#about',
        },
        {
            label: 'Experience',
            href: current === 'home' ? '#experience' : '/#experience',
        },
        { label: 'Work', href: '/work' },
        { label: 'Gallery', href: '/gallery' },
        { label: 'Blog', href: '/blog' },
        {
            label: 'Contact',
            href: current === 'home' ? '#contact' : '/#contact',
        },
    ]);

    const navItem =
        'rounded-full px-3.5 py-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground dark:hover:text-white focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none';
    const mobileNavItem =
        'block rounded-xl px-4 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground dark:hover:text-white focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none';

    let menuOpen = $state(false);
    let scrolled = $state(false);

    function handleScroll(): void {
        scrolled = window.scrollY > 8;
    }

    function handleBrandClick(): void {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function isActive(href: string): boolean {
        return (
            (href === '/work' && current === 'work') ||
            (href === '/gallery' && current === 'gallery') ||
            (href === '/blog' && (current === 'blog' || current === 'post'))
        );
    }
</script>

<svelte:window onscroll={handleScroll} />
<BaliClockRails />

<header class="fixed inset-x-0 top-4 z-50 px-4">
    <nav
        aria-label="Main Navigation"
        class={cn(
            'liquid-glass mx-auto flex h-14 w-full max-w-6xl items-center justify-between gap-3 rounded-full pr-2 pl-3 transition-shadow duration-300 sm:pl-4 xl:max-w-5xl 2xl:max-w-6xl',
            scrolled && 'liquid-glass-scrolled',
        )}
    >
        <Link
            href="/"
            onclick={handleBrandClick}
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

        <div class="hidden items-center gap-0.5 lg:flex">
            {#each navLinks as link (link.href)}
                {#if link.href.startsWith('/#')}
                    <a href={link.href} class={navItem}>
                        {link.label}
                    </a>
                {:else if link.href.startsWith('#')}
                    <a href={link.href} class={navItem}>
                        {link.label}
                    </a>
                {:else}
                    <Link
                        href={link.href}
                        class={cn(
                            navItem,
                            isActive(link.href) &&
                                'font-semibold text-foreground dark:text-white',
                        )}
                        aria-current={isActive(link.href) ? 'page' : undefined}
                    >
                        {link.label}
                    </Link>
                {/if}
            {/each}
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

            <button
                type="button"
                onclick={() => (menuOpen = !menuOpen)}
                aria-expanded={menuOpen}
                aria-controls="mobile-nav"
                aria-label="Toggle navigation"
                class="liquid-glass-control grid size-9 place-items-center rounded-full text-muted-foreground transition-colors hover:text-foreground dark:hover:text-white focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none lg:hidden"
            >
                {#if menuOpen}
                    <X class="size-4" aria-hidden="true" />
                {:else}
                    <Menu class="size-4" aria-hidden="true" />
                {/if}
            </button>
        </div>
    </nav>

    {#if menuOpen}
        <nav
            id="mobile-nav"
            aria-label="Mobile"
            class="liquid-glass mx-auto mt-2 w-full max-w-6xl rounded-2xl p-2 lg:hidden"
        >
            {#each navLinks as link (link.href)}
                {#if link.href.startsWith('/#') || link.href.startsWith('#')}
                    <a
                        href={link.href}
                        onclick={() => (menuOpen = false)}
                        class={cn(
                            mobileNavItem,
                            isActive(link.href) &&
                                'font-semibold text-foreground dark:text-white',
                        )}
                    >
                        {link.label}
                    </a>
                {:else}
                    <Link
                        href={link.href}
                        onclick={() => (menuOpen = false)}
                        class={cn(
                            mobileNavItem,
                            isActive(link.href) &&
                                'font-semibold text-foreground dark:text-white',
                        )}
                        aria-current={isActive(link.href) ? 'page' : undefined}
                    >
                        {link.label}
                    </Link>
                {/if}
            {/each}
        </nav>
    {/if}
</header>
