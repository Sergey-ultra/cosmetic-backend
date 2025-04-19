import getScrollParent from './get-scroll-parent';

const GUTTER = 8;
const MAX_HEIGHT = 32 * 16;
const MAX_WIDTH = 32 * 16;

interface PopupAlignment {
    x: 'left' | 'right';
    xLeft: number;
    xRight: number;
    xFree: number;
    xMax: number;

    y: 'top' | 'middle' | 'bottom';
    yTop: number;
    yBottom: number;
    yFree: number;
    yMax: number;
}

interface PopupAlignmentParameters {
    control: HTMLElement;
    popup: HTMLElement;

    parentTop: number;
    parentHeight: number;

    parentLeft: number;
    parentWidth: number;
    parentScrollTop: number;
    parentScrollLeft: number;

    popupFixedHeight?: boolean;
}

function getAlignmentValue(parameters: PopupAlignmentParameters): PopupAlignment {
    let {
        control,
        popup, popupFixedHeight,
        parentTop, parentHeight,
        parentLeft, parentWidth,
        parentScrollTop,
    } = parameters;

    let controlBoundingClientRect = control.getBoundingClientRect();
    let controlTop = controlBoundingClientRect.top - parentTop;
    let controlHeight = controlBoundingClientRect.bottom - controlBoundingClientRect.top;
    let controlLeft = controlBoundingClientRect.left - parentLeft;
    let controlWidth = controlBoundingClientRect.right - controlBoundingClientRect.left;

    let popupHeight = popup.offsetHeight;
    let popupWidth = popup.offsetWidth;

    let maxHeight = Math.max(parentHeight, MAX_HEIGHT);
    let maxWidth = Math.max(parentWidth, MAX_WIDTH);

    let alignment: PopupAlignment = {
        x: 'left',
        xLeft: controlLeft,
        xRight: parentWidth - (controlLeft + popupWidth),
        xFree: 0,
        xMax: 0,

        y: 'bottom',
        yTop: controlTop,
        yBottom: parentHeight - (controlTop + controlHeight),
        yFree: 0,
        yMax: 0,
    };

    if (alignment.xLeft + popupWidth > parentWidth
        && alignment.xLeft > alignment.xRight) {
        alignment.x = 'right';
        alignment.xFree = alignment.xLeft + controlWidth - GUTTER;
    } else {
        alignment.xFree = parentWidth - alignment.xLeft - GUTTER;
    }
    alignment.xMax = Math.min(alignment.xFree, maxWidth);

    if (alignment.yTop + controlHeight + popupHeight > parentHeight
        && alignment.yTop > alignment.yBottom
        && (!popupFixedHeight
            || alignment.yTop >= popupHeight
            || parentScrollTop + controlTop >= popupHeight)
    ) {
        alignment.y = 'top';
        alignment.yFree = alignment.yTop - GUTTER;
    } else {
        alignment.yFree = parentHeight - alignment.yTop - controlHeight - GUTTER;
    }
    alignment.yMax = Math.min(alignment.yFree, maxHeight);

    // if (popupFixedHeight
    //     && alignment.yTop + alignment.yBottom + controlHeight <= popupHeight) {
    //     alignment.y = "middle";
    // }

    return alignment;
}

/**
 * Получение лучшего местоположения для попапа
 * без учета стиля родительских элементов
 * @export
 * @param {HTMLElement} popup
 * @returns {PopupAlignment}
 */
export function getPopupAlignment(control: HTMLElement, popup: HTMLElement, popupFixedHeight = false): PopupAlignment {
    return getAlignmentValue({
        control,
        popup,

        parentTop: 0,
        parentLeft: 0,
        parentHeight: window.innerHeight,
        parentWidth: window.innerWidth,
        parentScrollTop: window.scrollY,
        parentScrollLeft: window.scrollX,

        popupFixedHeight,
    });
}

/**
 * Получение лучшего местоположения для попапа
 * с учетом стиля родительских элементов
 * @export
 * @param {HTMLElement} popup
 * @returns {PopupAlignment}
 */
export function getPopupAlignmentGreedy(
    control: HTMLElement, popup: HTMLElement, popupFixedHeight = false,
): PopupAlignment {
    let parent = getScrollParent(control, true);
    if (parent.tagName === 'HTML' || parent.tagName === 'BODY') {
        return getPopupAlignment(control, popup, popupFixedHeight);
    }

    let parentBoundingClientRect = parent.getBoundingClientRect();
    let parentTop = parentBoundingClientRect.top;
    let parentLeft = parentBoundingClientRect.left;
    let parentHeight = Math.min(
        parentBoundingClientRect.bottom - parentBoundingClientRect.top,
        window.innerHeight,
    );
    let parentWidth = Math.min(
        parentBoundingClientRect.right - parentBoundingClientRect.left,
        window.innerWidth,
    );

    return getAlignmentValue({
        control,
        popup,
        popupFixedHeight,

        parentTop,
        parentLeft,
        parentHeight,
        parentWidth,
        parentScrollTop: parent.scrollTop,
        parentScrollLeft: parent.scrollLeft,
    });
}
