<script lang="ts">
    import type { PortfolioSeo } from '@/types';

    let { seo }: { seo: PortfolioSeo } = $props();

</script>

<svelte:head>
    <title>{seo.title}</title>
    <meta name="description" content={seo.description} />
    {#if seo.author}
        <meta name="author" content={seo.author} />
    {/if}
    <link rel="canonical" href={seo.canonical} />

    <meta property="og:type" content={seo.type} />
    <meta property="og:title" content={seo.title} />
    <meta property="og:description" content={seo.description} />
    <meta property="og:url" content={seo.canonical} />
    <meta property="og:site_name" content={seo.site_name} />
    <meta property="og:image" content={seo.image} />
    <meta property="og:image:alt" content={seo.title} />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content={seo.title} />
    <meta name="twitter:description" content={seo.description} />
    <meta name="twitter:image" content={seo.image} />
    <meta name="twitter:image:alt" content={seo.title} />

    <meta
        name="theme-color"
        media="(prefers-color-scheme: light)"
        content="#f7f8fa"
    />
    <meta
        name="theme-color"
        media="(prefers-color-scheme: dark)"
        content="#07080c"
    />
</svelte:head>
