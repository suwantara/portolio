<script lang="ts">
    import Moon from '@lucide/svelte/icons/moon';
    import Sun from '@lucide/svelte/icons/sun';
    import { onDestroy, onMount } from 'svelte';
    import { themeState } from '@/lib/theme.svelte';
    import { cn } from '@/lib/utils';

    let { class: className = '' }: { class?: string } = $props();

    const { appearance, updateAppearance } = themeState();

    let systemPrefersDark = $state(false);
    let mediaQuery: MediaQueryList | undefined;

    const isDark = $derived(
        appearance.value === 'dark' ||
            (appearance.value === 'system' && systemPrefersDark),
    );

    function toggleTheme(): void {
        updateAppearance(isDark ? 'light' : 'dark');
    }

    function handleSystemChange(event: MediaQueryListEvent): void {
        systemPrefersDark = event.matches;
    }

    onMount(() => {
        mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        systemPrefersDark = mediaQuery.matches;
        mediaQuery.addEventListener('change', handleSystemChange);
    });

    onDestroy(() => {
        mediaQuery?.removeEventListener('change', handleSystemChange);
    });
</script>

<button
    type="button"
    onclick={toggleTheme}
    aria-label="Toggle color theme"
    class={cn(
        'grid size-9 place-items-center rounded-full text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none',
        className,
    )}
>
    <Sun class="hidden size-4 dark:block" aria-hidden="true" />
    <Moon class="size-4 dark:hidden" aria-hidden="true" />
</button>
