<script lang="ts">
    import { onMount } from 'svelte';
    import { cn } from '@/lib/utils';

    const interactiveSelector =
        'a, button, [role="button"], input, textarea, select, [data-cursor="hover"]';

    let glow = $state<HTMLDivElement | null>(null);
    let ring = $state<HTMLDivElement | null>(null);
    let isActive = $state(false);
    let isHovering = $state(false);

    onMount(() => {
        if (!glow || !ring) {
            return;
        }

        const finePointer = window.matchMedia('(pointer: fine)');
        const reducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)',
        );

        let frame = 0;
        let listening = false;
        let hasPosition = false;
        let targetX = 0;
        let targetY = 0;
        let glowX = 0;
        let glowY = 0;
        let ringX = 0;
        let ringY = 0;

        const canAnimate = (): boolean =>
            finePointer.matches && !reducedMotion.matches;

        const applyPosition = (): void => {
            if (!glow || !ring) {
                return;
            }

            glow.style.transform = `translate3d(${glowX}px, ${glowY}px, 0) translate(-50%, -50%)`;
            ring.style.transform = `translate3d(${ringX}px, ${ringY}px, 0)`;
        };

        const render = (): void => {
            frame = 0;

            glowX += (targetX - glowX) * 0.09;
            glowY += (targetY - glowY) * 0.09;
            ringX += (targetX - ringX) * 0.32;
            ringY += (targetY - ringY) * 0.32;

            applyPosition();

            const settled =
                Math.abs(targetX - glowX) < 0.1 &&
                Math.abs(targetY - glowY) < 0.1 &&
                Math.abs(targetX - ringX) < 0.1 &&
                Math.abs(targetY - ringY) < 0.1;

            if (!settled) {
                frame = requestAnimationFrame(render);
            }
        };

        const handlePointerMove = (event: PointerEvent): void => {
            if (!canAnimate()) {
                return;
            }

            targetX = event.clientX;
            targetY = event.clientY;

            if (!hasPosition) {
                glowX = targetX;
                glowY = targetY;
                ringX = targetX;
                ringY = targetY;
                hasPosition = true;
                applyPosition();
            }

            isActive = true;

            if (frame === 0) {
                frame = requestAnimationFrame(render);
            }
        };

        const handlePointerOver = (event: PointerEvent): void => {
            const target = event.target;

            isHovering =
                target instanceof Element &&
                target.closest(interactiveSelector) !== null;
        };

        const hide = (): void => {
            isActive = false;
            isHovering = false;
            hasPosition = false;

            if (frame !== 0) {
                cancelAnimationFrame(frame);
                frame = 0;
            }
        };

        const handleWindowOut = (event: MouseEvent): void => {
            if (event.relatedTarget === null) {
                hide();
            }
        };

        const startListening = (): void => {
            if (listening) {
                return;
            }

            window.addEventListener('pointermove', handlePointerMove, {
                passive: true,
            });
            window.addEventListener('pointerover', handlePointerOver, {
                passive: true,
            });
            window.addEventListener('mouseout', handleWindowOut);
            window.addEventListener('blur', hide);
            listening = true;
        };

        const stopListening = (): void => {
            window.removeEventListener('pointermove', handlePointerMove);
            window.removeEventListener('pointerover', handlePointerOver);
            window.removeEventListener('mouseout', handleWindowOut);
            window.removeEventListener('blur', hide);
            listening = false;
        };

        const syncPreference = (): void => {
            if (canAnimate()) {
                startListening();
            } else {
                stopListening();
                hide();
            }
        };

        syncPreference();
        finePointer.addEventListener('change', syncPreference);
        reducedMotion.addEventListener('change', syncPreference);

        return () => {
            finePointer.removeEventListener('change', syncPreference);
            reducedMotion.removeEventListener('change', syncPreference);
            stopListening();
            hide();
        };
    });
</script>

<div
    class="pointer-events-none fixed inset-0 z-[60] overflow-hidden select-none"
    aria-hidden="true"
>
    <div
        bind:this={glow}
        data-cursor-glow
        class={cn(
            'absolute top-0 left-0 size-[16rem] rounded-full bg-[radial-gradient(circle,rgba(14,165,233,0.13)_0%,rgba(59,130,246,0.07)_38%,transparent_68%)] transition-opacity duration-500 will-change-transform dark:bg-[radial-gradient(circle,rgba(56,189,248,0.16)_0%,rgba(37,99,235,0.08)_40%,transparent_70%)]',
            isActive ? 'opacity-[0.45]' : 'opacity-0',
        )}
    ></div>

    <div
        bind:this={ring}
        data-cursor-ring
        class={cn(
            'absolute top-0 left-0 transition-opacity duration-300 will-change-transform',
            isActive ? 'opacity-100' : 'opacity-0',
        )}
    >
        <div
            class={cn(
                'glass-cursor-ring size-8 -translate-x-1/2 -translate-y-1/2 rounded-full transition-[width,height] duration-200',
                isHovering &&
                    'glass-cursor-ring-active size-11',
            )}
        ></div>
    </div>
</div>
