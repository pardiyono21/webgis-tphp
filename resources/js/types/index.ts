export * from './auth';
export * from './navigation';
export * from './ui';

import type { Auth } from './auth';

export interface FlashMessages {
    success: string | null;
    error: string | null;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    flash: FlashMessages;
    sidebarOpen: boolean;
    [key: string]: unknown;
};
