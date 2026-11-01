export function getError(
    errors: Record<string, unknown>,
    path: string,
): string | undefined {
    const value = errors[path];

    if (Array.isArray(value)) {
        return typeof value[0] === 'string' ? value[0] : undefined;
    }

    return typeof value === 'string' ? value : undefined;
}
