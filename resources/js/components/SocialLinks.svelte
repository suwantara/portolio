<script lang="ts">
    import Globe from '@lucide/svelte/icons/globe';
    import { socialIconPaths } from '@/lib/socials';
    import { cn } from '@/lib/utils';
    import type { PortfolioSocial } from '@/types';

    let {
        socials,
        size = 'md',
        class: className = '',
    }: {
        socials: PortfolioSocial[];
        size?: 'sm' | 'md';
        class?: string;
    } = $props();

    const boxSizes = {
        sm: 'size-8',
        md: 'size-10',
    };

    const iconSizes = {
        sm: 'size-3.5',
        md: 'size-4',
    };
</script>

<div class={cn('flex flex-wrap items-center gap-2', className)}>
    {#each socials as social (social.name + social.href)}
        <a
            href={social.href}
            target="_blank"
            rel="noreferrer noopener"
            aria-label={social.label}
            title={social.label}
            class={cn(
                'grid place-items-center rounded-full border border-border/70 bg-background/60 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none',
                boxSizes[size],
            )}
        >
            {#if social.name === 'website' || !socialIconPaths[social.name]}
                <Globe class={iconSizes[size]} aria-hidden="true" />
            {:else}
                <svg
                    class={iconSizes[size]}
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path d={socialIconPaths[social.name]} />
                </svg>
            {/if}
        </a>
    {/each}
</div>
