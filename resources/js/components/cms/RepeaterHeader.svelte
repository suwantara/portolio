<script lang="ts">
    import ArrowDown from '@lucide/svelte/icons/arrow-down';
    import ArrowUp from '@lucide/svelte/icons/arrow-up';
    import Trash2 from '@lucide/svelte/icons/trash-2';

    let {
        title,
        subtitle,
        canMoveUp = false,
        canMoveDown = false,
        onMoveUp,
        onMoveDown,
        onRemove,
    }: {
        title: string;
        subtitle?: string;
        canMoveUp?: boolean;
        canMoveDown?: boolean;
        onMoveUp: () => void;
        onMoveDown: () => void;
        onRemove: () => void;
    } = $props();

    const button =
        'grid size-8 place-items-center rounded-md text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none disabled:pointer-events-none disabled:opacity-30';
</script>

<div class="flex items-start justify-between gap-3">
    <div class="min-w-0">
        <p class="truncate text-sm font-semibold">{title}</p>
        {#if subtitle}
            <p class="mt-0.5 truncate text-xs text-muted-foreground">
                {subtitle}
            </p>
        {/if}
    </div>
    <div class="flex shrink-0 items-center gap-0.5">
        <button
            type="button"
            class={button}
            onclick={onMoveUp}
            disabled={!canMoveUp}
            aria-label="Move up"
        >
            <ArrowUp class="size-4" aria-hidden="true" />
        </button>
        <button
            type="button"
            class={button}
            onclick={onMoveDown}
            disabled={!canMoveDown}
            aria-label="Move down"
        >
            <ArrowDown class="size-4" aria-hidden="true" />
        </button>
        <button
            type="button"
            class="{button} hover:bg-destructive/10 hover:text-destructive"
            onclick={onRemove}
            aria-label="Remove item"
        >
            <Trash2 class="size-4" aria-hidden="true" />
        </button>
    </div>
</div>
