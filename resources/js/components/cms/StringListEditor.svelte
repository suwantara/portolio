<script lang="ts">
    import Plus from '@lucide/svelte/icons/plus';
    import X from '@lucide/svelte/icons/x';
    import InputError from '@/components/InputError.svelte';
    import { Input } from '@/components/ui/input';
    import { getError } from '@/lib/errors';
    import { cn } from '@/lib/utils';
    import Textarea from './Textarea.svelte';

    let {
        label,
        items = $bindable([]),
        errors = {},
        path,
        placeholder = '',
        addLabel = 'Add item',
        multiline = false,
        class: className = '',
    }: {
        label: string;
        items?: string[];
        errors?: Record<string, unknown>;
        path: string;
        placeholder?: string;
        addLabel?: string;
        multiline?: boolean;
        class?: string;
    } = $props();

    function addItem(): void {
        items = [...items, ''];
    }

    function removeItem(index: number): void {
        items = items.filter((_, itemIndex) => itemIndex !== index);
    }
</script>

<div class={cn('grid gap-2.5', className)}>
    <span class="text-sm font-medium leading-none">{label}</span>

    <div class="grid gap-2">
        {#each items as item, index (index)}
            <div class="flex items-start gap-2">
                <div class="min-w-0 flex-1">
                    {#if multiline}
                        <Textarea
                            bind:value={items[index]}
                            {placeholder}
                            rows={3}
                            aria-label={`${label} ${index + 1}`}
                        />
                    {:else}
                        <Input
                            bind:value={items[index]}
                            {placeholder}
                            aria-label={`${label} ${index + 1}`}
                        />
                    {/if}
                    <InputError
                        message={getError(errors, `${path}.${index}`)}
                    />
                </div>
                <button
                    type="button"
                    onclick={() => removeItem(index)}
                    aria-label={`Remove ${label} ${index + 1}`}
                    class="mt-1 grid size-8 shrink-0 place-items-center rounded-md text-muted-foreground transition-colors hover:bg-destructive/10 hover:text-destructive focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                >
                    <X class="size-4" aria-hidden="true" />
                </button>
            </div>
        {/each}
    </div>

    <button
        type="button"
        onclick={addItem}
        class="inline-flex h-9 w-fit items-center gap-1.5 rounded-md border border-dashed border-border bg-background/50 px-3 text-sm font-medium text-muted-foreground transition-colors hover:border-foreground/30 hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
    >
        <Plus class="size-4" aria-hidden="true" />
        {addLabel}
    </button>
</div>
