<script lang="ts">
    import { onDestroy, onMount } from 'svelte';

    let now = $state<Date | null>(null);
    let timer: ReturnType<typeof setInterval> | undefined;

    const timeFormatter = new Intl.DateTimeFormat('en-GB', {
        timeZone: 'Asia/Makassar',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
    });

    const dateFormatter = new Intl.DateTimeFormat('id-ID', {
        timeZone: 'Asia/Makassar',
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });

    const time = $derived(now ? timeFormatter.format(now) : '--:--:--');
    const date = $derived(now ? dateFormatter.format(now) : '—');

    onMount(() => {
        now = new Date();
        timer = setInterval(() => {
            now = new Date();
        }, 1000);
    });

    onDestroy(() => clearInterval(timer));
</script>

<div
    class="pointer-events-none fixed inset-x-0 top-4 z-30 hidden h-14 xl:block"
>
    <div class="absolute inset-y-0 left-4 flex flex-col justify-center">
        <time
            datetime={now?.toISOString()}
            class="block font-mono text-sm tracking-[0.02em] text-foreground/80 tabular-nums"
        >
            {time}
        </time>
        <p
            class="mt-1 font-mono text-[9px] tracking-[0.14em] text-muted-foreground"
        >
            WITA · GMT+8
        </p>
    </div>

    <div
        class="absolute inset-y-0 right-4 flex flex-col justify-center text-right"
    >
        <time
            datetime={now?.toISOString()}
            class="block font-mono text-[10px] tracking-[0.04em] text-foreground/80"
        >
            {date}
        </time>
    </div>
</div>
