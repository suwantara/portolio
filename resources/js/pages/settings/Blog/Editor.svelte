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
    import { Link, useForm, useHttp } from '@inertiajs/svelte';
    import Bold from '@lucide/svelte/icons/bold';
    import Code from '@lucide/svelte/icons/code';
    import Eye from '@lucide/svelte/icons/eye';
    import Heading2 from '@lucide/svelte/icons/heading-2';
    import ImagePlus from '@lucide/svelte/icons/image-plus';
    import Italic from '@lucide/svelte/icons/italic';
    import Link2 from '@lucide/svelte/icons/link-2';
    import List from '@lucide/svelte/icons/list';
    import ArrowLeft from '@lucide/svelte/icons/arrow-left';
    import ExternalLink from '@lucide/svelte/icons/external-link';
    import { onDestroy, onMount, tick } from 'svelte';
    import { toast } from 'svelte-sonner';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { cn } from '@/lib/utils';
    import type {
        BlogPostForm,
        PostCategory,
        PostStatus,
        UploadedImage,
    } from '@/types';

    type EditablePost = {
        title: string;
        slug: string;
        category: PostCategory;
        excerpt: string | null;
        cover_image: string | null;
        content: string;
        status: PostStatus;
    };

    let {
        post = null,
        action,
        method = 'post',
        media_url,
        preview_url,
        categories,
    }: {
        post?: EditablePost | null;
        action: string;
        method?: 'post' | 'put';
        media_url: string;
        preview_url: string;
        categories: PostCategory[];
    } = $props();

    const form = useForm<BlogPostForm>(() => ({
        title: post?.title ?? '',
        slug: post?.slug ?? '',
        category: post?.category ?? categories[0] ?? 'tutorial',
        excerpt: post?.excerpt ?? '',
        cover_image: post?.cover_image ?? null,
        content: post?.content ?? '',
        status: post?.status ?? 'draft',
    }));

    const inlineUpload = useHttp<{ file: File | null }, UploadedImage>(
        () => ({ file: null }),
    );
    const coverUpload = useHttp<{ file: File | null }, UploadedImage>(() => ({
        file: null,
    }));
    const preview = useHttp<{ content: string }, { html: string }>({
        content: '',
    });

    let textarea = $state<HTMLTextAreaElement | null>(null);
    let coverInput = $state<HTMLInputElement | null>(null);
    let inlineInput = $state<HTMLInputElement | null>(null);
    let previewHtml = $state('');
    let previewError = $state<string | null>(null);
    let previewTimer: ReturnType<typeof setTimeout> | undefined;

    const errors = $derived(form.errors as Record<string, string | undefined>);
    const categoryLabels: Record<PostCategory, string> = {
        tutorial: 'Tutorial',
        tips: 'Tips',
        progress: 'Progress',
        notes: 'Notes',
    };

    function schedulePreview(): void {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(() => {
            if (!form.content.trim()) {
                previewHtml = '';
                previewError = null;

                return;
            }

            preview.content = form.content;
            preview.cancel();
            void preview
                .post(preview_url, {
                    onSuccess: (data) => {
                        previewHtml = data.html;
                        previewError = null;
                    },
                    onError: () => {
                        previewError = 'Preview could not be rendered.';
                    },
                })
                .catch(() => {
                    previewError = 'Preview could not be rendered.';
                });
        }, 350);
    }

    onMount(() => {
        schedulePreview();
    });

    onDestroy(() => {
        clearTimeout(previewTimer);
        preview.cancel();
    });

    function insertAtCursor(text: string): void {
        const element = textarea;

        if (!element) {
            form.content += text;
            schedulePreview();

            return;
        }

        const start = element.selectionStart ?? form.content.length;
        const end = element.selectionEnd ?? start;
        form.content = `${form.content.slice(0, start)}${text}${form.content.slice(end)}`;

        void tick().then(() => {
            element.focus();
            element.setSelectionRange(
                start + text.length,
                start + text.length,
            );
        });

        schedulePreview();
    }

    function wrapSelection(before: string, after: string, fallback: string): void {
        const element = textarea;

        if (!element) {
            insertAtCursor(`${before}${fallback}${after}`);

            return;
        }

        const start = element.selectionStart ?? 0;
        const end = element.selectionEnd ?? start;
        const selected = form.content.slice(start, end) || fallback;
        const replacement = `${before}${selected}${after}`;

        form.content = `${form.content.slice(0, start)}${replacement}${form.content.slice(end)}`;

        void tick().then(() => {
            element.focus();
            element.setSelectionRange(start + before.length, start + before.length + selected.length);
        });

        schedulePreview();
    }

    function prefixLine(prefix: string): void {
        const element = textarea;

        if (!element) {
            form.content += `\n${prefix}`;
            schedulePreview();

            return;
        }

        const cursor = element.selectionStart ?? form.content.length;
        const lineStart = form.content.lastIndexOf('\n', cursor - 1) + 1;

        form.content = `${form.content.slice(0, lineStart)}${prefix}${form.content.slice(lineStart)}`;

        void tick().then(() => {
            element.focus();
            element.setSelectionRange(cursor + prefix.length, cursor + prefix.length);
        });

        schedulePreview();
    }

    function chooseInlineImage(): void {
        inlineInput?.click();
    }

    function chooseCover(): void {
        coverInput?.click();
    }

    async function uploadInlineImage(file: File): Promise<void> {
        inlineUpload.file = file;

        try {
            const data = await inlineUpload.post(media_url);
            const alt = file.name
                .replace(/\.[^.]+$/, '')
                .replaceAll('[', '')
                .replaceAll(']', '');
            insertAtCursor(`\n![${alt}](${data.url})\n`);
            toast.success('Image inserted.');
        } catch {
            toast.error('Image upload failed. Use JPG, PNG, WebP, or AVIF.');
        } finally {
            inlineUpload.reset();
        }
    }

    async function handleInlineFile(event: Event): Promise<void> {
        const input = event.currentTarget as HTMLInputElement;
        const file = input.files?.[0];
        input.value = '';

        if (file) {
            await uploadInlineImage(file);
        }
    }

    async function handleCoverFile(event: Event): Promise<void> {
        const input = event.currentTarget as HTMLInputElement;
        const file = input.files?.[0];
        input.value = '';

        if (!file) {
            return;
        }

        coverUpload.file = file;

        try {
            const data = await coverUpload.post(media_url);
            form.cover_image = data.url;
            toast.success('Cover uploaded.');
        } catch {
            toast.error('Cover upload failed. Use JPG, PNG, WebP, or AVIF.');
        } finally {
            coverUpload.reset();
        }
    }

    function handlePaste(event: ClipboardEvent): void {
        const items = Array.from(event.clipboardData?.items ?? []);
        const image = items.find((item) => item.type.startsWith('image/'));
        const file = image?.getAsFile();

        if (!file) {
            return;
        }

        event.preventDefault();
        void uploadInlineImage(file);
    }

    function save(): void {
        const options = {
            preserveScroll: true,
        };

        if (method === 'put') {
            form.put(action, options);

            return;
        }

        form.post(action, options);
    }
