// import { nanoid } from 'nanoid/non-secure';

export function isEmptyOption(node) {
    return node.label === null && node.value === '';
}

function getNodeColor(node) {
    if (node.hasAttribute('color')) {
        return node.getAttribute('color');
    }

    if (node.style && node.style.color) {
        return node.style.color;
    }

    // const style = getComputedStyle(node);
    // if (style && style.color) {
    //     return style.color;
    // }

    return null;
}

function dataFromNode(node) {
    if (!node) {
        return null;
    }

    if (node instanceof HTMLElement) {
        if (!node.tagName) {
            return null;
        }

        let nodeLabel = node.label !== undefined ? String(node.label).trim() : '';
        let nodeTitle = node.title !== undefined ? String(node.title).trim() : '';

        let type = node.tagName.toLowerCase();
        let label = nodeLabel;
        let title = (type === 'option' ? node.innerHTML.trim() : (nodeTitle || nodeLabel || ''));

        return {
            disabled: node.disabled,
            // className: node.className,
            label,
            color: getNodeColor(node),
            // optgroupIndex,
            selected: node.selected,
            // style: node.getAttribute("style"),
            title,
            type,
            value: node.value,
        };
    }

    if (typeof node === 'object') {
        let nodeLabel = node.label !== undefined ? String(node.label).trim() : '';
        let nodeTitle = node.title !== undefined ? String(node.title).trim() : '';

        return {
            disabled: Boolean(node.disabled),
            // className: node.className || null,
            label: nodeLabel,
            color: node.color,
            // optgroupIndex,
            selected: false,
            // style: node.style || null,
            title: nodeTitle || nodeLabel || '',
            type: node.type || 'option',
            value: node.value,
        };
    }

    return null;
}

function processOptions(nodes) {
    let data = [];

    for (let i = 0; i < nodes.length; i += 1) {
        let node = nodes[i].elm || nodes[i];
        let newNode = dataFromNode(node);

        if (newNode) {
            // newNode.id = nanoid(16);
            if (newNode.type === 'optgroup') {
                newNode.children = processOptions(node.children);
                data.push(newNode);
            } else if (newNode.type === 'option') {
                data.push(newNode);
            }
        }
    }

    return data;
}

export function selectedOptions(nodes) {
    let selected = [];

    for (let i = 0; i < nodes.length; i += 1) {
        let node = nodes[i].elm || nodes[i];

        if (node.type === 'option' && node.selected) {
            selected.push(node);
        } else if (node.type === 'optgroup' && node.children) {
            selected = selected.concat(
                selectedOptions(node.children),
            );
        }
    }

    return selected;
}

function getFirstOption(nodes) {
    for (let i = 0; i < nodes.length; i += 1) {
        let node = nodes[i];
        if (node.type === 'option') {
            return node;
        }
        if (node.type === 'optgroup') {
            let option = getFirstOption(node.children);

            if (option) {
                return option;
            }
        }
    }

    return null;
}


export function setupOptionSelected(nodes, value, isMultiple) {
    if (!nodes || !nodes.length) {
        return [];
    }

    let valueIsArray = Array.isArray(value);
    let isValueFound = false;

    for (let i = 0; i < nodes.length; i += 1) {
        let node = nodes[i];

        if (node.type === 'option') {
            let isValue = valueIsArray ? value.includes(node.value) : (node.value === value);

            node.selected = isValue && !isValueFound;
            if (isMultiple !== true) {
                isValueFound = isValueFound || (isValue && isEmptyOption(node));
            }
        } else if (node.type === 'optgroup') {
            setupOptionSelected(node.children, value, isMultiple);
        }
    }

    return nodes;
}

export function setOptionSelected(nodes, value, selectedArg, explicit) {
    if (!nodes || !nodes.length) {
        return;
    }

    let selected = Boolean(selectedArg);

    let valueIsArray = Array.isArray(value);

    for (let i = 0; i < nodes.length; i += 1) {
        let node = nodes[i];

        if (node.type === 'option') {
            let isValue = valueIsArray ? value.includes(node.value) : (node.value === value);

            if (explicit === true) {
                if (isValue) {
                    node.selected = selected;
                }
            } else {
                node.selected = isValue ? selected : false;
            }
        } else if (node.type === 'optgroup') {
            setOptionSelected(node.children, value, selected, explicit);
        }
    }

    // return nodes;
}


export function createOptions(nodes, value, isMultiple) {
    let options = processOptions(nodes);
    setupOptionSelected(options, value, isMultiple);
    return options;
}

export function createOptionsFromNodes(nodes, value, isMultiple) {
    let options = processOptions(nodes);

    if (value !== undefined) {
        setupOptionSelected(options, value, isMultiple);
    } else if (options && options.length) {
        let selected = selectedOptions(options);
        if (!selected || !selected.length) {
            let firstOption = getFirstOption(options);
            if (firstOption) {
                firstOption.selected = true;
            }
        }
    }

    return options;
}


function prepareString(value) {
    return value ? value.toString().toLowerCase().trim() : '';
}

export function filterOptions(nodes, _search) {
    let result = [];
    let search = prepareString(_search);

    for (let i = 0; i < nodes.length; i += 1) {
        let node = nodes[i];

        if (node.type === 'option' && isEmptyOption(node)) {
            continue; // eslint-disable-line no-continue
        } else if (node.type === 'option') {
            if ((node.label && prepareString(node.label).includes(search))
                || (node.title && prepareString(node.title).includes(search))
                // || node.value && prepareString(node.value).includes(search)
            ) {
                result.push(node);
            }
        } else if (node.type === 'optgroup') {
            let children = filterOptions(node.children, search);

            if (children.length) {
                result.push({
                    ...node,
                    children,
                });
            }
        }
    }
    return result;
}

export function valueToString(value) {
    if (typeof value !== 'string' && Array.isArray(value)) {
        return [...value].sort().join('~');
    }
    return value || '';
}
