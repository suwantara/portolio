<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ArrowLeft from '@lucide/svelte/icons/arrow-left';
    import type { Snippet } from 'svelte';
    import ThemeToggle from '@/components/ThemeToggle.svelte';
    import { home } from '@/routes';

    let {
        title = '',
        description = '',
        children,
    }: {
        title?: string;
        description?: string;
        children?: Snippet;
    } = $props();
</script>

<svelte:head>
    <meta name="robots" content="noindex, nofollow" />
</svelte:head>

<div
    class="relative isolate min-h-svh overflow-hidden bg-[#f7f8fa] text-foreground dark:bg-[#07080c]"
>
    <div
        class="pointer-events-none absolute inset-0 -z-10 overflow-hidden"
        aria-hidden="true"
    >
        <div
            class="absolute inset-x-0 top-0 h-[640px] bg-[radial-gradient(80%_60%_at_50%_0%,rgba(147,197,253,0.32),transparent_70%)] dark:bg-[radial-gradient(80%_60%_at_50%_0%,rgba(37,99,235,0.18),transparent_70%)]"
        ></div>
        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,rgba(15,23,42,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,0.04)_1px,transparent_1px)] [background-size:56px_56px] [mask-image:linear-gradient(to_bottom,black,transparent_75%)] dark:bg-[linear-gradient(to_right,rgba(255,255,255,0.045)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.045)_1px,transparent_1px)]"
        ></div>
    </div>

    <div class="grid min-h-svh lg:grid-cols-[1.05fr_1fr]">
        <aside
            class="relative hidden flex-col overflow-hidden bg-[linear-gradient(145deg,#071a33_0%,#0a2a52_45%,#0a66c2_100%)] p-10 text-white lg:flex"
        >
            <div
                class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.07)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.07)_1px,transparent_1px)] [background-size:52px_52px]"
                aria-hidden="true"
            ></div>
            <div
                class="absolute -top-24 -right-20 size-96 rounded-full bg-sky-300/20 blur-3xl"
                aria-hidden="true"
            ></div>
            <div
                class="absolute -bottom-32 -left-20 size-80 rounded-full bg-blue-500/30 blur-3xl"
                aria-hidden="true"
            ></div>
            <svg
                class="absolute -right-20 bottom-10 h-80 w-80 text-white/15"
                viewBox="0 0 200 200"
                fill="none"
                stroke="currentColor"
                aria-hidden="true"
            >
                <circle cx="100" cy="100" r="88" opacity="0.4" />
                <circle cx="100" cy="100" r="62" stroke-dasharray="3 7" />
                <circle cx="100" cy="100" r="38" />
                <circle
                    cx="176"
                    cy="100"
                    r="4"
                    fill="currentColor"
                    stroke="none"
                />
            </svg>

            <Link
                href={home()}
                class="relative z-10 flex w-fit items-center gap-2.5 rounded-full focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:outline-none"
            >
                <span
                    class="grid size-9 place-items-center rounded-xl bg-linear-to-br from-sky-400 to-blue-600 text-sm font-semibold text-white shadow-lg shadow-blue-950/30"
                >
                    X
                </span>
                <span class="text-base font-semibold tracking-tight">
                    xuan<span class="text-sky-300">.</span>
                </span>
            </Link>

            <div class="relative z-10 my-auto max-w-md">
                <p
                    class="font-mono text-[10px] tracking-[0.22em] text-sky-200/70 uppercase"
                >
                    Private workspace
                </p>
                <h2
                    class="mt-4 text-4xl leading-[1.08] font-semibold tracking-[-0.03em]"
                >
                    Your work,<br />behind one door.
                </h2>
                <p class="mt-5 max-w-sm text-sm leading-relaxed text-blue-100/75">
                    Sign in to update the story, gallery, documents, and
                    everything else visitors see.
                </p>

                <div class="mt-9 space-y-3 text-sm text-blue-50/80">
                    {#each ['Content, gallery & files in one CMS', 'Changes go live instantly', 'Private owner access'] as item (item)}
                        <div class="flex items-center gap-3">
                            <span
                                class="size-1.5 rounded-full bg-sky-300"
                                aria-hidden="true"
                            ></span>
                            {item}
                        </div>
                    {/each}
                </div>
            </div>

            <p
                class="relative z-10 font-mono text-[10px] tracking-[0.16em] text-blue-100/45 uppercase"
            >
                © 2026 xuan. — Private admin
            </p>
        </aside>

        <main
            class="relative flex min-h-svh flex-col px-4 py-6 sm:px-8 lg:px-12"
        >
            <header class="flex items-center justify-between gap-4">
                <Link
                    href={home()}
                    class="flex items-center gap-2.5 rounded-full focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none lg:hidden"
                >
                    <span
                        class="grid size-8 place-items-center rounded-xl bg-linear-to-br from-sky-400 to-blue-600 text-[13px] font-semibold text-white"
                    >
                        X
                    </span>
                    <span class="text-[15px] font-semibold tracking-tight">
                        xuan<span class="text-[#0a66c2] dark:text-sky-400"
                            >.</span
                        >
                    </span>
                </Link>

                <Link
                    href={home()}
                    class="hidden items-center gap-1.5 rounded-full px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground lg:inline-flex"
                >
                    <ArrowLeft class="size-4" aria-hidden="true" />
                    Back to portfolio
                </Link>

                <ThemeToggle />
            </header>

            <div class="flex flex-1 items-center justify-center py-10">
                <div class="w-full max-w-md">
                    <div
                        class="rounded-[1.75rem] border border-border/70 bg-card/85 p-6 shadow-[0_32px_90px_-60px_rgba(15,23,42,0.55)] backdrop-blur-sm sm:p-8"
                    >
                        <header class="mb-7">
                            <p
                                class="font-mono text-[10px] tracking-[0.2em] text-[#0a66c2] uppercase dark:text-sky-400"
                            >
                                Owner access
                            </p>
                            <h1
                                class="mt-2 text-2xl font-semibold tracking-[-0.02em] sm:text-3xl"
                            >
                                {title}
                            </h1>
                            {#if description}
                                <p
                                    class="mt-2 text-sm leading-relaxed text-muted-foreground"
                                >
                                    {description}
                                </p>
                            {/if}
                        </header>

                        {@render children?.()}
                    </div>

                    <div
                        class="mt-5 flex items-center justify-between gap-3 text-xs text-muted-foreground"
                    >
                        <Link
                            href={home()}
                            class="inline-flex items-center gap-1.5 font-medium transition-colors hover:text-foreground lg:hidden"
                        >
                            <ArrowLeft class="size-3.5" aria-hidden="true" />
                            Back to portfolio
                        </Link>
                        <span
                            class="ml-auto font-mono text-[10px] tracking-[0.14em] uppercase"
                        >
                            xuan. / admin
                        </span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
