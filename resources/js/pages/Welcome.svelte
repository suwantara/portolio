<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ArrowRight from '@lucide/svelte/icons/arrow-right';
    import ArrowUpRight from '@lucide/svelte/icons/arrow-up-right';
    import BadgeCheck from '@lucide/svelte/icons/badge-check';
    import BriefcaseBusiness from '@lucide/svelte/icons/briefcase-business';
    import Check from '@lucide/svelte/icons/check';
    import Clock from '@lucide/svelte/icons/clock';
    import CodeXml from '@lucide/svelte/icons/code-xml';
    import Copy from '@lucide/svelte/icons/copy';
    import Download from '@lucide/svelte/icons/download';
    import Eye from '@lucide/svelte/icons/eye';
    import FileText from '@lucide/svelte/icons/file-text';
    import GalleryHorizontal from '@lucide/svelte/icons/gallery-horizontal';
    import ExternalLink from '@lucide/svelte/icons/external-link';
    import Globe from '@lucide/svelte/icons/globe';
    import GraduationCap from '@lucide/svelte/icons/graduation-cap';
    import LanguagesIcon from '@lucide/svelte/icons/languages';
    import Layers from '@lucide/svelte/icons/layers';
    import Mail from '@lucide/svelte/icons/mail';
    import MapPin from '@lucide/svelte/icons/map-pin';
    import Menu from '@lucide/svelte/icons/menu';
    import UserPlus from '@lucide/svelte/icons/user-plus';
    import UserRound from '@lucide/svelte/icons/user-round';
    import X from '@lucide/svelte/icons/x';
    import { onDestroy, onMount } from 'svelte';
    import SeoHead from '@/components/SeoHead.svelte';
    import GalleryGrid from '@/components/GalleryGrid.svelte';
    import SocialLinks from '@/components/SocialLinks.svelte';
    import ThemeToggle from '@/components/ThemeToggle.svelte';
    import {
        Dialog,
        DialogClose,
        DialogContent,
        DialogDescription,
        DialogTitle,
    } from '@/components/ui/dialog';
    import {
        experienceAccents,
        projectCovers,
        projectNumber,
    } from '@/lib/portfolio';
    import { cn } from '@/lib/utils';
    import { gallery as galleryPage } from '@/routes';
    import type {
        PortfolioContent,
        PortfolioDocument,
        PortfolioSeo,
    } from '@/types';

    let {
        portfolio,
        seo,
    }: { portfolio: PortfolioContent; seo: PortfolioSeo } = $props();

    const profile = $derived(portfolio.profile);
    const stats = $derived(portfolio.stats);
    const about = $derived(portfolio.about);
    const aboutTags = $derived(portfolio.about_tags);
    const experience = $derived(portfolio.experience);
    const projects = $derived(portfolio.projects);
    const gallery = $derived(portfolio.gallery);
    const documents = $derived(portfolio.documents);
    const skillGroups = $derived(portfolio.skill_groups);
    const education = $derived(portfolio.education);
    const languages = $derived(portfolio.languages);
    const socials = $derived(portfolio.socials);
    const cta = $derived(portfolio.cta);
    const galleryPreview = $derived(gallery.slice(0, 4));

    const navLinks = $derived([
        { label: 'About', href: '#about' },
        { label: 'Experience', href: '#experience' },
        { label: 'Work', href: '#work' },
        ...(gallery.length > 0
            ? [{ label: 'Gallery', href: '/gallery' }]
            : []),
        { label: 'Contact', href: '#contact' },
    ]);

    const mailto = $derived(
        `mailto:${profile.email}?subject=${encodeURIComponent(cta.subject)}`,
    );
    const card =
        'rounded-2xl border border-border/70 bg-card/80 shadow-[0_16px_50px_-40px_rgba(15,23,42,0.45)] backdrop-blur-sm';

    let menuOpen = $state(false);
    let copied = $state(false);
    let scrolled = $state(false);
    let copyTimer: ReturnType<typeof setTimeout> | undefined;

    async function copyEmail(): Promise<void> {
        try {
            await navigator.clipboard.writeText(profile.email);
            copied = true;
            clearTimeout(copyTimer);
            copyTimer = setTimeout(() => {
                copied = false;
            }, 2000);
        } catch {
            window.location.href = mailto;
        }
    }

    function projectMailto(name: string): string {
        return `mailto:${profile.email}?subject=${encodeURIComponent(
            `Case study request: ${name}`,
        )}`;
    }

    let documentOpen = $state(false);
    let activeDocument = $state<PortfolioDocument | null>(null);

    function runClick(handler: unknown): void {
        if (typeof handler === 'function') {
            handler();
        }
    }

    function openDocument(document: PortfolioDocument): void {
        if (!document.file) {
            return;
        }

        activeDocument = document;
        documentOpen = true;
    }

    function fileExtension(url: string | null): string {
        if (!url) {
            return '';
        }

        const path = url.split('?')[0].split('#')[0];
        const name = path.split('/').pop() ?? '';

        return name.includes('.') ? (name.split('.').pop() ?? '').toLowerCase() : '';
    }

    function isPdf(url: string | null): boolean {
        return fileExtension(url) === 'pdf';
    }

    function isViewableImage(url: string | null): boolean {
        return ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif', 'svg'].includes(
            fileExtension(url),
        );
    }

    function handleScroll(): void {
        scrolled = window.scrollY > 8;
    }

    onMount(() => {
        document.documentElement.classList.add('scroll-smooth');
    });

    onDestroy(() => {
        document.documentElement.classList.remove('scroll-smooth');
        clearTimeout(copyTimer);
    });
