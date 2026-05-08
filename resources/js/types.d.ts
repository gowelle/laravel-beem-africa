export { };

declare global {
    interface Window {
        BeemPay: unknown;
        InitializeBeem?: () => void;
    }
}
