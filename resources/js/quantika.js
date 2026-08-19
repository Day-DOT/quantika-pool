document.addEventListener('DOMContentLoaded', () => {

    const mobileMenu =
        document.getElementById('mobileMenu');

    const sidebar =
        document.getElementById('sidebar');


    if (mobileMenu && sidebar) {

        mobileMenu.addEventListener('click', () => {

            sidebar.classList.toggle('open');

        });

    }


    /*
    =========================================
    ANIMACIONES DE ENTRADA
    =========================================
    */

    const elements =
        document.querySelectorAll(
            '.stat-card, .panel, .level-card, .branch-card'
        );


    const observer =
        new IntersectionObserver(
            entries => {

                entries.forEach(entry => {

                    if (entry.isIntersecting) {

                        entry.target.style.opacity = '1';

                        entry.target.style.transform =
                            'translateY(0)';

                    }

                });

            },
            {
                threshold: .08
            }
        );


    elements.forEach(element => {

        element.style.opacity = '0';

        element.style.transform =
            'translateY(12px)';

        element.style.transition =
            'opacity .5s ease, transform .5s ease';

        observer.observe(element);

    });


    /*
    =========================================
    CERRAR SIDEBAR EN MOBILE
    =========================================
    */

    document.addEventListener('click', event => {

        if (
            window.innerWidth <= 850 &&
            sidebar &&
            !sidebar.contains(event.target) &&
            !mobileMenu?.contains(event.target)
        ) {

            sidebar.classList.remove('open');

        }

    });

});