<script lang="ts">
    let {
        class: className = '',
        ...rest
    }: {
        class?: string;
        [key: string]: unknown;
    } = $props();
</script>

<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 40 40"
    fill="none"
    class={className}
    {...rest}
>
    <path
        d="M12 12 28 28M28 12 12 28"
        stroke="currentColor"
        stroke-width="4"
        stroke-linecap="round"
    />
</svg>
