<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ArrowLeft from '@lucide/svelte/icons/arrow-left';
    import ArrowRight from '@lucide/svelte/icons/arrow-right';
    import ExternalLink from '@lucide/svelte/icons/external-link';
    import CursorGlow from '@/components/CursorGlow.svelte';
    import GalleryGrid from '@/components/GalleryGrid.svelte';
    import SeoHead from '@/components/SeoHead.svelte';
    import SiteHeader from '@/components/SiteHeader.svelte';
    import SocialLinks from '@/components/SocialLinks.svelte';
    import { projectCovers } from '@/lib/portfolio';
    import type {
        PortfolioContent,
        PortfolioGalleryItem,
        PortfolioProject,
        PortfolioSeo,
    } from '@/types';

    let {
        portfolio,
        project,
        seo,
    }: {
        portfolio: PortfolioContent;
        project: PortfolioProject;
        seo: PortfolioSeo;
    } = $props();

    const profile = $derived(portfolio.profile);
    const socials = $derived(portfolio.socials);
    const mailto = $derived(
        `mailto:${profile.email}?subject=${encodeURIComponent(
            `Project inquiry: ${project.name}`,
        )}`,
    );
    const paragraphs = $derived(
        project.description
            .split(/\n+/)
            .map((paragraph) => paragraph.trim())
            .filter(Boolean),
    );
    const galleryItems = $derived(
        project.gallery
            .filter(
                (image): image is string =>
                    typeof image === 'string' &&
                    image !== '' &&
                    image !== project.image,
            )
            .map((image, index) => ({
                image,
                title: `${project.name} ${index + 1}`,
                caption: project.tags.join(' · '),
                link: null,
                featured: false,
            })),
    );
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

    <SiteHeader {portfolio} current="work" />

    <main
        class="mx-auto w-full max-w-5xl px-4 pt-28 pb-12 sm:px-6 sm:pt-32"
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <Link
                href="/work"
                class="inline-flex items-center gap-1.5 rounded-sm text-xs font-medium text-muted-foreground transition-colors hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
            >
                <ArrowLeft class="size-3.5" aria-hidden="true" />
                Back to all work
            </Link>

            {#if project.link}
                <a
                    href={project.link}
                    target="_blank"
                    rel="noreferrer noopener"
                    class="inline-flex h-10 items-center gap-2 rounded-full border border-border bg-background/70 px-5 text-sm font-semibold text-foreground transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                >
                    View live project
                    <ExternalLink class="size-4" aria-hidden="true" />
                </a>
            {/if}
        </div>

        <article class="mt-8">
            <header>
                <p
                    class="font-mono text-[10px] tracking-[0.2em] text-muted-foreground uppercase"
                >
                    Project / {project.year}
                </p>
                <h1
                    id="project-title"
                    class="mt-3 text-4xl leading-[1.05] font-semibold tracking-[-0.04em] sm:text-6xl"
                >
                    {project.name}
                </h1>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    {#each project.tags as tag, index (tag + index)}
                        <span
                            class="rounded-full border border-border/70 bg-background/60 px-3 py-1 text-xs font-medium text-muted-foreground"
                        >
                            {tag}
                        </span>
                    {/each}
                </div>
            </header>

            <div
                class="mt-8 overflow-hidden rounded-[1.5rem] border border-border/70 bg-card/60 shadow-[0_24px_70px_-50px_rgba(15,23,42,0.5)]"
            >
                {#if project.image}
                    <img
                        src={project.image}
                        alt={`${project.name} preview`}
                        class="aspect-[16/9] w-full object-cover"
                    />
                {:else}
                    <div
                        class={`relative aspect-[16/9] w-full ${projectCovers[project.cover] ?? projectCovers.blue}`}
                    >
                        <div
                            class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.07)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.07)_1px,transparent_1px)] [background-size:40px_40px]"
                            aria-hidden="true"
                        ></div>
                        <span
                            class="absolute -right-4 -bottom-16 text-[14rem] leading-none font-semibold text-white/10 select-none"
                            aria-hidden="true"
                        >
                            {project.name.charAt(0)}
                        </span>
                    </div>
                {/if}
            </div>

            <div class="mt-10 grid gap-10 lg:grid-cols-[minmax(0,1fr)_260px]">
                <div class="space-y-4 text-[15px] leading-8 text-muted-foreground">
                    {#each paragraphs as paragraph, index (index)}
                        <p class={index === 0 ? 'text-foreground/90' : ''}>
                            {paragraph}
                        </p>
                    {/each}
                </div>

                <aside class="h-fit rounded-2xl border border-border/70 bg-card/60 p-5">
                    <p
                        class="font-mono text-[10px] tracking-[0.16em] text-muted-foreground uppercase"
                    >
                        Project details
                    </p>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-xs text-muted-foreground">Year</dt>
                            <dd class="mt-0.5 font-medium">{project.year}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-muted-foreground">
                                Stack
                            </dt>
                            <dd class="mt-0.5 font-medium">
                                {#if project.tags.length > 0}
                                    {project.tags.join(', ')}
                                {:else}
                                    Not listed
                                {/if}
                            </dd>
                        </div>
                        {#if project.link}
                            <div>
                                <dt class="text-xs text-muted-foreground">
                                    Live site
                                </dt>
                                <dd class="mt-0.5">
                                    <a
                                        href={project.link}
                                        target="_blank"
                                        rel="noreferrer noopener"
                                        class="inline-flex items-center gap-1.5 font-medium text-[#0a66c2] hover:underline dark:text-sky-400"
                                    >
                                        Visit project
                                        <ExternalLink
                                            class="size-3.5"
                                            aria-hidden="true"
                                        />
                                    </a>
                                </dd>
                            </div>
                        {/if}
                    </dl>
                </aside>
            </div>

            {#if galleryItems.length > 0}
                <section class="mt-12" aria-labelledby="project-gallery">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2
                            id="project-gallery"
                            class="text-lg font-semibold tracking-tight"
                        >
                            Project gallery
                        </h2>
                        <span
                            class="font-mono text-[10px] tracking-[0.16em] text-muted-foreground uppercase"
                        >
                            {galleryItems.length} images
                        </span>
                    </div>
                    <GalleryGrid items={galleryItems} columns={2} />
                </section>
            {/if}

            <section
                class="mt-12 flex flex-col items-start justify-between gap-4 rounded-2xl border border-border/70 bg-card/60 p-6 sm:flex-row sm:items-center"
            >
                <div>
                    <p class="text-lg font-semibold tracking-tight">
                        Have a similar project?
                    </p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Tell me what you are building. I usually reply within a
                        day.
                    </p>
                </div>
                <a
                    href={mailto}
                    class="inline-flex h-10 items-center gap-2 rounded-full bg-foreground px-5 text-sm font-semibold text-background transition-opacity hover:opacity-90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                >
                    Start a conversation
                    <ArrowRight class="size-4" aria-hidden="true" />
                </a>
            </section>
        </article>
    </main>

    <footer
        class="mx-auto flex w-full max-w-5xl flex-col items-center justify-between gap-4 border-t border-border/60 px-4 py-8 text-center sm:flex-row sm:px-6 sm:text-left"
    >
        <p class="text-xs text-muted-foreground">
            © 2026 {profile.name}. {portfolio.footer_note}
        </p>
        <SocialLinks {socials} size="sm" />
    </footer>
</div>
