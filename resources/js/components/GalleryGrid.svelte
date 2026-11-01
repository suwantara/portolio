<script lang="ts">
    import ExternalLink from '@lucide/svelte/icons/external-link';
    import Eye from '@lucide/svelte/icons/eye';
    import X from '@lucide/svelte/icons/x';
    import {
        Dialog,
        DialogClose,
        DialogContent,
        DialogDescription,
        DialogTitle,
    } from '@/components/ui/dialog';
    import { cn } from '@/lib/utils';
    import type { PortfolioGalleryItem } from '@/types';

    let {
        items,
        columns = 2,
        class: className = '',
    }: {
        items: PortfolioGalleryItem[];
        columns?: 2 | 3;
        class?: string;
    } = $props();

    const visibleItems = $derived(items.filter((item) => item.image));

    const gridColumns = {
        2: 'sm:grid-cols-2',
        3: 'sm:grid-cols-2 lg:grid-cols-3',
    };

    let galleryOpen = $state(false);
    let activeItem = $state<PortfolioGalleryItem | null>(null);

    function runClick(handler: unknown): void {
        if (typeof handler === 'function') {
            handler();
        }
    }

    function openItem(item: PortfolioGalleryItem): void {
        activeItem = item;
        galleryOpen = true;
    }
</script>

<div class={cn('grid gap-3', gridColumns[columns], className)}>
    {#each visibleItems as item, index (index)}
        <button
            type="button"
            onclick={() => openItem(item)}
            class={cn(
                'group relative overflow-hidden rounded-xl border border-border/70 text-left focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none',
                item.featured && 'sm:col-span-2',
            )}
            aria-label={`Open image: ${item.title}`}
        >
            <img
                src={item.image ?? ''}
                alt={item.title}
                loading="lazy"
                class={cn(
                    'w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]',
                    item.featured ? 'aspect-[16/9]' : 'aspect-[4/3]',
                )}
            />
            <span
                class="absolute inset-0 bg-linear-to-t from-black/75 via-black/10 to-transparent"
                aria-hidden="true"
            ></span>
            <span
                class="absolute top-3 right-3 grid size-8 place-items-center rounded-full bg-white/15 text-white backdrop-blur"
                aria-hidden="true"
            >
                <Eye class="size-4" />
            </span>
            <span class="absolute inset-x-0 bottom-0 p-4 text-white">
                <span class="block text-sm font-semibold">{item.title}</span>
                {#if item.caption}
                    <span class="mt-0.5 line-clamp-1 block text-xs text-white/75">
                        {item.caption}
                    </span>
                {/if}
            </span>
        </button>
    {/each}
</div>

<Dialog bind:open={galleryOpen}>
    <DialogContent class="max-w-4xl p-3 sm:p-4">
        {#if activeItem}
            <div class="flex items-start justify-between gap-4 px-1 pb-3">
                <div>
                    <DialogTitle>{activeItem.title}</DialogTitle>
                    {#if activeItem.caption}
                        <DialogDescription>
                            {activeItem.caption}
                        </DialogDescription>
                    {/if}
                </div>
                <DialogClose asChild>
                    {#snippet children(props)}
                        <button
                            type="button"
                            onclick={() => runClick(props.onClick)}
                            aria-label="Close image viewer"
                            class="grid size-8 shrink-0 place-items-center rounded-full text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                        >
                            <X class="size-4" aria-hidden="true" />
                        </button>
                    {/snippet}
                </DialogClose>
            </div>

            {#if activeItem.image}
                <img
                    src={activeItem.image}
                    alt={activeItem.title}
                    class="max-h-[70vh] w-full rounded-lg bg-black/5 object-contain dark:bg-white/5"
                />
            {/if}

            {#if activeItem.link}
                <div class="mt-3 flex justify-end">
                    <a
                        href={activeItem.link}
                        target="_blank"
                        rel="noreferrer noopener"
                        class="inline-flex h-9 items-center gap-1.5 rounded-full bg-foreground px-4 text-sm font-semibold text-background transition-opacity hover:opacity-90"
                    >
                        Visit link
                        <ExternalLink class="size-3.5" aria-hidden="true" />
                    </a>
                </div>
            {/if}
        {/if}
    </DialogContent>
</Dialog>
