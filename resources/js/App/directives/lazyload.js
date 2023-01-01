function loadImage(el) {
    el.src = el.dataset.src
}

function createIntersectionObserver(el) {
    const options = {
        root: null,
        threshold: 0
    }
    const firstLoad = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                loadImage(el)
                observer.unobserve(el)
            }
        })
    }
    const observer = new IntersectionObserver(firstLoad, options)
    observer.observe(el)
}

export default {
    created: el => {
        if (!window['IntersectionObserver']) {
            loadImage(el)
        } else {
            createIntersectionObserver(el)
        }
    },
    beforeUpdate: el => {
        if (!window['IntersectionObserver']) {
            loadImage(el)
        } else {
            createIntersectionObserver(el)
        }
    }
}
