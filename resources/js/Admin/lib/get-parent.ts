export function getParent(element: HTMLElement): HTMLElement | null {
    if (element.parentElement) {
        return element.parentElement;
    }

    // webcomponents workaround
    if (window.ShadowRoot
        && element.parentNode
        && element.parentNode instanceof ShadowRoot
        && element.parentNode.host) {
        return element.parentNode.host as HTMLElement;
    }

    return null;
}
