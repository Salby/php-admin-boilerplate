class Counter {
    constructor(query) {
        this.counters = document.querySelectorAll(query)

        this.counters.forEach(counter => {
            if (!counter.initialized) {
                this.init(counter)
                counter.initialized = true
            }
        })
    }

    init(elem) {
        let counter = elem

        counter.next = counter.querySelector('.counter__button--next')
        counter.prev = counter.querySelector('.counter__button--prev')

        counter.input = counter.querySelector('input[type=number]')
        counter.initial = parseInt(counter.input.value)
        
        counter.next.addEventListener('click', () => this.update(counter, 'next'))
        counter.prev.addEventListener('click', () => this.update(counter, 'prev'))
    }

    update(elem, method) {
        let counter = elem

        let event = new Event('changed')

        const next = method === 'next'
        let count = parseInt(counter.input.value)

        if (next) {
            count++
        } else {
            if (count > 1) count--
        }

        counter.input.setAttribute('value', count.toString())
        counter.input.dispatchEvent(event)
    }

}