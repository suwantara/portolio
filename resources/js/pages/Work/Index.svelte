<script lang="ts">
    import { Link, usePoll } from '@inertiajs/svelte';
    import ArrowLeft from '@lucide/svelte/icons/arrow-left';
    import Plus from '@lucide/svelte/icons/plus';
    import CursorGlow from '@/components/CursorGlow.svelte';
    import ProjectCard from '@/components/ProjectCard.svelte';
    import SeoHead from '@/components/SeoHead.svelte';
    import SiteHeader from '@/components/SiteHeader.svelte';
    import SocialLinks from '@/components/SocialLinks.svelte';
    import type { PortfolioContent, PortfolioSeo } from '@/types';

    let {
        portfolio,
        seo,
    }: { portfolio: PortfolioContent; seo: PortfolioSeo } = $props();

    usePoll(5000, { only: ['portfolio'] });

    const profile = $derived(portfolio.profile);
    const socials = $derived(portfolio.socials);
    const projects = $derived(portfolio.projects);
    const workHeader = $derived(portfolio.work_header);
    const workDescription = $derived(
        workHeader.description.replaceAll('{count}', String(projects.length)),
    );

    const pageSize = 9;
    let visibleCount = $state(pageSize);

    const visibleProjects = $derived(projects.slice(0, visibleCount));
    const hasMore = $derived(visibleCount < projects.length);

    function loadMore(): void {
        visibleCount = Math.min(visibleCount + pageSize, projects.length);
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

    <SiteHeader {portfolio} current="work" />

    <main
        class="mx-auto w-full max-w-6xl px-4 pt-28 pb-12 sm:px-6 sm:pt-32"
    >
        <header
            class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
        >
            <div>
                <p
                    class="font-mono text-[10px] tracking-[0.2em] text-muted-foreground uppercase"
                >
                    {workHeader.eyebrow}
                </p>
                <h1
                    class="mt-2 text-4xl font-semibold tracking-[-0.035em] sm:text-5xl"
                >
                    {workHeader.title}
                </h1>
                <p
                    class="mt-3 max-w-xl text-sm leading-relaxed text-muted-foreground sm:text-[15px]"
                >
                    {workDescription}
                </p>
            </div>

            <Link
                href="/"
                class="inline-flex h-10 w-fit items-center gap-2 rounded-full border border-border bg-background/70 px-5 text-sm font-semibold text-foreground transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
            >
                <ArrowLeft class="size-4" aria-hidden="true" />
                Back to profile
            </Link>
        </header>

        {#if projects.length > 0}
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                {#each visibleProjects as project, index (project.name + index)}
                    <ProjectCard {project} {index} />
                {/each}
            </div>

            <div class="mt-8 flex flex-col items-center gap-3">
                <p
                    class="font-mono text-[10px] tracking-[0.16em] text-muted-foreground uppercase"
                >
                    Showing {visibleProjects.length} of {projects.length}
                </p>

                {#if hasMore}
                    <button
                        type="button"
                        onclick={loadMore}
                        class="inline-flex h-10 items-center gap-2 rounded-full border border-border bg-background/70 px-5 text-sm font-semibold text-foreground transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                    >
                        <Plus class="size-4" aria-hidden="true" />
                        Load more projects
                    </button>
                {/if}
            </div>
        {:else}
            <div
                class="rounded-2xl border border-dashed border-border/80 bg-card/50 px-6 py-16 text-center"
            >
                <p class="text-base font-semibold">No projects yet.</p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Add projects through the portfolio CMS.
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
