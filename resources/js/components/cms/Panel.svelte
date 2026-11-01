<script lang="ts">
    import type { Snippet } from 'svelte';

    let {
        title,
        description,
        children,
    }: {
        title: string;
        description?: string;
        children?: Snippet;
    } = $props();
</script>

<section
    class="overflow-hidden rounded-2xl border border-border/70 bg-card/80 shadow-[0_16px_50px_-40px_rgba(15,23,42,0.45)]"
>
    <header class="border-b border-border/60 px-5 py-4">
        <h2 class="text-base font-semibold tracking-tight">{title}</h2>
        {#if description}
            <p class="mt-1 text-sm text-muted-foreground">{description}</p>
        {/if}
    </header>
    <div class="p-5">
        {@render children?.()}
    </div>
</section>
