<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import Newspaper from '@lucide/svelte/icons/newspaper';
    import SquarePen from '@lucide/svelte/icons/square-pen';
    import type { Snippet } from 'svelte';
    import AppLogo from '@/components/AppLogo.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarHeader,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import { toUrl } from '@/lib/utils';
    import { edit as editPortfolio } from '@/routes/portfolio';
    import type { NavItem } from '@/types';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    const isAdmin = $derived(Boolean(page.props.auth?.user?.is_admin));
    const homeUrl = $derived(isAdmin ? toUrl(editPortfolio()) : '/');

    const mainNavItems = $derived<NavItem[]>(
        isAdmin
            ? [
                  {
                      title: 'Portfolio CMS',
                      href: editPortfolio(),
                      icon: SquarePen,
                  },
                  {
                      title: 'Blog',
                      href: '/settings/blog',
                      icon: Newspaper,
                  },
              ]
            : [],
    );
</script>

<Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton size="lg" asChild>
                    {#snippet children(props)}
                        <Link {...props} href={homeUrl} class={props.class}>
                            <AppLogo />
                        </Link>
                    {/snippet}
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
        <NavMain items={mainNavItems} />
    </SidebarContent>

    <SidebarFooter>
        <NavUser />
    </SidebarFooter>
</Sidebar>
{@render children?.()}
