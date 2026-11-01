export type PostCategory = 'tutorial' | 'tips' | 'progress' | 'notes';

export type PostStatus = 'draft' | 'published';

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

export type BlogPostSummary = {
    id: number;
    title: string;
    slug: string;
    category: PostCategory;
    excerpt: string | null;
    cover_image: string | null;
    published_at: string | null;
    reading_minutes: number;
};

export type BlogPost = Omit<BlogPostSummary, 'id'> & {
    html: string;
};

export type ManagedPost = BlogPostSummary & {
    status: PostStatus;
    updated_at: string;
};

export type BlogPostForm = {
    title: string;
    slug: string;
    category: PostCategory;
    excerpt: string;
    cover_image: string | null;
    content: string;
    status: PostStatus;
};

export type UploadedImage = {
    url: string;
    path: string;
    name: string;
};
