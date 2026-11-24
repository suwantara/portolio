export type SocialName =
    | 'github'
    | 'linkedin'
    | 'instagram'
    | 'x'
    | 'discord'
    | 'youtube'
    | 'reddit'
    | 'threads'
    | 'tiktok'
    | 'facebook'
    | 'dribbble'
    | 'behance'
    | 'bluesky'
    | 'mastodon'
    | 'website';

export type PortfolioSocial = {
    name: SocialName;
    label: string;
    href: string;
};

export type PortfolioProfile = {
    name: string;
    monogram: string;
    role: string;
    tagline: string;
    location: string;
    timezone: string;
    email: string;
    availability: string;
    availability_note: string;
    availability_open: boolean;
    avatar: string | null;
};

export type PortfolioStat = {
    value: string;
    label: string;
};

export type PortfolioExperience = {
    role: string;
    company: string;
    employment: string;
    period: string;
    location: string;
    description: string;
    highlights: string[];
    skills: string[];
    monogram: string;
    accent: string;
};

export type PortfolioProject = {
    name: string;
    year: string;
    description: string;
    tags: string[];
    cover: string;
    image: string | null;
    link: string | null;
    featured: boolean;
};

export type PortfolioGalleryItem = {
    image: string | null;
    title: string;
    caption: string;
    link: string | null;
    featured: boolean;
};

export type PortfolioDocument = {
    title: string;
    description: string;
    file: string | null;
};

export type PortfolioSkillGroup = {
    title: string;
    skills: string[];
};

export type PortfolioEducation = {
    degree: string;
    school: string;
    period: string;
    description: string;
};

export type PortfolioLanguage = {
    name: string;
    level: string;
};

export type PortfolioPageHeader = {
    eyebrow: string;
    title: string;
    description: string;
};

export type PortfolioCta = {
    eyebrow: string;
    heading: string;
    description: string;
    button: string;
    subject: string;
};

export type PortfolioContent = {
    profile: PortfolioProfile;
    stats: PortfolioStat[];
    about: string[];
    about_tags: string[];
    experience: PortfolioExperience[];
    projects: PortfolioProject[];
    gallery: PortfolioGalleryItem[];
    documents: PortfolioDocument[];
    skill_groups: PortfolioSkillGroup[];
    education: PortfolioEducation[];
    languages: PortfolioLanguage[];
    currently_exploring: string[];
    socials: PortfolioSocial[];
    gallery_header: PortfolioPageHeader;
    blog_header: PortfolioPageHeader;
    cta: PortfolioCta;
    footer_note: string;
};

export type PortfolioSeo = {
    title: string;
    description: string;
    canonical: string;
    image: string;
    type: string;
    robots: string;
    site_name: string;
    author?: string;
    schema?: Record<string, unknown>;
};

export type PortfolioOptions = {
    experience_accents: string[];
    project_covers: string[];
    socials: SocialName[];
};
