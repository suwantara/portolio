<script lang="ts">
    import { useHttp } from '@inertiajs/svelte';
    import FileText from '@lucide/svelte/icons/file-text';
    import ImageIcon from '@lucide/svelte/icons/image';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { getError } from '@/lib/errors';
    import { destroy as destroyMedia } from '@/routes/portfolio/media';
    import { store as storeMedia } from '@/routes/portfolio/media';

    type Kind = 'image' | 'document';

    type UploadedMedia = {
        url: string;
        path: string;
        name: string;
        mime: string;
        size: number;
        kind: Kind;
    };

    let {
        value = $bindable<string | null>(null),
        kind,
        label,
        error,
        hint,
    }: {
        value?: string | null;
        kind: Kind;
        label: string;
        error?: string;
        hint?: string;
    } = $props();

    const upload = useHttp<{ file: File | null; kind: Kind }, UploadedMedia>(
        () => ({ file: null, kind }),
    );

    const deletion = useHttp<{ url: string }>({ url: '' });

    const accept = $derived(
        kind === 'image'
            ? 'image/jpeg,image/png,image/webp,image/avif'
            : '.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.csv,application/pdf',
    );

    let fileInput = $state<HTMLInputElement>();
    let localError = $state<string | null>(null);

    const uploadError = $derived(
        localError ??
            getError(
                upload.errors as unknown as Record<string, unknown>,
                'file',
            ),
    );

    function chooseFile(): void {
        fileInput?.click();
    }

    async function handleFile(event: Event): Promise<void> {
        const input = event.currentTarget as HTMLInputElement;
        const file = input.files?.[0];

        if (!file) {
            return;
        }

        localError = null;
        upload.file = file;
        upload.kind = kind;

        const previous = value;

        await upload.post(storeMedia().url, {
            onSuccess: (data) => {
                if (
                    previous &&
                    previous !== data.url &&
                    previous.includes('/storage/portfolio/')
                ) {
                    deletion.url = previous;
                    void deletion.delete(destroyMedia().url);
                }

                value = data.url;
                input.value = '';
                upload.reset();
            },
            onError: () => {
                localError = 'Upload failed. Check the file type and size.';
                input.value = '';
                upload.reset();
            },
        });
    }

    function removeFile(): void {
        const url = value;

        value = null;

        if (!url || !url.includes('/storage/portfolio/')) {
            return;
        }

        deletion.url = url;

        void deletion.delete(destroyMedia().url);
    }

    function fileName(url: string): string {
        try {
            const path = new URL(url, 'http://localhost').pathname;
            const name = path.split('/').pop();

            return name ? decodeURIComponent(name) : 'Document';
        } catch {
            return 'Document';
        }
    }
</script>

<div class="grid gap-3">
    <div class="flex items-center justify-between gap-2">
        <span class="text-sm font-medium leading-none">{label}</span>
        <div class="flex items-center gap-1.5">
            <Button
                type="button"
                variant="secondary"
                size="sm"
                onclick={chooseFile}
                disabled={upload.processing}
            >
                {upload.processing ? 'Uploading...' : 'Upload'}
            </Button>
            {#if value}
                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    onclick={removeFile}
                    disabled={upload.processing}
                >
                    Remove
                </Button>
            {/if}
        </div>
    </div>

    <input
        bind:this={fileInput}
        type="file"
        class="sr-only"
        {accept}
        onchange={handleFile}
        aria-label={label}
    />

    {#if value}
        {#if kind === 'image'}
            <div class="overflow-hidden rounded-xl border border-border/70">
                <img
                    src={value}
                    alt={label}
                    class="h-40 w-full object-cover"
                />
            </div>
        {:else}
            <div
                class="flex items-center gap-3 rounded-xl border border-border/70 bg-background/40 p-3"
            >
                <span
                    class="grid size-10 shrink-0 place-items-center rounded-lg bg-muted text-muted-foreground"
                >
                    <FileText class="size-5" aria-hidden="true" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium">
                        {fileName(value)}
                    </p>
                    <a
                        href={value}
                        target="_blank"
                        rel="noreferrer noopener"
                        class="text-xs text-[#0a66c2] hover:underline dark:text-sky-400"
                    >
                        Open file
                    </a>
                </div>
            </div>
        {/if}
    {:else}
        <div
            class="flex flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-border/80 bg-background/30 px-4 py-8 text-center"
        >
            <span
                class="grid size-10 place-items-center rounded-full bg-muted text-muted-foreground"
            >
                {#if kind === 'image'}
                    <ImageIcon class="size-5" aria-hidden="true" />
                {:else}
                    <FileText class="size-5" aria-hidden="true" />
                {/if}
            </span>
            <p class="text-sm text-muted-foreground">
                No {kind === 'image' ? 'image' : 'document'} selected yet.
            </p>
        </div>
    {/if}

    {#if upload.processing}
        <div class="h-1.5 overflow-hidden rounded-full bg-muted">
            <div
                class="h-full rounded-full bg-[#0a66c2] transition-all"
                style={`width: ${upload.progress?.percentage ?? 0}%`}
            ></div>
        </div>
    {/if}

    <Input
        bind:value
        placeholder={kind === 'image'
            ? 'https://... or upload an image'
            : 'https://... or upload a document'}
    />

    {#if hint}
        <p class="text-xs text-muted-foreground">{hint}</p>
    {/if}

    <InputError message={error ?? uploadError} />
</div>
