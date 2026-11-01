<script lang="ts">
    import type { Snippet } from 'svelte';
    import InputError from '@/components/InputError.svelte';

    let {
        label,
        error,
        hint,
        children,
    }: {
        label: string;
        error?: string;
        hint?: string;
        children?: Snippet;
    } = $props();
</script>

<label class="grid gap-2">
    <span class="text-sm font-medium leading-none">{label}</span>
    {@render children?.()}
    {#if hint}
        <span class="text-xs text-muted-foreground">{hint}</span>
    {/if}
    <InputError message={error} />
</label>
