<script module lang="ts">
    import { edit as editPortfolio } from '@/routes/portfolio';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Portfolio CMS',
                href: editPortfolio(),
            },
        ],
    };
</script>

<script lang="ts">
    import { page, useForm } from '@inertiajs/svelte';
    import ExternalLink from '@lucide/svelte/icons/external-link';
    import Plus from '@lucide/svelte/icons/plus';
    import AppHead from '@/components/AppHead.svelte';
    import Field from '@/components/cms/Field.svelte';
    import Panel from '@/components/cms/Panel.svelte';
    import RepeaterHeader from '@/components/cms/RepeaterHeader.svelte';
    import Select from '@/components/cms/Select.svelte';
    import StringListEditor from '@/components/cms/StringListEditor.svelte';
    import Textarea from '@/components/cms/Textarea.svelte';
    import Heading from '@/components/Heading.svelte';
    import InputError from '@/components/InputError.svelte';
    import MediaPicker from '@/components/cms/MediaPicker.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { getError } from '@/lib/errors';
    import {
        isPortfolioSection,
        portfolioSections as tabs,
        type PortfolioSectionId,
    } from '@/lib/portfolio-sections';
    import {
        experienceAccents,
        projectCovers,
        projectNumber,
    } from '@/lib/portfolio';
    import { socialLabels } from '@/lib/socials';
    import { cn } from '@/lib/utils';
    import { update } from '@/routes/portfolio';
    import type { PortfolioContent, PortfolioOptions } from '@/types';

    let {
        portfolio,
        options,
    }: {
        portfolio: PortfolioContent;
        options: PortfolioOptions;
    } = $props();

    type Tab = PortfolioSectionId;

    const tabPrefixes: Record<Tab, string[]> = {
        profile: ['profile.', 'socials.'],
        about: ['about', 'stats.'],
        experience: ['experience.'],
        work: ['projects.'],
        skills: ['skill_groups.'],
        education: ['education.', 'languages.', 'currently_exploring.'],
        gallery: ['gallery.'],
        pages: ['gallery_header.', 'blog_header.'],
        documents: ['documents.'],
        cta: ['cta.', 'footer_note'],
    };

    const form = useForm(() => $state.snapshot(portfolio));
    const errors = $derived(
        form.errors as unknown as Record<string, unknown>,
    );

    const routeSection = $derived.by<Tab>(() => {
        const path = new URL(page.url, 'http://localhost').pathname;
        const match = path.match(/^\/settings\/portfolio\/([^/]+)$/);

        if (match && isPortfolioSection(match[1])) {
            return match[1];
        }

        return 'profile';
    });

    let activeTab = $state<Tab>('profile');

    $effect(() => {
        activeTab = routeSection;
    });

    const availabilityPresets = [
        'Open to new opportunities',
        'Open to freelance work',
        'Open to full-time roles',
        'Not currently available',
    ];

    const addButton =
        'inline-flex h-9 w-fit items-center gap-1.5 rounded-md border border-dashed border-border bg-background/50 px-3 text-sm font-medium text-muted-foreground transition-colors hover:border-foreground/30 hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none';

    function save(): void {
        const url = new URL(update().url, window.location.origin);
        url.searchParams.set('section', routeSection);

        form.put(url.toString(), {
            preserveScroll: true,
            onError: () => {
                const tab = tabs
                    .map((item) => item.id)
                    .find((id) => tabHasErrors(id));

                if (tab) {
                    activeTab = tab;

                    if (typeof window !== 'undefined') {
                        window.history.replaceState(
                            null,
                            '',
                            `/settings/portfolio/${tab}`,
                        );
                    }
                }
            },
        });
    }

    function discard(): void {
        form.reset();
        form.clearErrors();
    }

    function tabHasErrors(tab: Tab): boolean {
        return Object.keys(form.errors).some((key) =>
            tabPrefixes[tab].some((prefix) => key.startsWith(prefix)),
        );
    }

    function moveItem<T>(items: T[], index: number, offset: number): void {
        const target = index + offset;

        if (target < 0 || target >= items.length) {
            return;
        }

        const [item] = items.splice(index, 1);
        items.splice(target, 0, item);
    }

    function removeItem<T>(items: T[], index: number): void {
        items.splice(index, 1);
    }

    function addStat(): void {
        if (form.stats.length >= 6) {
            return;
        }

        form.stats.push({ value: '', label: '' });
    }

    function addSocial(): void {
        if (form.socials.length >= 8) {
            return;
        }

        form.socials.push({
            name: options.socials[0] ?? 'website',
            label: '',
            href: '',
        });
    }

    function fillSocialLabel(index: number): void {
        const social = form.socials[index];

        if (!social.label) {
            social.label = socialLabels[social.name];
        }
    }

    function addExperience(): void {
        if (form.experience.length >= 30) {
            return;
        }

        form.experience.push({
            role: '',
            company: '',
            employment: 'Full-time',
            period: '',
            location: '',
            description: '',
            highlights: [],
            skills: [],
            monogram: '',
            accent: options.experience_accents[0] ?? 'blue',
        });
    }

    function addProject(): void {
        if (form.projects.length >= 20) {
            return;
        }

        form.projects.push({
            name: '',
            year: new Date().getFullYear().toString(),
            description: '',
            tags: [],
            cover: options.project_covers[0] ?? 'blue',
            image: null,
            link: null,
            featured: false,
        });
    }

    function addSkillGroup(): void {
        if (form.skill_groups.length >= 12) {
            return;
        }

        form.skill_groups.push({ title: '', skills: [] });
    }

    function addGalleryItem(): void {
        if (form.gallery.length >= 100) {
            return;
        }

        form.gallery.push({
            image: null,
            title: '',
            caption: '',
            link: null,
            featured: false,
        });
    }

    function addDocument(): void {
        if (form.documents.length >= 12) {
            return;
        }

        form.documents.push({
            title: '',
            description: '',
            file: null,
        });
    }

    function addEducation(): void {
        if (form.education.length >= 12) {
            return;
        }

        form.education.push({
            degree: '',
            school: '',
            period: '',
            description: '',
        });
    }

    function addLanguage(): void {
        if (form.languages.length >= 20) {
            return;
        }

        form.languages.push({ name: '', level: '' });
    }
