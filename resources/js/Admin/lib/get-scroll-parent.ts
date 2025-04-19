import { getParent } from './get-parent';

export default function getScrollParent(element: HTMLElement, includeHidden: boolean = true): HTMLElement {
    let style = getComputedStyle(element);
    let excludeStaticParent = style.position === 'absolute';
    let overflowRegex = includeHidden ? /(auto|scroll|hidden)/ : /(auto|scroll)/;

    if (style.position === 'fixed') {
        return document.body;
    }

    let parent: HTMLElement | null = getParent(element);
    while (parent) {
        style = getComputedStyle(parent);

        if (excludeStaticParent && style.position === 'static') {
            parent = getParent(parent);
            continue;
        }

        if (style.overflow
            && overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) {
            return parent;
        }

        parent = getParent(parent);
    }

    return document.body;
}
