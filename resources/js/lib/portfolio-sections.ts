export type PortfolioSectionId =
    | 'profile'
    | 'about'
    | 'experience'
    | 'work'
    | 'skills'
    | 'education'
    | 'gallery'
    | 'documents'
    | 'pages'
    | 'cta';

export type PortfolioSection = {
    id: PortfolioSectionId;
    label: string;
};

export const portfolioSectionGroups: {
    title: string;
    sections: PortfolioSection[];
}[] = [
    {
        title: 'Content',
        sections: [
            { id: 'profile', label: 'Profile' },
            { id: 'about', label: 'About' },
            { id: 'experience', label: 'Experience' },
            { id: 'work', label: 'Work' },
            { id: 'skills', label: 'Skills' },
            { id: 'education', label: 'Education' },
        ],
    },
    {
        title: 'Media',
        sections: [
            { id: 'gallery', label: 'Gallery' },
            { id: 'documents', label: 'Files' },
        ],
    },
    {
        title: 'Publishing',
        sections: [
            { id: 'pages', label: 'Page headers' },
            { id: 'cta', label: 'Call to action' },
        ],
    },
];

export const portfolioSections = portfolioSectionGroups.flatMap(
    (group) => group.sections,
);

export function isPortfolioSection(value: string): value is PortfolioSectionId {
    return portfolioSections.some((section) => section.id === value);
}