</script>

<svelte:window onscroll={handleScroll} />

<SeoHead {seo} />

<div
    class="relative isolate min-h-screen overflow-x-clip bg-[#f7f8fa] text-foreground dark:bg-[#07080c]"
>
    <div
        class="pointer-events-none absolute inset-0 -z-10 overflow-hidden"
        aria-hidden="true"
    >
        <div
            class="absolute inset-x-0 top-0 h-[860px] bg-[radial-gradient(80%_60%_at_50%_0%,rgba(147,197,253,0.38),transparent_72%)] dark:bg-[radial-gradient(80%_60%_at_50%_0%,rgba(37,99,235,0.2),transparent_72%)]"
        ></div>
        <div
            class="absolute inset-x-0 top-0 h-[860px] bg-[linear-gradient(to_right,rgba(15,23,42,0.045)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,0.045)_1px,transparent_1px)] [background-size:56px_56px] [mask-image:linear-gradient(to_bottom,black,transparent)] dark:bg-[linear-gradient(to_right,rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.05)_1px,transparent_1px)]"
        ></div>
    </div>

    <header class="fixed inset-x-0 top-4 z-50 px-4">
        <nav
            aria-label="Primary"
            class={cn(
                'mx-auto flex h-14 w-full max-w-5xl items-center justify-between gap-3 rounded-full border border-border/70 bg-background/80 pr-2 pl-3 backdrop-blur-xl transition-shadow duration-300 sm:pl-4',
                scrolled
                    ? 'shadow-lg shadow-slate-900/5 dark:shadow-black/30'
                    : 'shadow-sm shadow-slate-900/[0.03]',
            )}
        >
            <a
                href="#top"
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
            </a>

            <div class="hidden items-center gap-0.5 md:flex">
                {#each navLinks as link (link.href)}
                    {#if link.href.startsWith('#')}
                        <a
                            href={link.href}
                            class="rounded-full px-3.5 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        >
                            {link.label}
                        </a>
                    {:else}
                        <Link
                            href={link.href}
                            class="rounded-full px-3.5 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        >
                            {link.label}
                        </Link>
                    {/if}
                {/each}
            </div>

            <div class="flex items-center gap-1.5">
                <ThemeToggle />

                <a
                    href={mailto}
                    class="inline-flex h-9 items-center gap-1.5 rounded-full bg-[#0a66c2] px-4 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#0958a8] focus-visible:ring-2 focus-visible:ring-[#0a66c2] focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
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
                    class="grid size-9 place-items-center rounded-full text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none md:hidden"
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
            <div
                id="mobile-nav"
                class="mx-auto mt-2 w-full max-w-5xl rounded-2xl border border-border/70 bg-background/95 p-2 shadow-lg shadow-slate-900/5 backdrop-blur-xl md:hidden"
            >
                {#each navLinks as link (link.href)}
                    {#if link.href.startsWith('#')}
                        <a
                            href={link.href}
                            onclick={() => (menuOpen = false)}
                            class="block rounded-xl px-4 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        >
                            {link.label}
                        </a>
                    {:else}
                        <Link
                            href={link.href}
                            onclick={() => (menuOpen = false)}
                            class="block rounded-xl px-4 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        >
                            {link.label}
                        </Link>
                    {/if}
                {/each}

            </div>
        {/if}
    </header>

    <main id="top" class="mx-auto w-full max-w-5xl px-4 pb-12 sm:px-6">
        <section class="pt-28 sm:pt-32" aria-labelledby="profile-name">
            <div
                class="overflow-hidden rounded-[1.75rem] border border-border/70 bg-card/80 shadow-[0_32px_90px_-60px_rgba(15,23,42,0.5)] backdrop-blur-sm"
            >
                <div
                    class="relative h-44 overflow-hidden border-b border-border/60 sm:h-56"
                >
                    <div
                        class="absolute inset-0 bg-[radial-gradient(130%_120%_at_10%_-10%,#dbeafe_0%,#eff6ff_45%,#f8fafc_100%)] dark:bg-[radial-gradient(130%_120%_at_10%_-10%,#0b1d3f_0%,#0a1224_48%,#07080c_100%)]"
                    ></div>
                    <div
                        class="absolute -top-24 -right-10 size-72 rounded-full bg-sky-300/60 blur-[90px] dark:bg-blue-600/25"
                    ></div>
                    <div
                        class="absolute -bottom-28 left-1/3 size-64 rounded-full bg-blue-400/30 blur-[80px] dark:bg-indigo-500/20"
                    ></div>
                    <div
                        class="absolute inset-0 bg-[linear-gradient(to_right,rgba(15,23,42,0.055)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,0.055)_1px,transparent_1px)] [background-size:44px_44px] dark:bg-[linear-gradient(to_right,rgba(255,255,255,0.055)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.055)_1px,transparent_1px)]"
                    ></div>
                    <svg
                        class="absolute -right-8 -bottom-14 h-60 w-60 text-slate-900/10 dark:text-white/10"
                        viewBox="0 0 200 200"
                        fill="none"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <circle cx="100" cy="100" r="88" opacity="0.4" />
                        <circle
                            cx="100"
                            cy="100"
                            r="62"
                            stroke-dasharray="3 7"
                        />
                        <circle cx="100" cy="100" r="38" />
                        <circle
                            cx="176"
                            cy="100"
                            r="4"
                            fill="currentColor"
                            stroke="none"
                        />
                    </svg>
                    <p
                        class="absolute top-5 left-6 font-mono text-[10px] font-medium tracking-[0.22em] text-slate-500 uppercase dark:text-slate-400"
                    >
                        Portfolio — 2026
                    </p>
                    <p
                        class="absolute right-6 bottom-5 font-mono text-[10px] tracking-[0.18em] text-slate-500/80 uppercase dark:text-slate-400/80"
                    >
                        {profile.location}
                    </p>
                </div>

                <div class="px-5 pb-6 sm:px-8 sm:pb-8">
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
                    >
                        <div class="-mt-14 sm:-mt-16">
                            <div
                                class="relative inline-flex rounded-[1.75rem] bg-card p-1.5 shadow-xl shadow-slate-900/10 ring-1 ring-border/60 dark:shadow-black/40"
                                role="img"
                                aria-label={`${profile.name} avatar`}
                            >
                                {#if profile.avatar}
                                    <img
                                        src={profile.avatar}
                                        alt={profile.name}
                                        class="size-24 rounded-[1.35rem] object-cover sm:size-32"
                                    />
                                {:else}
                                    <div
                                        class="grid size-24 place-items-center rounded-[1.35rem] bg-linear-to-br from-slate-900 via-blue-700 to-sky-400 text-2xl font-semibold tracking-tight text-white sm:size-32 sm:text-3xl"
                                    >
                                        {profile.monogram}
                                    </div>
                                {/if}
                                <span
                                    class="absolute right-3 bottom-3 size-4 rounded-full border-[3px] border-card bg-emerald-500"
                                    aria-hidden="true"
                                ></span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 sm:pb-1">
                            <a
                                href={mailto}
                                class="inline-flex h-10 items-center gap-2 rounded-full bg-[#0a66c2] px-5 text-sm font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-[#0958a8] focus-visible:ring-2 focus-visible:ring-[#0a66c2] focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                            >
                                <UserPlus class="size-4" aria-hidden="true" />
                                Connect
                            </a>
                            <a
                                href="#experience"
                                class="inline-flex h-10 items-center gap-2 rounded-full border border-border bg-background/70 px-5 text-sm font-semibold text-foreground transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                            >
                                <BriefcaseBusiness
                                    class="size-4"
                                    aria-hidden="true"
                                />
                                View experience
                            </a>
                        </div>
                    </div>

                    <div class="mt-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1
                                id="profile-name"
                                class="text-[2.1rem] leading-none font-semibold tracking-[-0.035em] sm:text-[2.6rem]"
                            >
                                {profile.name}
                            </h1>
                            <span
                                class="grid size-6 place-items-center rounded-full bg-[#0a66c2] text-white dark:bg-sky-500"
                                title="Verified profile"
                            >
                                <BadgeCheck
                                    class="size-4"
                                    aria-hidden="true"
                                />
                            </span>
                            <span
                                class={cn(
                                    'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 font-mono text-[10px] font-medium tracking-[0.14em] uppercase',
                                    profile.availability_open
                                        ? 'border-emerald-500/25 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                        : 'border-amber-500/25 bg-amber-500/10 text-amber-700 dark:text-amber-400',
                                )}
                            >
                                <span class="relative flex size-1.5">
                                    {#if profile.availability_open}
                                        <span
                                            class="absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-60 motion-safe:animate-ping"
                                        ></span>
                                    {/if}
                                    <span
                                        class={cn(
                                            'relative inline-flex size-1.5 rounded-full',
                                            profile.availability_open
                                                ? 'bg-emerald-500'
                                                : 'bg-amber-500',
                                        )}
                                    ></span>
                                </span>
                                {profile.availability}
                            </span>
                        </div>

                        <p class="mt-2 text-base font-medium sm:text-lg">
                            {profile.role}
                        </p>
                        <p
                            class="mt-2 max-w-2xl text-sm leading-relaxed text-muted-foreground sm:text-[15px]"
                        >
                            {profile.tagline}
                        </p>

                        <div
                            class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-[13px] text-muted-foreground sm:text-sm"
                        >
                            <span class="inline-flex items-center gap-1.5">
                                <MapPin class="size-4" aria-hidden="true" />
                                {profile.location}
                            </span>
                            <span
                                class="hidden size-1 rounded-full bg-border sm:block"
                                aria-hidden="true"
                            ></span>
                            <span class="inline-flex items-center gap-1.5">
                                <Clock class="size-4" aria-hidden="true" />
                                {profile.timezone}
                            </span>
                            <span
                                class="hidden size-1 rounded-full bg-border sm:block"
                                aria-hidden="true"
                            ></span>
                            <span class="inline-flex min-w-0 items-center gap-1.5">
                                <Mail class="size-4 shrink-0" aria-hidden="true" />
                                <a
                                    href={mailto}
                                    class="truncate hover:text-foreground hover:underline"
                                >
                                    {profile.email}
                                </a>
                            </span>
                        </div>

                        <SocialLinks {socials} class="mt-4" />
                    </div>

                    <div
                        class="mt-7 grid grid-cols-3 divide-x divide-border/60 border-t border-border/60 pt-5"
                    >
                        {#each stats as stat (stat.label)}
                            <div class="px-4 first:pl-0 last:pr-0 sm:px-5">
                                <p
                                    class="font-mono text-xl font-medium tracking-tight sm:text-2xl"
                                >
                                    {stat.value}
                                </p>
                                <p
                                    class="mt-1 font-mono text-[9px] tracking-[0.14em] text-muted-foreground uppercase sm:text-[10px]"
                                >
                                    {stat.label}
                                </p>
                            </div>
                        {/each}
                    </div>
                </div>
            </div>
        </section>

        <div
            class="mt-5 grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-start"
        >
            <div class="flex flex-col gap-5">
                <section
                    id="about"
                    aria-labelledby="about-heading"
                    class={cn(card, 'scroll-mt-24 p-5 sm:p-6')}
                >
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="grid size-8 place-items-center rounded-lg bg-muted text-muted-foreground"
                            >
                                <UserRound
                                    class="size-4"
                                    aria-hidden="true"
                                />
                            </span>
                            <h2
                                id="about-heading"
                                class="text-lg font-semibold tracking-tight"
                            >
                                About
                            </h2>
                        </div>
                        <span
                            class="font-mono text-[10px] tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            01
                        </span>
                    </div>

                    <div
                        class="space-y-3 text-[15px] leading-7 text-muted-foreground"
                    >
                        {#each about as paragraph, index (index)}
                            <p class={index === 0 ? 'text-foreground/90' : ''}>
                                {paragraph}
                            </p>
                        {/each}
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        {#each aboutTags as item (item)}
                            <span
                                class="rounded-full border border-border/70 bg-background/60 px-3 py-1 text-xs font-medium text-muted-foreground"
                            >
                                {item}
                            </span>
                        {/each}
                    </div>
                </section>

                <section
                    id="experience"
                    aria-labelledby="experience-heading"
                    class={cn(card, 'scroll-mt-24 p-5 sm:p-6')}
                >
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="grid size-8 place-items-center rounded-lg bg-muted text-muted-foreground"
                            >
                                <BriefcaseBusiness
                                    class="size-4"
                                    aria-hidden="true"
                                />
                            </span>
                            <h2
                                id="experience-heading"
                                class="text-lg font-semibold tracking-tight"
                            >
                                Experience
                            </h2>
                        </div>
                        <span
                            class="font-mono text-[10px] tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            02
                        </span>
                    </div>

                    <div class="divide-y divide-border/60">
                        {#each experience as job (job.company)}
                            <article class="py-5 first:pt-0 last:pb-0">
                                <div class="flex gap-4 sm:gap-5">
                                    <div
                                        class={cn(
                                            'grid size-12 shrink-0 place-items-center rounded-xl bg-linear-to-br text-sm font-semibold text-white shadow-sm sm:size-14',
                                            experienceAccents[job.accent] ??
                                                experienceAccents.blue,
                                        )}
                                        aria-hidden="true"
                                    >
                                        {job.monogram}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div
                                            class="flex flex-wrap items-start justify-between gap-x-4 gap-y-1"
                                        >
                                            <div>
                                                <h3
                                                    class="text-[15px] font-semibold sm:text-base"
                                                >
                                                    {job.role}
                                                </h3>
                                                <p
                                                    class="text-sm text-muted-foreground"
                                                >
                                                    {job.company} · {job
                                                        .employment}
                                                </p>
                                            </div>
                                            <p
                                                class="font-mono text-[10px] whitespace-nowrap tracking-[0.14em] text-muted-foreground uppercase"
                                            >
                                                {job.period}
                                            </p>
                                        </div>
                                        <p
                                            class="mt-0.5 font-mono text-[10px] tracking-[0.12em] text-muted-foreground/80 uppercase"
                                        >
                                            {job.location}
                                        </p>
                                        <p
                                            class="mt-2.5 text-sm leading-relaxed text-muted-foreground"
                                        >
                                            {job.description}
                                        </p>

                                        <ul class="mt-2.5 space-y-1.5">
                                            {#each job.highlights as highlight (highlight)}
                                                <li
                                                    class="flex gap-2.5 text-sm leading-relaxed text-muted-foreground"
                                                >
                                                    <span
                                                        class="mt-[9px] size-1 shrink-0 rounded-full bg-sky-500/80"
                                                        aria-hidden="true"
                                                    ></span>
                                                    {highlight}
                                                </li>
                                            {/each}
                                        </ul>

                                        <div
                                            class="mt-3 flex flex-wrap gap-1.5"
                                        >
                                            {#each job.skills as skill (skill)}
                                                <span
                                                    class="rounded-md bg-muted px-2 py-1 text-[11px] font-medium text-muted-foreground"
                                                >
                                                    {skill}
                                                </span>
                                            {/each}
                                        </div>
                                    </div>
                                </div>
                            </article>
                        {/each}
                    </div>
                </section>

                <section
                    id="work"
                    aria-labelledby="work-heading"
                    class={cn(card, 'scroll-mt-24 p-5 sm:p-6')}
                >
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="grid size-8 place-items-center rounded-lg bg-muted text-muted-foreground"
                            >
                                <Layers class="size-4" aria-hidden="true" />
                            </span>
                            <h2
                                id="work-heading"
                                class="text-lg font-semibold tracking-tight"
                            >
                                Selected work
                            </h2>
                        </div>
                        <span
                            class="font-mono text-[10px] tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            03
                        </span>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        {#each projects as project, index (project.name)}
                            <article
                                class={cn(
                                    'group overflow-hidden rounded-xl border border-border/70 bg-background/40',
                                    project.featured && 'sm:col-span-2',
                                )}
                            >
                                <div
                                    class={cn(
                                        'relative overflow-hidden',
                                        projectCovers[project.cover] ??
                                            projectCovers.blue,
                                        project.featured ? 'h-48' : 'h-40',
                                    )}
                                >
                                    {#if project.image}
                                        <img
                                            src={project.image}
                                            alt=""
                                            class="absolute inset-0 size-full object-cover"
                                        />
                                        <div
                                            class="absolute inset-0 bg-linear-to-t from-black/70 via-black/25 to-black/10"
                                            aria-hidden="true"
                                        ></div>
                                    {:else}
                                        <div
                                            class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.07)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.07)_1px,transparent_1px)] [background-size:32px_32px]"
                                            aria-hidden="true"
                                        ></div>
                                        <div
                                            class="absolute top-1/2 left-1/2 size-36 -translate-x-1/2 -translate-y-1/2 rounded-full border border-white/15"
                                            aria-hidden="true"
                                        ></div>
                                        <div
                                            class="absolute top-1/2 left-1/2 size-56 -translate-x-1/2 -translate-y-1/2 rounded-full border border-white/10"
                                            aria-hidden="true"
                                        ></div>
                                        <span
                                            class="absolute -right-3 -bottom-8 text-[7rem] leading-none font-semibold text-white/10 select-none"
                                            aria-hidden="true"
                                        >
                                            {project.name.charAt(0)}
                                        </span>
                                    {/if}
                                    <p
                                        class="absolute top-3 left-3 font-mono text-[10px] tracking-[0.18em] text-white/70 uppercase"
                                    >
                                        {projectNumber(index)} — {project.year}
                                    </p>

                                    {#if project.featured}
                                        <div
                                            class="absolute right-6 bottom-0 hidden w-52 rounded-t-xl border border-white/15 bg-white/10 p-3 backdrop-blur-md sm:block"
                                            aria-hidden="true"
                                        >
                                            <div class="flex gap-1">
                                                <span
                                                    class="size-1.5 rounded-full bg-white/40"
                                                ></span>
                                                <span
                                                    class="size-1.5 rounded-full bg-white/25"
                                                ></span>
                                                <span
                                                    class="size-1.5 rounded-full bg-white/25"
                                                ></span>
                                            </div>
                                            <div class="mt-3 space-y-1.5">
                                                <div
                                                    class="h-1.5 w-3/4 rounded-full bg-white/30"
                                                ></div>
                                                <div
                                                    class="h-1.5 w-1/2 rounded-full bg-white/20"
                                                ></div>
                                                <div
                                                    class="h-1.5 w-2/3 rounded-full bg-white/15"
                                                ></div>
                                            </div>
                                        </div>
                                    {/if}

                                    <span
                                        class="absolute right-3 bottom-3 grid size-8 place-items-center rounded-full bg-white/10 text-white backdrop-blur transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5"
                                        aria-hidden="true"
                                    >
                                        <ArrowUpRight class="size-4" />
                                    </span>
                                </div>

                                <div class="p-4 sm:p-5">
                                    <div
                                        class="flex items-start justify-between gap-3"
                                    >
                                        <h3 class="font-semibold">
                                            {project.name}
                                        </h3>
                                        <span
                                            class="font-mono text-[10px] tracking-wider text-muted-foreground uppercase"
                                        >
                                            {project.year}
                                        </span>
                                    </div>
                                    <p
                                        class="mt-1 text-sm leading-relaxed text-muted-foreground"
                                    >
                                        {project.description}
                                    </p>
                                    <div class="mt-3 flex flex-wrap gap-1.5">
                                        {#each project.tags as tag (tag)}
                                            <span
                                                class="rounded-md bg-muted px-2 py-1 text-[11px] font-medium text-muted-foreground"
                                            >
                                                {tag}
                                            </span>
                                        {/each}
                                    </div>
                                    {#if project.link}
                                        <a
                                            href={project.link}
                                            target="_blank"
                                            rel="noreferrer noopener"
                                            class="mt-3 inline-flex items-center gap-1.5 text-xs font-medium text-[#0a66c2] hover:underline dark:text-sky-400"
                                        >
                                            View project
                                            <ExternalLink
                                                class="size-3.5"
                                                aria-hidden="true"
                                            />
                                        </a>
                                    {:else}
                                        <a
                                            href={projectMailto(project.name)}
                                            class="mt-3 inline-flex items-center gap-1.5 text-xs font-medium text-[#0a66c2] hover:underline dark:text-sky-400"
                                        >
                                            Request case study
                                            <ArrowRight
                                                class="size-3.5"
                                                aria-hidden="true"
                                            />
                                        </a>
                                    {/if}
                                </div>
                            </article>
                        {/each}
                    </div>
                </section>

                {#if gallery.length > 0}
                    <section
                        id="gallery"
                        aria-labelledby="gallery-heading"
                        class={cn(card, 'scroll-mt-24 p-5 sm:p-6')}
                    >
                        <div
                            class="mb-4 flex flex-wrap items-center justify-between gap-3"
                        >
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="grid size-8 place-items-center rounded-lg bg-muted text-muted-foreground"
                                >
                                    <GalleryHorizontal
                                        class="size-4"
                                        aria-hidden="true"
                                    />
                                </span>
                                <h2
                                    id="gallery-heading"
                                    class="text-lg font-semibold tracking-tight"
                                >
                                    Gallery
                                </h2>
                            </div>

                            <div class="flex items-center gap-3">
                                <Link
                                    href={galleryPage.url()}
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0a66c2] hover:underline dark:text-sky-400"
                                >
                                    View all ({gallery.length})
                                    <ArrowRight
                                        class="size-3.5"
                                        aria-hidden="true"
                                    />
                                </Link>
                                <span
                                    class="font-mono text-[10px] tracking-[0.18em] text-muted-foreground uppercase"
                                >
                                    04
                                </span>
                            </div>
                        </div>

                        <GalleryGrid items={galleryPreview} columns={2} />
                    </section>
                {/if}

                {#if documents.length > 0}
                    <section
                        id="documents"
                        aria-labelledby="documents-heading"
                        class={cn(card, 'scroll-mt-24 p-5 sm:p-6')}
                    >
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="grid size-8 place-items-center rounded-lg bg-muted text-muted-foreground"
                                >
                                    <FileText
                                        class="size-4"
                                        aria-hidden="true"
                                    />
                                </span>
                                <h2
                                    id="documents-heading"
                                    class="text-lg font-semibold tracking-tight"
                                >
                                    Documents
                                </h2>
                            </div>
                            <span
                                class="font-mono text-[10px] tracking-[0.18em] text-muted-foreground uppercase"
                            >
                                05
                            </span>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            {#each documents as document, index (document.title + index)}
                                {#if document.file}
                                    <article
                                        class="flex flex-col rounded-xl border border-border/70 bg-background/40 p-4"
                                    >
                                        <div class="flex gap-3">
                                            <span
                                                class="grid size-10 shrink-0 place-items-center rounded-lg bg-muted text-muted-foreground"
                                            >
                                                <FileText
                                                    class="size-5"
                                                    aria-hidden="true"
                                                />
                                            </span>
                                            <div class="min-w-0">
                                                <h3 class="text-sm font-semibold">
                                                    {document.title}
                                                </h3>
                                                {#if document.description}
                                                    <p
                                                        class="mt-1 text-xs leading-relaxed text-muted-foreground"
                                                    >
                                                        {document.description}
                                                    </p>
                                                {/if}
                                            </div>
                                        </div>

                                        <div
                                            class="mt-4 flex flex-wrap items-center gap-2"
                                        >
                                            {#if isPdf(document.file) || isViewableImage(document.file)}
                                                <button
                                                    type="button"
                                                    onclick={() =>
                                                        openDocument(document)}
                                                    class="inline-flex h-8 items-center gap-1.5 rounded-full bg-foreground px-3 text-xs font-semibold text-background transition-opacity hover:opacity-90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                                                >
                                                    <Eye
                                                        class="size-3.5"
                                                        aria-hidden="true"
                                                    />
                                                    View
                                                </button>
                                            {:else}
                                                <a
                                                    href={document.file}
                                                    target="_blank"
                                                    rel="noreferrer noopener"
                                                    class="inline-flex h-8 items-center gap-1.5 rounded-full bg-foreground px-3 text-xs font-semibold text-background transition-opacity hover:opacity-90"
                                                >
                                                    Open
                                                    <ExternalLink
                                                        class="size-3.5"
                                                        aria-hidden="true"
                                                    />
                                                </a>
                                            {/if}
                                            <a
                                                href={document.file}
                                                download
                                                target="_blank"
                                                rel="noreferrer noopener"
                                                class="inline-flex h-8 items-center gap-1.5 rounded-full border border-border px-3 text-xs font-semibold text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                            >
                                                <Download
                                                    class="size-3.5"
                                                    aria-hidden="true"
                                                />
                                                Download
                                            </a>
                                        </div>
                                    </article>
                                {/if}
                            {/each}
                        </div>
                    </section>
                {/if}

                <section
                    aria-labelledby="skills-heading"
                    class={cn(card, 'p-5 sm:p-6')}
                >
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="grid size-8 place-items-center rounded-lg bg-muted text-muted-foreground"
                            >
                                <CodeXml class="size-4" aria-hidden="true" />
                            </span>
                            <h2
                                id="skills-heading"
                                class="text-lg font-semibold tracking-tight"
                            >
                                Skills
                            </h2>
                        </div>
                        <span
                            class="font-mono text-[10px] tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            06
                        </span>
                    </div>

                    <div class="space-y-4">
                        {#each skillGroups as group (group.title)}
                            <div
                                class="grid gap-2 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-start"
                            >
                                <h3
                                    class="font-mono text-[10px] tracking-[0.14em] text-muted-foreground uppercase sm:pt-1.5"
                                >
                                    {group.title}
                                </h3>
                                <div class="flex flex-wrap gap-1.5">
                                    {#each group.skills as skill (skill)}
                                        <span
                                            class="rounded-full border border-border/70 bg-background/60 px-2.5 py-1 text-xs font-medium text-foreground/80"
                                        >
                                            {skill}
                                        </span>
                                    {/each}
                                </div>
                            </div>
                        {/each}
                    </div>
                </section>

                <section
                    aria-labelledby="education-heading"
                    class={cn(card, 'p-5 sm:p-6')}
                >
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="grid size-8 place-items-center rounded-lg bg-muted text-muted-foreground"
                            >
                                <GraduationCap
                                    class="size-4"
                                    aria-hidden="true"
                                />
                            </span>
                            <h2
                                id="education-heading"
                                class="text-lg font-semibold tracking-tight"
                            >
                                Education
                            </h2>
                        </div>
                        <span
                            class="font-mono text-[10px] tracking-[0.18em] text-muted-foreground uppercase"
                        >
                            07
                        </span>
                    </div>

                    {#each education as item (item.school)}
                        <div class="flex gap-4">
                            <div
                                class="grid size-12 shrink-0 place-items-center rounded-xl border border-border/70 bg-background/60 text-muted-foreground"
                                aria-hidden="true"
                            >
                                <GraduationCap class="size-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div
                                    class="flex flex-wrap items-start justify-between gap-x-4 gap-y-1"
                                >
                                    <div>
                                        <h3
                                            class="text-[15px] font-semibold sm:text-base"
                                        >
                                            {item.degree}
                                        </h3>
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            {item.school}
                                        </p>
                                    </div>
                                    <p
                                        class="font-mono text-[10px] whitespace-nowrap tracking-[0.14em] text-muted-foreground uppercase"
                                    >
                                        {item.period}
                                    </p>
                                </div>
                                <p
                                    class="mt-2 text-sm leading-relaxed text-muted-foreground"
                                >
                                    {item.description}
                                </p>
                            </div>
                        </div>
                    {/each}

                    <div class="mt-5 border-t border-border/60 pt-4">
                        <p
                            class="font-mono text-[10px] tracking-[0.14em] text-muted-foreground uppercase"
                        >
                            Currently exploring
                        </p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            {#each ['Svelte 5 runes', 'Laravel 12', 'Local-first apps', 'Design tokens'] as topic (topic)}
                                <span
                                    class="rounded-full border border-border/70 bg-background/60 px-2.5 py-1 text-xs font-medium text-muted-foreground"
                                >
                                    {topic}
                                </span>
                            {/each}
                        </div>
                    </div>
                </section>
            </div>

            <aside class="flex flex-col gap-5 lg:sticky lg:top-24">
                <section
                    aria-labelledby="contact-heading"
                    class={cn(card, 'p-5')}
                >
                    <div class="mb-3 flex items-center gap-2.5">
                        <span
                            class="grid size-8 place-items-center rounded-lg bg-muted text-muted-foreground"
                        >
                            <Mail class="size-4" aria-hidden="true" />
                        </span>
                        <h2
                            id="contact-heading"
                            class="text-lg font-semibold tracking-tight"
                        >
                            Contact
                        </h2>
                    </div>

                    <div class="space-y-0.5">
                        <div
                            class="flex items-center justify-between gap-2 rounded-xl px-2 py-2 transition-colors hover:bg-muted/60"
                        >
                            <div class="flex min-w-0 items-center gap-2.5">
                                <Mail
                                    class="size-4 shrink-0 text-muted-foreground"
                                    aria-hidden="true"
                                />
                                <a
                                    href={mailto}
                                    class="truncate text-sm font-medium hover:underline"
                                >
                                    {profile.email}
                                </a>
                            </div>
                            <button
                                type="button"
                                onclick={copyEmail}
                                aria-label="Copy email address"
                                class="grid size-7 shrink-0 place-items-center rounded-md text-muted-foreground transition-colors hover:bg-background hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                            >
                                {#if copied}
                                    <Check
                                        class="size-3.5 text-emerald-600 dark:text-emerald-400"
                                        aria-hidden="true"
                                    />
                                {:else}
                                    <Copy class="size-3.5" aria-hidden="true" />
                                {/if}
                            </button>
                        </div>

                        <div class="flex items-center gap-2.5 px-2 py-2">
                            <MapPin
                                class="size-4 shrink-0 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <span class="text-sm text-muted-foreground">
                                {profile.location}
                            </span>
                        </div>

                        <div class="flex items-center gap-2.5 px-2 py-2">
                            <Clock
                                class="size-4 shrink-0 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <span class="text-sm text-muted-foreground">
                                {profile.timezone}
                            </span>
                        </div>

                        <div class="flex items-center gap-2.5 px-2 py-2">
                            <Globe
                                class="size-4 shrink-0 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <span class="text-sm text-muted-foreground">
                                Remote-friendly
                            </span>
                        </div>
                    </div>

                    <span class="sr-only" aria-live="polite">
                        {copied ? 'Email address copied' : ''}
                    </span>

                    <SocialLinks {socials} class="mt-3" />
                </section>

                <section
                    aria-labelledby="availability-heading"
                    class="relative overflow-hidden rounded-2xl border border-sky-600/20 bg-[linear-gradient(145deg,rgba(10,102,194,0.14),rgba(56,189,248,0.08)_55%,transparent)] p-5 dark:border-sky-400/20 dark:bg-[linear-gradient(145deg,rgba(10,102,194,0.28),rgba(56,189,248,0.1)_55%,transparent)]"
                >
                    <div
                        class="absolute -top-12 -right-10 size-32 rounded-full bg-sky-400/20 blur-2xl"
                        aria-hidden="true"
                    ></div>

                    <h2
                        id="availability-heading"
                        class="flex items-center gap-2 font-mono text-[10px] font-medium tracking-[0.16em] text-[#0a66c2] uppercase dark:text-sky-300"
                    >
                        <span class="relative flex size-1.5">
                            {#if profile.availability_open}
                                <span
                                    class="absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-60 motion-safe:animate-ping"
                                ></span>
                            {/if}
                            <span
                                class={cn(
                                    'relative inline-flex size-1.5 rounded-full',
                                    profile.availability_open
                                        ? 'bg-emerald-500'
                                        : 'bg-amber-500',
                                )}
                            ></span>
                        </span>
                        {profile.availability}
                    </h2>

                    <p
                        class="mt-3 text-lg leading-snug font-semibold tracking-tight"
                    >
                        {profile.availability_note}
                    </p>
                    <p class="mt-2 text-sm leading-relaxed text-muted-foreground">
                        Based in {profile.location}, working remotely with teams
                        across time zones.
                    </p>

                    <a
                        href={mailto}
                        class="mt-4 inline-flex h-9 w-full items-center justify-center gap-2 rounded-full bg-foreground text-sm font-semibold text-background transition-opacity hover:opacity-90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                    >
                        Start a conversation
                        <ArrowRight class="size-3.5" aria-hidden="true" />
                    </a>
                </section>

                <section
                    aria-labelledby="languages-heading"
                    class={cn(card, 'p-5')}
                >
                    <div class="mb-3 flex items-center gap-2.5">
                        <span
                            class="grid size-8 place-items-center rounded-lg bg-muted text-muted-foreground"
                        >
                            <LanguagesIcon
                                class="size-4"
                                aria-hidden="true"
                            />
                        </span>
                        <h2
                            id="languages-heading"
                            class="text-lg font-semibold tracking-tight"
                        >
                            Languages
                        </h2>
                    </div>

                    <div class="divide-y divide-border/60">
                        {#each languages as language (language.name)}
                            <div
                                class="flex items-center justify-between gap-3 py-2 first:pt-0 last:pb-0"
                            >
                                <span class="text-sm font-medium">
                                    {language.name}
                                </span>
                                <span
                                    class="font-mono text-[10px] tracking-[0.12em] text-muted-foreground uppercase"
                                >
                                    {language.level}
                                </span>
                            </div>
                        {/each}
                    </div>
                </section>
            </aside>
        </div>

        <section
            id="contact"
            aria-labelledby="cta-heading"
            class="relative mt-5 scroll-mt-24 overflow-hidden rounded-[1.75rem] border border-blue-500/20 bg-[linear-gradient(135deg,#0a2a52_0%,#0a66c2_48%,#38bdf8_100%)] px-6 py-10 text-white shadow-xl shadow-blue-900/10 sm:px-12 sm:py-14"
        >
            <div
                class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.08)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.08)_1px,transparent_1px)] [background-size:44px_44px] [mask-image:linear-gradient(to_bottom,black,transparent)]"
                aria-hidden="true"
            ></div>
            <div
                class="absolute -top-24 -right-16 size-72 rounded-full bg-white/15 blur-3xl"
                aria-hidden="true"
            ></div>

            <div
                class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between"
            >
                <div class="max-w-xl">
                    <p
                        class="font-mono text-[10px] tracking-[0.2em] text-sky-100/80 uppercase"
                    >
                        {cta.eyebrow}
                    </p>
                    <h2
                        id="cta-heading"
                        class="mt-3 text-3xl leading-tight font-semibold tracking-[-0.02em] sm:text-4xl"
                    >
                        {cta.heading}
                    </h2>
                    <p
                        class="mt-3 text-sm leading-relaxed text-blue-50/80 sm:text-base"
                    >
                        {cta.description}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href={mailto}
                        class="inline-flex h-11 items-center gap-2 rounded-full bg-white px-6 text-sm font-semibold text-[#0a2a52] shadow-sm transition-transform hover:-translate-y-0.5 focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#0a66c2] focus-visible:outline-none"
                    >
                        {cta.button}
                        <Mail class="size-4" aria-hidden="true" />
                    </a>
                    <SocialLinks
                        {socials}
                        class="[&>a]:border-white/25 [&>a]:bg-white/10 [&>a]:text-white/85 [&>a]:hover:bg-white/20 [&>a]:hover:text-white"
                    />
                </div>
            </div>
        </section>

        <Dialog bind:open={documentOpen}>
            <DialogContent class="max-w-4xl p-3 sm:p-4">
                {#if activeDocument}
                    <div class="flex items-start justify-between gap-4 px-1 pb-3">
                        <div>
                            <DialogTitle>{activeDocument.title}</DialogTitle>
                            {#if activeDocument.description}
                                <DialogDescription>
                                    {activeDocument.description}
                                </DialogDescription>
                            {/if}
                        </div>
                        <DialogClose asChild>
                            {#snippet children(props)}
                                <button
                                    type="button"
                                    onclick={() => runClick(props.onClick)}
                                    aria-label="Close document viewer"
                                    class="grid size-8 shrink-0 place-items-center rounded-full text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                                >
                                    <X class="size-4" aria-hidden="true" />
                                </button>
                            {/snippet}
                        </DialogClose>
                    </div>

                    {#if activeDocument.file}
                        {#if isPdf(activeDocument.file)}
                            <iframe
                                src={activeDocument.file}
                                title={activeDocument.title}
                                class="h-[68vh] w-full rounded-lg border border-border/70 bg-white"
                            ></iframe>
                        {:else if isViewableImage(activeDocument.file)}
                            <img
                                src={activeDocument.file}
                                alt={activeDocument.title}
                                class="max-h-[68vh] w-full rounded-lg object-contain"
                            />
                        {/if}

                        <div class="mt-3 flex justify-end">
                            <a
                                href={activeDocument.file}
                                target="_blank"
                                rel="noreferrer noopener"
                                class="inline-flex h-9 items-center gap-1.5 rounded-full border border-border px-4 text-sm font-semibold text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            >
                                Open in new tab
                                <ExternalLink
                                    class="size-3.5"
                                    aria-hidden="true"
                                />
                            </a>
                        </div>
                    {/if}
                {/if}
            </DialogContent>
        </Dialog>

        <footer
            class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-border/60 py-8 text-center sm:flex-row sm:text-left"
        >
            <p class="text-xs text-muted-foreground">
                © 2026 {profile.name}. {portfolio.footer_note}
            </p>
            <div class="flex items-center gap-4">
                <a
                    href="#top"
                    class="font-mono text-[10px] tracking-[0.14em] text-muted-foreground uppercase transition-colors hover:text-foreground"
                >
                    Back to top ↑
                </a>
                <SocialLinks {socials} size="sm" />
            </div>
        </footer>
    </main>
</div>
