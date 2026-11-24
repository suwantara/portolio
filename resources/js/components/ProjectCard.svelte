<script lang="ts">
    import ArrowRight from '@lucide/svelte/icons/arrow-right';
    import ArrowUpRight from '@lucide/svelte/icons/arrow-up-right';
    import ExternalLink from '@lucide/svelte/icons/external-link';
    import { projectCovers, projectNumber } from '@/lib/portfolio';
    import { cn } from '@/lib/utils';
    import type { PortfolioProject } from '@/types';

    let {
        project,
        index,
        wide = false,
    }: {
        project: PortfolioProject;
        index: number;
        wide?: boolean;
    } = $props();
</script>

<article
    class={cn(
        'group flex flex-col overflow-hidden rounded-xl border border-border/70 bg-background/40',
        wide && 'sm:col-span-2',
    )}
>
    <div
        class={cn(
            'relative overflow-hidden',
            projectCovers[project.cover] ?? projectCovers.blue,
            wide ? 'h-48' : 'h-40',
        )}
    >
        {#if project.image}
            <img
                src={project.image}
                alt={`${project.name} project preview`}
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

        {#if wide}
            <div
                class="absolute right-6 bottom-0 hidden w-52 rounded-t-xl border border-white/15 bg-white/10 p-3 backdrop-blur-md sm:block"
                aria-hidden="true"
            >
                <div class="flex gap-1">
                    <span class="size-1.5 rounded-full bg-white/40"></span>
                    <span class="size-1.5 rounded-full bg-white/25"></span>
                    <span class="size-1.5 rounded-full bg-white/25"></span>
                </div>
                <div class="mt-3 space-y-1.5">
                    <div class="h-1.5 w-3/4 rounded-full bg-white/30"></div>
                    <div class="h-1.5 w-1/2 rounded-full bg-white/20"></div>
                    <div class="h-1.5 w-2/3 rounded-full bg-white/15"></div>
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

    <div class="flex flex-1 flex-col p-4 sm:p-5">
        <div class="flex items-start justify-between gap-3">
            <h3
                class="line-clamp-2 min-w-0 flex-1 font-semibold"
                title={project.name}
            >
                {project.name}
            </h3>
            <span
                class="shrink-0 font-mono text-[10px] tracking-wider text-muted-foreground uppercase"
            >
                {project.year}
            </span>
        </div>
        <p
            class="mt-1 line-clamp-3 min-h-[4.25rem] text-sm leading-relaxed text-muted-foreground"
            title={project.description}
        >
            {project.description}
        </p>
        <div class="mt-3 flex flex-wrap gap-1.5">
            {#each project.tags as tag, tagIndex (tag + tagIndex)}
                <span
                    class="rounded-md bg-muted px-2 py-1 text-[11px] font-medium text-muted-foreground"
                >
                    {tag}
                </span>
            {/each}
        </div>
        <div
            class="mt-auto flex flex-wrap items-center gap-x-3 gap-y-1 pt-3"
        >
            <a
                href={`/work/${project.slug}`}
                class="inline-flex items-center gap-1.5 rounded-sm text-xs font-medium text-[#0a66c2] hover:underline focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none dark:text-sky-400"
            >
                View case study
                <ArrowRight class="size-3.5" aria-hidden="true" />
            </a>
            {#if project.link}
                <a
                    href={project.link}
                    target="_blank"
                    rel="noreferrer noopener"
                    class="inline-flex items-center gap-1.5 rounded-sm text-xs font-medium text-muted-foreground transition-colors hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none dark:hover:text-white"
                >
                    Live site
                    <ExternalLink class="size-3.5" aria-hidden="true" />
                </a>
            {/if}
        </div>
    </div>
</article>