</script>

<AppHead title={post ? 'Edit post' : 'New post'} />

<div class="mx-auto flex w-full max-w-6xl flex-col gap-6 p-4 sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col gap-2">
            <Link
                href="/settings/blog"
                class="inline-flex w-fit items-center gap-1.5 text-xs font-medium text-muted-foreground transition-colors hover:text-foreground"
            >
                <ArrowLeft class="size-3.5" aria-hidden="true" />
                Back to blog
            </Link>
            <Heading
                title={post ? 'Edit post' : 'New post'}
                description="Write in Markdown. Use the toolbar or paste an image directly."
            />
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {#if post?.slug && post.status === 'published'}
                <Button asChild variant="outline">
                    {#snippet children(props)}
                        <Link
                            {...props}
                            href={`/blog/${post.slug}`}
                            target="_blank"
                            class={props.class}
                        >
                            <ExternalLink class="size-4" aria-hidden="true" />
                            View post
                        </Link>
                    {/snippet}
                </Button>
            {/if}
            <Button
                type="button"
                variant="secondary"
                disabled={form.processing}
                onclick={save}
            >
                {form.processing ? 'Saving...' : 'Save post'}
            </Button>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
        <div class="flex min-w-0 flex-col gap-6">
            <section class="rounded-2xl border border-border/70 bg-card/80 p-5 sm:p-6">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2 sm:col-span-2">
                        <label for="title" class="text-sm font-medium">Title</label>
                        <Input
                            id="title"
                            bind:value={form.title}
                            placeholder="Build a tiny Markdown blog with Laravel"
                        />
                        <InputError message={errors.title} />
                    </div>

                    <div class="grid gap-2">
                        <label for="slug" class="text-sm font-medium">Slug</label>
                        <Input
                            id="slug"
                            bind:value={form.slug}
                            placeholder="auto-generated-from-title"
                        />
                        <p class="text-xs text-muted-foreground">
                            Leave blank to keep or generate a clean slug.
                        </p>
                        <InputError message={errors.slug} />
                    </div>

                    <div class="grid gap-2">
                        <label for="category" class="text-sm font-medium">Category</label>
                        <select
                            id="category"
                            bind:value={form.category}
                            class="h-9 rounded-md border border-input bg-background px-3 text-sm shadow-sm focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                        >
                            {#each categories as category (category)}
                                <option value={category}>
                                    {categoryLabels[category] ?? category}
                                </option>
                            {/each}
                        </select>
                        <InputError message={errors.category} />
                    </div>

                    <div class="grid gap-2">
                        <label for="status" class="text-sm font-medium">Status</label>
                        <select
                            id="status"
                            bind:value={form.status}
                            class="h-9 rounded-md border border-input bg-background px-3 text-sm shadow-sm focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                        >
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                        <InputError message={errors.status} />
                    </div>

                    <div class="grid gap-2 sm:col-span-2">
                        <label for="excerpt" class="text-sm font-medium">
                            Excerpt
                        </label>
                        <textarea
                            id="excerpt"
                            bind:value={form.excerpt}
                            rows="3"
                            maxlength="500"
                            placeholder="A short summary for the blog index and search results."
                            class="w-full resize-y rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                        ></textarea>
                        <InputError message={errors.excerpt} />
                    </div>

                    <div class="grid gap-3 sm:col-span-2">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-medium">Cover image</span>
                            <Button
                                type="button"
                                variant="secondary"
                                size="sm"
                                onclick={chooseCover}
                                disabled={coverUpload.processing}
                            >
                                {coverUpload.processing ? 'Uploading...' : 'Upload cover'}
                            </Button>
                        </div>
                        <input
                            bind:this={coverInput}
                            type="file"
                            accept="image/jpeg,image/png,image/webp,image/avif"
                            class="sr-only"
                            onchange={handleCoverFile}
                            aria-label="Upload cover image"
                        />
                        {#if form.cover_image}
                            <div class="flex items-start gap-3">
                                <img
                                    src={form.cover_image}
                                    alt="Post cover preview"
                                    class="h-28 w-44 rounded-xl border border-border/70 object-cover"
                                />
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    onclick={() => (form.cover_image = null)}
                                >
                                    Remove
                                </Button>
                            </div>
                        {/if}
                        <Input
                            bind:value={form.cover_image}
                            placeholder="/storage/blog/images/cover.png or https://..."
                        />
                        <InputError message={errors.cover_image} />
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-border/70 bg-card/80 p-5 sm:p-6">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold">Content</h2>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            `# Heading`, **bold**, lists, links, code fences, and images.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-1">
                        <button type="button" class="toolbar-button" onclick={() => prefixLine('## ')} aria-label="Heading">
                            <Heading2 class="size-4" aria-hidden="true" />
                        </button>
                        <button type="button" class="toolbar-button" onclick={() => wrapSelection('**', '**', 'bold text')} aria-label="Bold">
                            <Bold class="size-4" aria-hidden="true" />
                        </button>
                        <button type="button" class="toolbar-button" onclick={() => wrapSelection('*', '*', 'italic text')} aria-label="Italic">
                            <Italic class="size-4" aria-hidden="true" />
                        </button>
                        <button type="button" class="toolbar-button" onclick={() => wrapSelection('[', '](https://)', 'link text')} aria-label="Link">
                            <Link2 class="size-4" aria-hidden="true" />
                        </button>
                        <button type="button" class="toolbar-button" onclick={() => wrapSelection('`', '`', 'code')} aria-label="Inline code">
                            <Code class="size-4" aria-hidden="true" />
                        </button>
                        <button type="button" class="toolbar-button" onclick={() => prefixLine('- ')} aria-label="List">
                            <List class="size-4" aria-hidden="true" />
                        </button>
                        <button type="button" class="toolbar-button" onclick={chooseInlineImage} aria-label="Upload image">
                            <ImagePlus class="size-4" aria-hidden="true" />
                        </button>
                        <input
                            bind:this={inlineInput}
                            type="file"
                            accept="image/jpeg,image/png,image/webp,image/avif"
                            class="sr-only"
                            onchange={handleInlineFile}
                            aria-label="Insert image"
                        />
                    </div>
                </div>

                <textarea
                    bind:this={textarea}
                    value={form.content}
                    oninput={(event) => {
                        form.content = event.currentTarget.value;
                        schedulePreview();
                    }}
                    onpaste={handlePaste}
                    spellcheck="true"
                    placeholder={`## Start writing\n\nTell the story, explain the steps, and add images with the button or paste them directly.`}
                    class="min-h-[32rem] w-full resize-y rounded-xl border border-input bg-background/70 px-4 py-3 font-mono text-[13px] leading-6 shadow-sm focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                ></textarea>
                <div class="mt-2 flex items-center justify-between gap-3 text-xs text-muted-foreground">
                    <span>{form.content.length.toLocaleString()} characters</span>
                    {#if inlineUpload.processing}
                        <span>Uploading image...</span>
                    {:else if inlineUpload.progress}
                        <span>{inlineUpload.progress.percentage}%</span>
                    {/if}
                </div>
                <InputError class="mt-2" message={errors.content} />
            </section>
        </div>

        <aside class="min-w-0 xl:sticky xl:top-6 xl:self-start">
            <div class="rounded-2xl border border-border/70 bg-card/80 p-5">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <Eye class="size-4 text-muted-foreground" aria-hidden="true" />
                        <h2 class="text-sm font-semibold">Preview</h2>
                    </div>
                    {#if preview.processing}
                        <span class="text-[10px] tracking-[0.14em] text-muted-foreground uppercase">
                            Rendering...
                        </span>
                    {/if}
                </div>

                {#if previewError}
                    <p class="text-sm text-destructive">{previewError}</p>
                {:else if previewHtml}
                    <article class="blog-content max-h-[42rem] overflow-y-auto pr-1">
                        {@html previewHtml}
                    </article>
                {:else}
                    <div class="rounded-xl border border-dashed border-border/80 px-4 py-10 text-center text-sm text-muted-foreground">
                        Your rendered Markdown will appear here.
                    </div>
                {/if}
            </div>
        </aside>
    </div>
</div>

<style>
    .toolbar-button {
        display: grid;
        place-items: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.375rem;
        color: var(--color-muted-foreground);
        transition: background-color 150ms ease, color 150ms ease;
    }

    .toolbar-button:hover {
        background: var(--color-muted);
        color: var(--color-foreground);
    }
</style>
