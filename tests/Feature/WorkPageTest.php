<?php

use App\Models\PortfolioSetting;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('work page renders the full project list', function () {
    $this->get(route('work.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Work/Index')
            ->where('portfolio.profile.name', config('portfolio.defaults.profile.name'))
            ->has('portfolio.projects', 3)
            ->where('portfolio.work_header.title', config('portfolio.defaults.work_header.title'))
        );
});

test('work header copy is editable from portfolio content', function () {
    $content = config('portfolio.defaults');
    $content['work_header']['title'] = 'All projects';
    $content['work_header']['description'] = 'A custom list of {count} projects.';

    PortfolioSetting::updateContent($content);

    $this->get(route('work.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('portfolio.work_header.title', 'All projects')
            ->where('portfolio.work_header.description', 'A custom list of {count} projects.')
        );
});

test('work page exposes its own canonical and collection schema', function () {
    $base = rtrim((string) config('app.url'), '/');

    $this->get(route('work.index'))
        ->assertOk()
        ->assertSee('rel="canonical" href="'.$base.'/work"', false)
        ->assertSee('CollectionPage', false)
        ->assertSee('index, follow', false);
});

test('projects can be marked for the homepage', function () {
    $user = User::factory()->admin()->create();
    $content = config('portfolio.defaults');
    $content['projects'][0]['selected'] = false;

    $this->actingAs($user)
        ->put(route('portfolio.update'), $content)
        ->assertSessionHasNoErrors();

    expect(PortfolioSetting::content()['projects'][0]['selected'])->toBeFalse();
});

test('project detail pages render the saved project', function () {
    $content = config('portfolio.defaults');
    $content['projects'][0]['slug'] = 'nusa-board';
    $content['projects'][0]['gallery'] = ['/images/portfolio/gallery/atlas.svg'];
    $base = rtrim((string) config('app.url'), '/');

    PortfolioSetting::updateContent($content);

    $this->get(route('work.show', 'nusa-board'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Work/Show')
            ->where('project.slug', 'nusa-board')
            ->where('project.name', $content['projects'][0]['name'])
            ->has('project.gallery', 1)
            ->where('seo.canonical', $base.'/work/nusa-board')
        );
});

test('missing projects return a 404 response', function () {
    $this->get(route('work.show', 'does-not-exist'))->assertNotFound();
});

test('legacy projects receive generated unique slugs', function () {
    $content = config('portfolio.defaults');
    unset($content['projects'][0]['slug']);
    $content['projects'][1]['name'] = $content['projects'][0]['name'];
    unset($content['projects'][1]['slug']);

    PortfolioSetting::updateContent($content);

    $projects = PortfolioSetting::content()['projects'];

    expect($projects[0]['slug'])->toBe('nusa-board');
    expect($projects[1]['slug'])->toBe('nusa-board-2');
});

test('sitemap includes project detail pages and project images', function () {
    $content = config('portfolio.defaults');
    $content['projects'][0]['slug'] = 'nusa-board';
    $content['projects'][0]['image'] = '/images/portfolio/gallery/atlas.svg';
    $content['projects'][0]['gallery'] = ['/images/portfolio/gallery/ember.svg'];
    $base = rtrim((string) config('app.url'), '/');

    PortfolioSetting::updateContent($content);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee('<loc>'.$base.'/work/nusa-board</loc>', false)
        ->assertSee('<image:loc>'.$base.'/images/portfolio/gallery/atlas.svg</image:loc>', false)
        ->assertSee('<image:loc>'.$base.'/images/portfolio/gallery/ember.svg</image:loc>', false)
        ->assertSee('<priority>0.8</priority>', false);
});
