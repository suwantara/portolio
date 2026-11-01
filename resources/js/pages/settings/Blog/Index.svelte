<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            {
                title: 'Blog',
                href: '/settings/blog',
            },
        ],
    };
</script>

<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import ArrowLeft from '@lucide/svelte/icons/arrow-left';
    import ArrowRight from '@lucide/svelte/icons/arrow-right';
    import Pencil from '@lucide/svelte/icons/pencil';
    import Plus from '@lucide/svelte/icons/plus';
    import Trash2 from '@lucide/svelte/icons/trash-2';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { cn } from '@/lib/utils';
    import type { ManagedPost, Paginated } from '@/types';

    let { posts }: { posts: Paginated<ManagedPost> } = $props();

    const dateFormatter = new Intl.DateTimeFormat('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });

    const categoryLabels: Record<string, string> = {
        tutorial: 'Tutorial',
        tips: 'Tips',
        progress: 'Progress',
        notes: 'Notes',
    };

    const previous = $derived(posts.links[0] ?? null);
    const next = $derived(posts.links[posts.links.length - 1] ?? null);

    function formatDate(value: string | null): string {
        return value ? dateFormatter.format(new Date(value)) : '—';
    }

    function removePost(post: ManagedPost): void {
        if (
            !window.confirm(
                `Delete “${post.title}”? This action cannot be undone.`,
            )
        ) {
            return;
        }

        router.delete(`/settings/blog/${post.slug}`, {
            preserveScroll: true,
        });
    }
</script>

<AppHead title="Blog" />

<div class="mx-auto flex w-full max-w-5xl flex-col gap-6 p-4 sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <Heading
            title="Blog"
            description="Write tutorials, tips, progress notes, and project stories."
        />

        <Button asChild>
            {#snippet children(props)}
                <Link {...props} href="/settings/blog/create" class={props.class}>
                    <Plus class="size-4" aria-hidden="true" />
                    New post
                </Link>
            {/snippet}
        </Button>
    </div>

    {#if posts.data.length > 0}
        <div class="overflow-hidden rounded-2xl border border-border/70 bg-card/80">
            <div
                class="hidden grid-cols-[minmax(0,1fr)_120px_110px_110px_80px] gap-4 border-b border-border/60 px-5 py-3 text-[10px] tracking-[0.16em] text-muted-foreground uppercase lg:grid"
            >
                <span>Post</span>
                <span>Status</span>
                <span>Updated</span>
                <span>Published</span>
                <span class="text-right">Actions</span>
            </div>

            {#each posts.data as post (post.id)}
                <article
                    class="grid gap-3 border-b border-border/60 px-5 py-4 last:border-b-0 lg:grid-cols-[minmax(0,1fr)_120px_110px_110px_80px] lg:items-center lg:gap-4"
                >
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <Link
                                href={`/settings/blog/${post.slug}/edit`}
                                class="truncate font-medium hover:underline"
                            >
                                {post.title}
                            </Link>
                            <span
                                class="rounded-full bg-muted px-2 py-0.5 text-[10px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                            >
                                {categoryLabels[post.category] ?? post.category}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">
                            /blog/{post.slug} · {post.reading_minutes} min read
                        </p>
                    </div>

                    <span
                        class={cn(
                            'w-fit rounded-full px-2.5 py-1 text-[10px] font-semibold tracking-[0.12em] uppercase',
                            post.status === 'published'
                                ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                : 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
                        )}
                    >
                        {post.status}
                    </span>

                    <span class="text-xs text-muted-foreground">
                        {formatDate(post.updated_at)}
                    </span>

                    <span class="text-xs text-muted-foreground">
                        {formatDate(post.published_at)}
                    </span>

                    <div class="flex items-center gap-1 lg:justify-end">
                        <Button
                            asChild
                            variant="ghost"
                            size="icon"
                            aria-label={`Edit ${post.title}`}
                        >
                            {#snippet children(props)}
                                <Link
                                    {...props}
                                    href={`/settings/blog/${post.slug}/edit`}
                                    class={props.class}
                                >
                                    <Pencil class="size-4" aria-hidden="true" />
                                </Link>
                            {/snippet}
                        </Button>
                        <Button
                            variant="ghost"
                            size="icon"
                            aria-label={`Delete ${post.title}`}
                            onclick={() => removePost(post)}
                        >
                            <Trash2 class="size-4" aria-hidden="true" />
                        </Button>
                    </div>
                </article>
            {/each}
        </div>

        <div
            class="flex flex-wrap items-center justify-between gap-3 text-sm text-muted-foreground"
        >
            <p>Page {posts.current_page} of {posts.last_page}</p>
            <div class="flex items-center gap-2">
                {#if previous?.url}
                    <Link
                        href={previous.url}
                        class="inline-flex h-8 items-center gap-1.5 rounded-md border border-input bg-background px-3 text-xs font-medium transition-colors hover:bg-accent"
                    >
                        <ArrowLeft class="size-3.5" aria-hidden="true" />
                        Previous
                    </Link>
                {:else}
                    <span
                        class="inline-flex h-8 items-center gap-1.5 rounded-md border border-input px-3 text-xs font-medium opacity-45"
                    >
                        <ArrowLeft class="size-3.5" aria-hidden="true" />
                        Previous
                    </span>
                {/if}
                {#if next?.url}
                    <Link
                        href={next.url}
                        class="inline-flex h-8 items-center gap-1.5 rounded-md border border-input bg-background px-3 text-xs font-medium transition-colors hover:bg-accent"
                    >
                        Next
                        <ArrowRight class="size-3.5" aria-hidden="true" />
                    </Link>
                {:else}
                    <span
                        class="inline-flex h-8 items-center gap-1.5 rounded-md border border-input px-3 text-xs font-medium opacity-45"
                    >
                        Next
                        <ArrowRight class="size-3.5" aria-hidden="true" />
                    </span>
                {/if}
            </div>
        </div>
    {:else}
        <div
            class="rounded-2xl border border-dashed border-border/80 bg-card/50 px-6 py-16 text-center"
        >
            <p class="text-base font-semibold">No posts yet.</p>
            <p class="mt-1 text-sm text-muted-foreground">
                Start with a tutorial, a short tip, or a project build log.
            </p>
            <Button asChild class="mt-5">
                {#snippet children(props)}
                    <Link
                        {...props}
                        href="/settings/blog/create"
                        class={props.class}
                    >
                        <Plus class="size-4" aria-hidden="true" />
                        Write the first post
                    </Link>
                {/snippet}
            </Button>
        </div>
    {/if}
</div>
