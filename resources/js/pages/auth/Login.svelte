<script module lang="ts">
    export const layout = {
        title: 'Welcome back',
        description: 'Sign in to manage your portfolio.',
    };
</script>

<script lang="ts">
    import { Form } from '@inertiajs/svelte';
    import LockKeyhole from '@lucide/svelte/icons/lock-keyhole';
    import AppHead from '@/components/AppHead.svelte';
    import InputError from '@/components/InputError.svelte';
    import PasswordInput from '@/components/PasswordInput.svelte';
    import TextLink from '@/components/TextLink.svelte';
    import { Button } from '@/components/ui/button';
    import { Checkbox } from '@/components/ui/checkbox';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { store } from '@/routes/login';
    import { request } from '@/routes/password';

    let {
        status = '',
        canResetPassword,
    }: {
        status?: string;
        canResetPassword: boolean;
    } = $props();
</script>

<AppHead title="Log in" />

{#if status}
    <div
        class="mb-5 rounded-xl border border-emerald-500/25 bg-emerald-500/10 px-3 py-2 text-sm font-medium text-emerald-700 dark:text-emerald-400"
    >
        {status}
    </div>
{/if}

<Form
    {...store.form()}
    resetOnSuccess={['password']}
    class="flex flex-col gap-5"
>
    {#snippet children({ errors, processing })}
        <div class="grid gap-5">
            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    class="h-10 bg-background/70"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="you@example.com"
                />
                <InputError message={errors.email} />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between gap-3">
                    <Label for="password">Password</Label>
                    {#if canResetPassword}
                        <TextLink
                            href={request()}
                            class="text-xs font-medium text-[#0a66c2] no-underline hover:underline dark:text-sky-400"
                        >
                            Forgot password?
                        </TextLink>
                    {/if}
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    class="h-10 bg-background/70"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                />
                <InputError message={errors.password} />
            </div>

            <div class="flex items-center justify-between">
                <Label
                    for="remember"
                    class="flex items-center gap-2.5 text-sm text-muted-foreground"
                >
                    <Checkbox id="remember" name="remember" />
                    <span>Remember me on this device</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-1 h-10 w-full rounded-full bg-[#0a66c2] text-white shadow-sm hover:bg-[#0958a8]"
                disabled={processing}
                data-test="login-button"
            >
                {#if processing}<Spinner />{/if}
                Sign in
            </Button>

            <p
                class="flex items-center justify-center gap-1.5 font-mono text-[10px] tracking-[0.14em] text-muted-foreground uppercase"
            >
                <LockKeyhole class="size-3" aria-hidden="true" />
                Private owner access
            </p>
        </div>
    {/snippet}
</Form>
