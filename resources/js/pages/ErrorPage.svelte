<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ArrowLeft from '@lucide/svelte/icons/arrow-left';
    import Home from '@lucide/svelte/icons/house';
    import ThemeToggle from '@/components/ThemeToggle.svelte';

    let { status }: { status: number } = $props();

    const messages: Record<number, { title: string; description: string }> = {
        401: {
            title: 'You need to sign in.',
            description: 'This page is only available to the site owner.',
        },
        403: {
            title: 'This page is private.',
            description: 'Your account does not have permission to view this page.',
        },
        404: {
            title: 'This page wandered off.',
            description:
                'The link may be broken, or the page may have moved somewhere else.',
        },
        419: {
            title: 'Your session expired.',
            description:
                'Refresh the page and try again. Your changes may not have been saved.',
        },
        429: {
            title: 'A little too fast.',
            description:
                'You have made too many requests. Give it a moment and try again.',
        },
        500: {
            title: 'Something broke on our end.',
            description:
                'The server hit an unexpected error. Try again in a moment.',
        },
        503: {
            title: 'We are doing some maintenance.',
            description:
                'The site is temporarily unavailable. Please check back shortly.',
        },
    };

    const message = $derived(
        messages[status] ?? {
            title: 'Something went wrong.',
            description: 'The request could not be completed. Please try again.',
        },
    );
</script>

<svelte:head>
    <title>{status} - xuan.</title>
    <meta name="robots" content="noindex, nofollow" />
</svelte:head>

<div
    class="relative isolate flex min-h-screen flex-col overflow-hidden bg-[#f7f8fa] text-foreground dark:bg-[#07080c]"
>
    <div
        class="pointer-events-none absolute inset-0 -z-10 overflow-hidden"
        aria-hidden="true"
    >
        <div
            class="absolute inset-x-0 top-0 h-[760px] bg-[radial-gradient(80%_60%_at_50%_0%,rgba(147,197,253,0.38),transparent_72%)] dark:bg-[radial-gradient(80%_60%_at_50%_0%,rgba(37,99,235,0.2),transparent_72%)]"
        ></div>
        <div
            class="absolute inset-x-0 top-0 h-[760px] bg-[linear-gradient(to_right,rgba(15,23,42,0.045)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,0.045)_1px,transparent_1px)] [background-size:56px_56px] [mask-image:linear-gradient(to_bottom,black,transparent)] dark:bg-[linear-gradient(to_right,rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.05)_1px,transparent_1px)]"
        ></div>
    </div>

    <header class="px-4 pt-4">
        <nav
            aria-label="Error"
            class="liquid-glass mx-auto flex h-14 w-full max-w-4xl items-center justify-between gap-3 rounded-full pr-2 pl-3 sm:pl-4"
        >
            <Link
                href="/"
                class="flex items-center gap-2.5 rounded-full focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
            >
                <span
                    class="grid size-8 place-items-center rounded-full bg-foreground text-[13px] font-semibold text-background"
                >
                    X
                </span>
                <span class="text-[15px] font-semibold tracking-tight">
                    xuan<span class="text-[#0a66c2] dark:text-sky-400">.</span>
                </span>
            </Link>

            <ThemeToggle class="liquid-glass-control" />
        </nav>
    </header>

    <main
        class="relative flex flex-1 items-center justify-center px-5 py-20 text-center"
    >
        <div class="w-full max-w-2xl">
            <p
                class="font-mono text-[11px] tracking-[0.22em] text-muted-foreground uppercase"
            >
                Error {status}
            </p>
            <h1
                class="mt-4 text-4xl leading-[1.05] font-semibold tracking-[-0.045em] sm:text-6xl"
            >
                {message.title}
            </h1>
            <p
                class="mx-auto mt-5 max-w-md text-sm leading-relaxed text-muted-foreground sm:text-base"
            >
                {message.description}
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-2.5">
                <Link
                    href="/"
                    class="inline-flex h-10 items-center gap-2 rounded-full bg-[#0a66c2] px-5 text-sm font-semibold text-white transition-colors hover:bg-[#0958a8] focus-visible:ring-2 focus-visible:ring-[#0a66c2] focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                >
                    <Home class="size-4" aria-hidden="true" />
                    Back to portfolio
                </Link>
                <button
                    type="button"
                    onclick={() => window.history.back()}
                    class="inline-flex h-10 items-center gap-2 rounded-full border border-border bg-background/70 px-5 text-sm font-semibold text-foreground transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none"
                >
                    <ArrowLeft class="size-4" aria-hidden="true" />
                    Go back
                </button>
            </div>
        </div>
    </main>

    <footer class="px-5 pb-8 text-center">
        <p class="text-xs text-muted-foreground">
            © 2026 xuan. If this keeps happening, come back in a little while.
        </p>
    </footer>
</div>