</script>

<AppHead title="Portfolio CMS" />

<h1 class="sr-only">Portfolio CMS</h1>

<div class="mx-auto flex w-full max-w-5xl flex-col gap-6 p-4 sm:p-6">
    <div
        class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
    >
        <Heading
            title="Portfolio CMS"
            description="Edit every section of the public landing page. Changes go live as soon as you save."
        />

        <div class="flex shrink-0 flex-wrap items-center gap-2">
            <a
                href="/"
                target="_blank"
                rel="noreferrer noopener"
                class="inline-flex h-9 items-center gap-1.5 rounded-md border border-border bg-background px-3 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
            >
                Preview
                <ExternalLink class="size-3.5" aria-hidden="true" />
            </a>
            <Button
                type="button"
                onclick={save}
                disabled={form.processing || !form.isDirty}
            >
                {form.processing ? 'Saving...' : 'Save changes'}
            </Button>
        </div>
    </div>

    {#if form.recentlySuccessful}
        <p
            class="rounded-lg border border-emerald-500/25 bg-emerald-500/10 px-3 py-2 text-sm font-medium text-emerald-700 dark:text-emerald-400"
            role="status"
        >
            All changes saved.
        </p>
    {/if}

    {#if form.hasErrors}
        <p
            class="rounded-lg border border-destructive/30 bg-destructive/5 px-3 py-2 text-sm font-medium text-destructive"
            role="alert"
        >
            Some fields need attention. Open the section with a red dot.
        </p>
    {/if}

    <form
        onsubmit={(event) => {
            event.preventDefault();
            save();
        }}
        class="space-y-5"
    >
        {#if activeTab === 'profile'}
            <Panel
                title="Identity"
                description="The hero section of the landing page."
            >
                <div class="grid gap-4 sm:grid-cols-2">
                    <Field
                        label="Full name"
                        error={getError(errors, 'profile.name')}
                    >
                        <Input bind:value={form.profile.name} />
                    </Field>
                    <Field
                        label="Monogram"
                        error={getError(errors, 'profile.monogram')}
                        hint="Used when no avatar URL is set. Up to 4 characters."
                    >
                        <Input
                            bind:value={form.profile.monogram}
                            maxlength={4}
                        />
                    </Field>
                    <Field
                        label="Role"
                        error={getError(errors, 'profile.role')}
                    >
                        <Input bind:value={form.profile.role} />
                    </Field>
                    <Field
                        label="Location"
                        error={getError(errors, 'profile.location')}
                    >
                        <Input bind:value={form.profile.location} />
                    </Field>
                    <Field
                        label="Timezone"
                        error={getError(errors, 'profile.timezone')}
                    >
                        <Input bind:value={form.profile.timezone} />
                    </Field>
                    <Field
                        label="Contact email"
                        error={getError(errors, 'profile.email')}
                    >
                        <Input
                            type="email"
                            bind:value={form.profile.email}
                        />
                    </Field>
                    <div class="sm:col-span-2">
                        <MediaPicker
                            bind:value={form.profile.avatar}
                            kind="image"
                            label="Profile photo"
                            error={getError(errors, 'profile.avatar')}
                            hint="Optional. Use a square image for best results."
                        />
                    </div>
                    <div class="sm:col-span-2">
                        <Field
                            label="Tagline"
                            error={getError(errors, 'profile.tagline')}
                        >
                            <Textarea
                                bind:value={form.profile.tagline}
                                rows={3}
                            />
                        </Field>
                    </div>
                </div>
            </Panel>

            <Panel
                title="Availability"
                description="Control the status badge shown in the hero and the availability card."
            >
                <div class="grid gap-5">
                    <Field
                        label="Status text"
                        error={getError(errors, 'profile.availability')}
                        hint="Shown in the badge and as the availability card heading."
                    >
                        <Input bind:value={form.profile.availability} />
                    </Field>

                    <div class="flex flex-wrap gap-2">
                        {#each availabilityPresets as preset (preset)}
                            <button
                                type="button"
                                onclick={() =>
                                    (form.profile.availability = preset)}
                                class={cn(
                                    'rounded-full border px-3 py-1.5 text-xs font-medium transition-colors',
                                    form.profile.availability === preset
                                        ? 'border-[#0a66c2] bg-[#0a66c2]/10 text-[#0a66c2] dark:text-sky-300'
                                        : 'border-border text-muted-foreground hover:bg-muted hover:text-foreground',
                                )}
                            >
                                {preset}
                            </button>
                        {/each}
                    </div>

                    <Field
                        label="Availability details"
                        error={getError(errors, 'profile.availability_note')}
                        hint="Shown in the availability card under the status."
                    >
                        <Textarea
                            bind:value={form.profile.availability_note}
                            rows={3}
                        />
                    </Field>

                    <div>
                        <label class="flex items-center gap-2 text-sm font-medium">
                            <input
                                type="checkbox"
                                bind:checked={form.profile.availability_open}
                                class="size-4 rounded border-input accent-foreground"
                            />
                            Currently available (green badge)
                        </label>
                        <InputError
                            message={getError(
                                errors,
                                'profile.availability_open',
                            )}
                        />
                    </div>

                    <div
                        class="rounded-xl border border-border/70 bg-background/40 p-4"
                    >
                        <p
                            class="font-mono text-[10px] tracking-[0.16em] text-muted-foreground uppercase"
                        >
                            Preview
                        </p>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span
                                class={cn(
                                    'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 font-mono text-[10px] font-medium tracking-[0.14em] uppercase',
                                    form.profile.availability_open
                                        ? 'border-emerald-500/25 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                        : 'border-amber-500/25 bg-amber-500/10 text-amber-700 dark:text-amber-400',
                                )}
                            >
                                <span class="relative flex size-1.5">
                                    {#if form.profile.availability_open}
                                        <span
                                            class="absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-60 motion-safe:animate-ping"
                                        ></span>
                                    {/if}
                                    <span
                                        class={cn(
                                            'relative inline-flex size-1.5 rounded-full',
                                            form.profile.availability_open
                                                ? 'bg-emerald-500'
                                                : 'bg-amber-500',
                                        )}
                                    ></span>
                                </span>
                                {form.profile.availability || 'Your status'}
                            </span>
                        </div>

                        <p class="mt-3 text-sm font-semibold">
                            {form.profile.availability_note ||
                                'Your availability details'}
                        </p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            Based in {form.profile.location || 'your location'},
                            working remotely with teams across time zones.
                        </p>
                    </div>
                </div>
            </Panel>

            <Panel
                title="Social links"
                description="Shown in the hero, contact card, call to action, and footer."
            >
                <div class="space-y-4">
                    {#each form.socials as social, index (index)}
                        <div
                            class="rounded-xl border border-border/60 bg-background/40 p-4"
                        >
                            <RepeaterHeader
                                title={social.label ||
                                    socialLabels[social.name] ||
                                    `Social ${index + 1}`}
                                subtitle={social.href}
                                canMoveUp={index > 0}
                                canMoveDown={index <
                                    form.socials.length - 1}
                                onMoveUp={() =>
                                    moveItem(form.socials, index, -1)}
                                onMoveDown={() =>
                                    moveItem(form.socials, index, 1)}
                                onRemove={() =>
                                    removeItem(form.socials, index)}
                            />

                            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                                <Field
                                    label="Platform"
                                    error={getError(
                                        errors,
                                        `socials.${index}.name`,
                                    )}
                                >
                                    <Select
                                        bind:value={social.name}
                                        onchange={() =>
                                            fillSocialLabel(index)}
                                    >
                                        {#each options.socials as name (name)}
                                            <option value={name}>
                                                {socialLabels[name]}
                                            </option>
                                        {/each}
                                    </Select>
                                </Field>
                                <Field
                                    label="Label"
                                    error={getError(
                                        errors,
                                        `socials.${index}.label`,
                                    )}
                                >
                                    <Input bind:value={social.label} />
                                </Field>
                                <Field
                                    label="URL"
                                    error={getError(
                                        errors,
                                        `socials.${index}.href`,
                                    )}
                                >
                                    <Input
                                        bind:value={social.href}
                                        placeholder="https://..."
                                    />
                                </Field>
                            </div>
                        </div>
                    {/each}

                    {#if form.socials.length < 8}
                        <button
                            type="button"
                            onclick={addSocial}
                            class={addButton}
                        >
                            <Plus class="size-4" aria-hidden="true" />
                            Add social link
                        </button>
                    {/if}
                </div>
            </Panel>
        {:else if activeTab === 'about'}
            <Panel
                title="About"
                description="Write the story shown in the About section."
            >
                <StringListEditor
                    bind:items={form.about}
                    label="Paragraphs"
                    path="about"
                    placeholder="Write a paragraph..."
                    addLabel="Add paragraph"
                    multiline
                    {errors}
                />
            </Panel>

            <Panel
                title="Focus tags"
                description="Small chips displayed under the About copy."
            >
                <StringListEditor
                    bind:items={form.about_tags}
                    label="Tags"
                    path="about_tags"
                    placeholder="Laravel"
                    addLabel="Add tag"
                    {errors}
                />
            </Panel>

            <Panel
                title="Stats"
                description="Up to six quick highlights in the profile header."
            >
                <div class="space-y-4">
                    {#each form.stats as stat, index (index)}
                        <div
                            class="rounded-xl border border-border/60 bg-background/40 p-4"
                        >
                            <RepeaterHeader
                                title={stat.value ||
                                    stat.label ||
                                    `Stat ${index + 1}`}
                                canMoveUp={index > 0}
                                canMoveDown={index < form.stats.length - 1}
                                onMoveUp={() =>
                                    moveItem(form.stats, index, -1)}
                                onMoveDown={() =>
                                    moveItem(form.stats, index, 1)}
                                onRemove={() =>
                                    removeItem(form.stats, index)}
                            />

                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <Field
                                    label="Value"
                                    error={getError(
                                        errors,
                                        `stats.${index}.value`,
                                    )}
                                >
                                    <Input
                                        bind:value={stat.value}
                                        placeholder="05+"
                                    />
                                </Field>
                                <Field
                                    label="Label"
                                    error={getError(
                                        errors,
                                        `stats.${index}.label`,
                                    )}
                                >
                                    <Input
                                        bind:value={stat.label}
                                        placeholder="Years building"
                                    />
                                </Field>
                            </div>
                        </div>
                    {/each}

                    {#if form.stats.length < 6}
                        <button
                            type="button"
                            onclick={addStat}
                            class={addButton}
                        >
                            <Plus class="size-4" aria-hidden="true" />
                            Add stat
                        </button>
                    {/if}
                </div>
            </Panel>
        {:else if activeTab === 'experience'}
            <div class="space-y-5">
                {#each form.experience as job, index (index)}
                    <Panel
                        title={`Role ${index + 1}`}
                        description="One entry in the Experience timeline."
                    >
                        <RepeaterHeader
                            title={job.role || 'Untitled role'}
                            subtitle={job.company}
                            canMoveUp={index > 0}
                            canMoveDown={index < form.experience.length - 1}
                            onMoveUp={() =>
                                moveItem(form.experience, index, -1)}
                            onMoveDown={() =>
                                moveItem(form.experience, index, 1)}
                            onRemove={() =>
                                removeItem(form.experience, index)}
                        />

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <Field
                                label="Role"
                                error={getError(
                                    errors,
                                    `experience.${index}.role`,
                                )}
                            >
                                <Input bind:value={job.role} />
                            </Field>
                            <Field
                                label="Company"
                                error={getError(
                                    errors,
                                    `experience.${index}.company`,
                                )}
                            >
                                <Input bind:value={job.company} />
                            </Field>
                            <Field
                                label="Employment type"
                                error={getError(
                                    errors,
                                    `experience.${index}.employment`,
                                )}
                            >
                                <Input
                                    bind:value={job.employment}
                                    placeholder="Full-time"
                                />
                            </Field>
                            <Field
                                label="Period"
                                error={getError(
                                    errors,
                                    `experience.${index}.period`,
                                )}
                            >
                                <Input
                                    bind:value={job.period}
                                    placeholder="2023 — Present"
                                />
                            </Field>
                            <Field
                                label="Location"
                                error={getError(
                                    errors,
                                    `experience.${index}.location`,
                                )}
                            >
                                <Input bind:value={job.location} />
                            </Field>
                            <Field
                                label="Monogram"
                                error={getError(
                                    errors,
                                    `experience.${index}.monogram`,
                                )}
                                hint="Up to 4 characters."
                            >
                                <Input
                                    bind:value={job.monogram}
                                    maxlength={4}
                                />
                            </Field>
                            <div class="sm:col-span-2">
                                <Field
                                    label="Accent"
                                    error={getError(
                                        errors,
                                        `experience.${index}.accent`,
                                    )}
                                >
                                    <div class="flex items-center gap-3">
                                        <Select
                                            bind:value={job.accent}
                                            class="flex-1"
                                        >
                                            {#each options.experience_accents as accent (accent)}
                                                <option value={accent}>
                                                    {accent}
                                                </option>
                                            {/each}
                                        </Select>
                                        <span
                                            class={cn(
                                                'size-9 shrink-0 rounded-md bg-linear-to-br',
                                                experienceAccents[job.accent] ??
                                                    experienceAccents.blue,
                                            )}
                                            aria-hidden="true"
                                        ></span>
                                    </div>
                                </Field>
                            </div>
                            <div class="sm:col-span-2">
                                <Field
                                    label="Description"
                                    error={getError(
                                        errors,
                                        `experience.${index}.description`,
                                    )}
                                >
                                    <Textarea
                                        bind:value={job.description}
                                        rows={3}
                                    />
                                </Field>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-5 lg:grid-cols-2">
                            <StringListEditor
                                bind:items={job.highlights}
                                label="Highlights"
                                path={`experience.${index}.highlights`}
                                placeholder="What did you ship?"
                                addLabel="Add highlight"
                                multiline
                                {errors}
                            />
                            <StringListEditor
                                bind:items={job.skills}
                                label="Skills"
                                path={`experience.${index}.skills`}
                                placeholder="Laravel"
                                addLabel="Add skill"
                                {errors}
                            />
                        </div>
                    </Panel>
                {/each}

                {#if form.experience.length < 30}
                    <button
                        type="button"
                        onclick={addExperience}
                        class={addButton}
                    >
                        <Plus class="size-4" aria-hidden="true" />
                        Add experience
                    </button>
                {/if}
            </div>
        {:else if activeTab === 'work'}
            <div class="space-y-5">
                {#each form.projects as project, index (index)}
                    <Panel
                        title={`Project ${index + 1}`}
                        description="A card in the Selected work grid."
                    >
                        <RepeaterHeader
                            title={project.name || 'Untitled project'}
                            subtitle={project.year}
                            canMoveUp={index > 0}
                            canMoveDown={index < form.projects.length - 1}
                            onMoveUp={() =>
                                moveItem(form.projects, index, -1)}
                            onMoveDown={() =>
                                moveItem(form.projects, index, 1)}
                            onRemove={() =>
                                removeItem(form.projects, index)}
                        />

                        <div
                            class={cn(
                                'relative mt-4 h-24 overflow-hidden rounded-lg',
                                projectCovers[project.cover] ??
                                    projectCovers.blue,
                            )}
                            aria-hidden="true"
                        >
                            {#if project.image}
                                <img
                                    src={project.image}
                                    alt=""
                                    class="absolute inset-0 size-full object-cover"
                                />
                            {/if}
                            <span
                                class="absolute top-3 left-3 font-mono text-[10px] tracking-[0.18em] text-white/80 uppercase"
                            >
                                {projectNumber(index)} — {project.year ||
                                    'Year'}
                            </span>
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <Field
                                label="Name"
                                error={getError(
                                    errors,
                                    `projects.${index}.name`,
                                )}
                            >
                                <Input bind:value={project.name} />
                            </Field>
                            <Field
                                label="Year"
                                error={getError(
                                    errors,
                                    `projects.${index}.year`,
                                )}
                            >
                                <Input
                                    bind:value={project.year}
                                    placeholder="2025"
                                />
                            </Field>
                            <Field
                                label="Cover"
                                error={getError(
                                    errors,
                                    `projects.${index}.cover`,
                                )}
                            >
                                <Select bind:value={project.cover}>
                                    {#each options.project_covers as cover (cover)}
                                        <option value={cover}>{cover}</option>
                                    {/each}
                                </Select>
                            </Field>
                            <div class="flex items-end pb-2">
                                <label
                                    class="flex items-center gap-2 text-sm font-medium"
                                >
                                    <input
                                        type="checkbox"
                                        bind:checked={project.featured}
                                        class="size-4 rounded border-input accent-foreground"
                                    />
                                    Feature this project
                                </label>
                            </div>
                            <div class="sm:col-span-2">
                                <MediaPicker
                                    bind:value={project.image}
                                    kind="image"
                                    label="Project thumbnail"
                                    error={getError(
                                        errors,
                                        `projects.${index}.image`,
                                    )}
                                    hint="Optional. Overrides the gradient cover."
                                />
                            </div>
                            <Field
                                label="Project URL"
                                error={getError(
                                    errors,
                                    `projects.${index}.link`,
                                )}
                                hint="Optional. Adds a “View project” link."
                            >
                                <Input
                                    bind:value={project.link}
                                    placeholder="https://..."
                                />
                            </Field>
                            <div class="sm:col-span-2">
                                <Field
                                    label="Description"
                                    error={getError(
                                        errors,
                                        `projects.${index}.description`,
                                    )}
                                >
                                    <Textarea
                                        bind:value={project.description}
                                        rows={3}
                                    />
                                </Field>
                            </div>
                        </div>

                        <div class="mt-5">
                            <StringListEditor
                                bind:items={project.tags}
                                label="Tags"
                                path={`projects.${index}.tags`}
                                placeholder="Laravel"
                                addLabel="Add tag"
                                {errors}
                            />
                        </div>
                    </Panel>
                {/each}

                {#if form.projects.length < 20}
                    <button
                        type="button"
                        onclick={addProject}
                        class={addButton}
                    >
                        <Plus class="size-4" aria-hidden="true" />
                        Add project
                    </button>
                {/if}
            </div>
        {:else if activeTab === 'skills'}
            <div class="space-y-5">
                {#each form.skill_groups as group, index (index)}
                    <Panel title={`Skill group ${index + 1}`}>
                        <RepeaterHeader
                            title={group.title || 'Untitled group'}
                            subtitle={`${group.skills.length} skills`}
                            canMoveUp={index > 0}
                            canMoveDown={index < form.skill_groups.length - 1}
                            onMoveUp={() =>
                                moveItem(form.skill_groups, index, -1)}
                            onMoveDown={() =>
                                moveItem(form.skill_groups, index, 1)}
                            onRemove={() =>
                                removeItem(form.skill_groups, index)}
                        />

                        <div class="mt-4 grid gap-5">
                            <Field
                                label="Group title"
                                error={getError(
                                    errors,
                                    `skill_groups.${index}.title`,
                                )}
                            >
                                <Input bind:value={group.title} />
                            </Field>
                            <StringListEditor
                                bind:items={group.skills}
                                label="Skills"
                                path={`skill_groups.${index}.skills`}
                                placeholder="Laravel"
                                addLabel="Add skill"
                                {errors}
                            />
                        </div>
                    </Panel>
                {/each}

                {#if form.skill_groups.length < 12}
                    <button
                        type="button"
                        onclick={addSkillGroup}
                        class={addButton}
                    >
                        <Plus class="size-4" aria-hidden="true" />
                        Add skill group
                    </button>
                {/if}
            </div>
        {:else if activeTab === 'education'}
            <Panel title="Education">
                <div class="space-y-4">
                    {#each form.education as item, index (index)}
                        <div
                            class="rounded-xl border border-border/60 bg-background/40 p-4"
                        >
                            <RepeaterHeader
                                title={item.degree || 'Untitled education'}
                                subtitle={item.school}
                                canMoveUp={index > 0}
                                canMoveDown={index <
                                    form.education.length - 1}
                                onMoveUp={() =>
                                    moveItem(form.education, index, -1)}
                                onMoveDown={() =>
                                    moveItem(form.education, index, 1)}
                                onRemove={() =>
                                    removeItem(form.education, index)}
                            />

                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <Field
                                    label="Degree"
                                    error={getError(
                                        errors,
                                        `education.${index}.degree`,
                                    )}
                                >
                                    <Input bind:value={item.degree} />
                                </Field>
                                <Field
                                    label="School"
                                    error={getError(
                                        errors,
                                        `education.${index}.school`,
                                    )}
                                >
                                    <Input bind:value={item.school} />
                                </Field>
                                <Field
                                    label="Period"
                                    error={getError(
                                        errors,
                                        `education.${index}.period`,
                                    )}
                                >
                                    <Input
                                        bind:value={item.period}
                                        placeholder="2015 — 2019"
                                    />
                                </Field>
                                <div class="sm:col-span-2">
                                    <Field
                                        label="Description"
                                        error={getError(
                                            errors,
                                            `education.${index}.description`,
                                        )}
                                    >
                                        <Textarea
                                            bind:value={item.description}
                                            rows={2}
                                        />
                                    </Field>
                                </div>
                            </div>
                        </div>
                    {/each}

                    {#if form.education.length < 12}
                        <button
                            type="button"
                            onclick={addEducation}
                            class={addButton}
                        >
                            <Plus class="size-4" aria-hidden="true" />
                            Add education
                        </button>
                    {/if}
                </div>
            </Panel>

            <Panel title="Languages">
                <div class="space-y-4">
                    {#each form.languages as language, index (index)}
                        <div
                            class="rounded-xl border border-border/60 bg-background/40 p-4"
                        >
                            <RepeaterHeader
                                title={language.name || 'Untitled language'}
                                subtitle={language.level}
                                canMoveUp={index > 0}
                                canMoveDown={index <
                                    form.languages.length - 1}
                                onMoveUp={() =>
                                    moveItem(form.languages, index, -1)}
                                onMoveDown={() =>
                                    moveItem(form.languages, index, 1)}
                                onRemove={() =>
                                    removeItem(form.languages, index)}
                            />

                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <Field
                                    label="Language"
                                    error={getError(
                                        errors,
                                        `languages.${index}.name`,
                                    )}
                                >
                                    <Input bind:value={language.name} />
                                </Field>
                                <Field
                                    label="Level"
                                    error={getError(
                                        errors,
                                        `languages.${index}.level`,
                                    )}
                                >
                                    <Input
                                        bind:value={language.level}
                                        placeholder="Native"
                                    />
                                </Field>
                            </div>
                        </div>
                    {/each}

                    {#if form.languages.length < 20}
                        <button
                            type="button"
                            onclick={addLanguage}
                            class={addButton}
                        >
                            <Plus class="size-4" aria-hidden="true" />
                            Add language
                        </button>
                    {/if}
                </div>
            </Panel>

            <Panel title="Currently exploring">
                <StringListEditor
                    bind:items={form.currently_exploring}
                    label="Topics"
                    path="currently_exploring"
                    placeholder="Design tokens"
                    addLabel="Add topic"
                    {errors}
                />
            </Panel>
        {:else if activeTab === 'gallery'}
            <div class="space-y-5">
                {#each form.gallery as item, index (index)}
                    <Panel
                        title={`Gallery item ${index + 1}`}
                        description="Shown in the gallery grid and opened in a lightbox."
                    >
                        <RepeaterHeader
                            title={item.title || 'Untitled image'}
                            subtitle={item.caption}
                            canMoveUp={index > 0}
                            canMoveDown={index < form.gallery.length - 1}
                            onMoveUp={() =>
                                moveItem(form.gallery, index, -1)}
                            onMoveDown={() =>
                                moveItem(form.gallery, index, 1)}
                            onRemove={() => removeItem(form.gallery, index)}
                        />

                        <div class="mt-4">
                            <MediaPicker
                                bind:value={item.image}
                                kind="image"
                                label="Image"
                                error={getError(
                                    errors,
                                    `gallery.${index}.image`,
                                )}
                                hint="Upload a JPG, PNG, WebP, or AVIF image up to 8 MB."
                            />
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <Field
                                label="Title"
                                error={getError(
                                    errors,
                                    `gallery.${index}.title`,
                                )}
                            >
                                <Input bind:value={item.title} />
                            </Field>
                            <Field
                                label="Link URL"
                                error={getError(
                                    errors,
                                    `gallery.${index}.link`,
                                )}
                                hint="Optional. Adds a link in the lightbox."
                            >
                                <Input
                                    bind:value={item.link}
                                    placeholder="https://..."
                                />
                            </Field>
                            <div class="sm:col-span-2">
                                <Field
                                    label="Caption"
                                    error={getError(
                                        errors,
                                        `gallery.${index}.caption`,
                                    )}
                                >
                                    <Textarea
                                        bind:value={item.caption}
                                        rows={2}
                                    />
                                </Field>
                            </div>
                            <div class="flex items-end pb-2">
                                <label
                                    class="flex items-center gap-2 text-sm font-medium"
                                >
                                    <input
                                        type="checkbox"
                                        bind:checked={item.featured}
                                        class="size-4 rounded border-input accent-foreground"
                                    />
                                    Feature this image
                                </label>
                            </div>
                        </div>
                    </Panel>
                {/each}

                {#if form.gallery.length < 100}
                    <button
                        type="button"
                        onclick={addGalleryItem}
                        class={addButton}
                    >
                        <Plus class="size-4" aria-hidden="true" />
                        Add gallery image
                    </button>
                {/if}
            </div>
        {:else if activeTab === 'pages'}
            <div class="space-y-5">
                <Panel
                    title="Gallery page"
                    description="Header copy shown above the gallery grid."
                >
                    <div class="grid gap-4">
                        <Field
                            label="Eyebrow"
                            error={getError(errors, 'gallery_header.eyebrow')}
                        >
                            <Input bind:value={form.gallery_header.eyebrow} />
                        </Field>
                        <Field
                            label="Title"
                            error={getError(errors, 'gallery_header.title')}
                        >
                            <Input bind:value={form.gallery_header.title} />
                        </Field>
                        <Field
                            label="Description"
                            error={getError(
                                errors,
                                'gallery_header.description',
                            )}
                            hint={'Use {count} to insert the number of images.'}
                        >
                            <Textarea
                                bind:value={form.gallery_header.description}
                                rows={3}
                            />
                        </Field>
                    </div>
                </Panel>

                <Panel
                    title="Blog page"
                    description="Header copy shown above the published posts."
                >
                    <div class="grid gap-4">
                        <Field
                            label="Eyebrow"
                            error={getError(errors, 'blog_header.eyebrow')}
                        >
                            <Input bind:value={form.blog_header.eyebrow} />
                        </Field>
                        <Field
                            label="Title"
                            error={getError(errors, 'blog_header.title')}
                        >
                            <Input bind:value={form.blog_header.title} />
                        </Field>
                        <Field
                            label="Description"
                            error={getError(
                                errors,
                                'blog_header.description',
                            )}
                        >
                            <Textarea
                                bind:value={form.blog_header.description}
                                rows={3}
                            />
                        </Field>
                    </div>
                </Panel>
            </div>
        {:else if activeTab === 'documents'}
            <div class="space-y-5">
                {#each form.documents as doc, index (index)}
                    <Panel
                        title={`Document ${index + 1}`}
                        description="PDFs open in a viewer; other files open in a new tab."
                    >
                        <RepeaterHeader
                            title={doc.title || 'Untitled document'}
                            subtitle={doc.file ?? undefined}
                            canMoveUp={index > 0}
                            canMoveDown={index < form.documents.length - 1}
                            onMoveUp={() =>
                                moveItem(form.documents, index, -1)}
                            onMoveDown={() =>
                                moveItem(form.documents, index, 1)}
                            onRemove={() =>
                                removeItem(form.documents, index)}
                        />

                        <div class="mt-4">
                            <MediaPicker
                                bind:value={doc.file}
                                kind="document"
                                label="File"
                                error={getError(
                                    errors,
                                    `documents.${index}.file`,
                                )}
                                hint="PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, or CSV up to 10 MB."
                            />
                        </div>

                        <div class="mt-4 grid gap-4">
                            <Field
                                label="Title"
                                error={getError(
                                    errors,
                                    `documents.${index}.title`,
                                )}
                            >
                                <Input bind:value={doc.title} />
                            </Field>
                            <Field
                                label="Description"
                                error={getError(
                                    errors,
                                    `documents.${index}.description`,
                                )}
                            >
                                <Textarea
                                    bind:value={doc.description}
                                    rows={2}
                                />
                            </Field>
                        </div>
                    </Panel>
                {/each}

                {#if form.documents.length < 12}
                    <button
                        type="button"
                        onclick={addDocument}
                        class={addButton}
                    >
                        <Plus class="size-4" aria-hidden="true" />
                        Add document
                    </button>
                {/if}
            </div>
        {:else if activeTab === 'cta'}
            <Panel
                title="Call to action"
                description="The blue block near the bottom of the landing page."
            >
                <div class="grid gap-4 sm:grid-cols-2">
                    <Field
                        label="Eyebrow"
                        error={getError(errors, 'cta.eyebrow')}
                    >
                        <Input bind:value={form.cta.eyebrow} />
                    </Field>
                    <Field
                        label="Button label"
                        error={getError(errors, 'cta.button')}
                    >
                        <Input bind:value={form.cta.button} />
                    </Field>
                    <div class="sm:col-span-2">
                        <Field
                            label="Heading"
                            error={getError(errors, 'cta.heading')}
                        >
                            <Textarea bind:value={form.cta.heading} rows={2} />
                        </Field>
                    </div>
                    <div class="sm:col-span-2">
                        <Field
                            label="Description"
                            error={getError(errors, 'cta.description')}
                        >
                            <Textarea
                                bind:value={form.cta.description}
                                rows={3}
                            />
                        </Field>
                    </div>
                    <div class="sm:col-span-2">
                        <Field
                            label="Email subject"
                            error={getError(errors, 'cta.subject')}
                            hint="Used in every mailto link on the page."
                        >
                            <Input bind:value={form.cta.subject} />
                        </Field>
                    </div>
                </div>
            </Panel>

            <Panel title="Footer">
                <Field
                    label="Footer note"
                    error={getError(errors, 'footer_note')}
                    hint="Displayed next to the copyright."
                >
                    <Input bind:value={form.footer_note} />
                </Field>
            </Panel>
        {/if}
    </form>

    {#if form.isDirty}
        <div
            class="sticky bottom-4 z-20 flex flex-col gap-3 rounded-xl border border-border/70 bg-background/95 p-3 shadow-lg shadow-slate-900/5 backdrop-blur sm:flex-row sm:items-center sm:justify-between dark:shadow-black/30"
        >
            <p class="text-sm text-muted-foreground">
                You have unsaved changes.
            </p>
            <div class="flex items-center gap-2">
                <Button
                    type="button"
                    variant="ghost"
                    onclick={discard}
                    disabled={form.processing}
                >
                    Discard
                </Button>
                <Button
                    type="button"
                    onclick={save}
                    disabled={form.processing}
                >
                    {form.processing ? 'Saving...' : 'Save changes'}
                </Button>
            </div>
        </div>
    {/if}
</div>
