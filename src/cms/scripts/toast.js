class Toast {
    constructor(config) {
        config = config || {};
        this.position = config.position || 'center';
        this.timeout = config.timeout || 3000;

        this.message = config.message;

        this.create();
    }

    create() {
        let toast = document.createElement('div');
        toast.classList.add('toast__container--' + this.position);
        toast.innerHTML = "<div class='toast'><p>" + this.message + "</p></div>";
        document.body.appendChild(toast);

        toast.show = () => {
            toast.clear();
            toast.childNodes[0].classList.add('active');
            setTimeout(() => {
               toast.hide();
            }, this.timeout);
        };
        toast.hide = () => {
            toast.childNodes[0].classList.replace('active', 'closing');
            toast.childNodes[0].addEventListener('animationend', () => {
                 toast.remove();
            });
        };
        toast.clear = () => {
            let toasts = document.querySelectorAll('div[class^=toast__container]');
            for (let i = 0; i < toasts.length; i++) {
                if (toasts[i].childNodes[0].classList.contains('active')) {
                    toasts[i].hide();
                }
            }
        };

        toast.show();
    }
}