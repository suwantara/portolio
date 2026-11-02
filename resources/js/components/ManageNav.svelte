<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import SquarePen from '@lucide/svelte/icons/square-pen';
    import {
        SidebarGroup,
        SidebarGroupLabel,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import {
        isPortfolioSection,
        portfolioSectionGroups,
        type PortfolioSectionId,
    } from '@/lib/portfolio-sections';
    import { cn } from '@/lib/utils';

    const path = $derived.by(() => {
        try {
            return new URL(page.url, 'http://localhost').pathname;
        } catch {
            return page.url;
        }
    });

    const isPortfolio = $derived(path.startsWith('/settings/portfolio'));
    const isBlog = $derived(path.startsWith('/settings/portfolio/blog'));
    const activeSection = $derived.by<PortfolioSectionId | null>(() => {
        const match = path.match(/^\/settings\/portfolio\/([^/]+)$/);

        if (match && isPortfolioSection(match[1])) {
            return match[1];
        }

        return path === '/settings/portfolio' ? 'profile' : null;
    });
</script>

<SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Manage</SidebarGroupLabel>
    <SidebarMenu>
        <SidebarMenuItem>
            <SidebarMenuButton
                asChild
                isActive={isPortfolio}
                tooltip="Portfolio CMS"
            >
                {#snippet children(props)}
                    <Link {...props} href="/settings/portfolio" class={props.class}>
                        <SquarePen class="size-4 shrink-0" />
                        <span>Portfolio CMS</span>
                    </Link>
                {/snippet}
            </SidebarMenuButton>
        </SidebarMenuItem>

        <SidebarMenuItem>
            <div
                class="ml-4 border-l border-sidebar-border/70 pl-2 group-data-[collapsible=icon]:hidden"
            >
                {#each portfolioSectionGroups as group (group.title)}
                    <div class="py-1.5">
                        <p
                            class="px-2 py-1 font-mono text-[10px] tracking-[0.12em] text-muted-foreground uppercase"
                        >
                            {group.title}
                        </p>
                        <div class="space-y-0.5">
                            {#each group.sections as section (section.id)}
                                <Link
                                    href={`/settings/portfolio/${section.id}`}
                                    preserveState
                                    preserveScroll
                                    aria-current={activeSection === section.id
                                        ? 'page'
                                        : undefined}
                                    class={cn(
                                        'block rounded-md px-2 py-1.5 text-sm font-medium transition-colors',
                                        activeSection === section.id
                                            ? 'bg-sidebar-accent text-sidebar-accent-foreground'
                                            : 'text-muted-foreground hover:bg-sidebar-accent/60 hover:text-sidebar-accent-foreground',
                                    )}
                                >
                                    {section.label}
                                </Link>
                            {/each}
                            {#if group.title === 'Publishing'}
                                <Link
                                    href="/settings/portfolio/blog"
                                    aria-current={isBlog ? 'page' : undefined}
                                    class={cn(
                                        'block rounded-md px-2 py-1.5 text-sm font-medium transition-colors',
                                        isBlog
                                            ? 'bg-sidebar-accent text-sidebar-accent-foreground'
                                            : 'text-muted-foreground hover:bg-sidebar-accent/60 hover:text-sidebar-accent-foreground',
                                    )}
                                >
                                    Blog
                                </Link>
                            {/if}
                        </div>
                    </div>
                {/each}
            </div>
        </SidebarMenuItem>
    </SidebarMenu>
</SidebarGroup>
