/*
    Cambio automático y manual de las imágenes del carrusel.
    Apertura y cierre del menú en pantallas pequeñas.
    Restricción básica de fechas del formulario de búsqueda.
 */

document.addEventListener('DOMContentLoaded', () => {
    const diapositivas = document.querySelectorAll('.diapositiva');
    const indicadores = document.querySelectorAll('.indicador');
    const botonAnterior = document.querySelector('.anterior');
    const botonSiguiente = document.querySelector('.siguiente');
    let indiceActual = 0;
    let intervalo;

    // Muestra una diapositiva y actualiza los indicadores.
    function mostrarDiapositiva(indice) {
        if (diapositivas.length === 0) {
            return;
        }

        indiceActual = (indice + diapositivas.length) % diapositivas.length;

        diapositivas.forEach((diapositiva, posicion) => {
            diapositiva.classList.toggle('activa', posicion === indiceActual);
        });

        indicadores.forEach((indicador, posicion) => {
            indicador.classList.toggle('activo', posicion === indiceActual);
        });
    }

    // Reinicia el cambio automático después de una interacción manual
    function reiniciarIntervalo() {
        window.clearInterval(intervalo);
        intervalo = window.setInterval(() => {
            mostrarDiapositiva(indiceActual + 1);
        }, 6000);
    }

    botonAnterior?.addEventListener('click', () => {
        mostrarDiapositiva(indiceActual - 1);
        reiniciarIntervalo();
    });

    botonSiguiente?.addEventListener('click', () => {
        mostrarDiapositiva(indiceActual + 1);
        reiniciarIntervalo();
    });

    indicadores.forEach((indicador, indice) => {
        indicador.addEventListener('click', () => {
            mostrarDiapositiva(indice);
            reiniciarIntervalo();
        });
    });

    reiniciarIntervalo();

    // Menú adaptable para tabletas y teléfonos.
    const botonMenu = document.querySelector('.boton-menu');
    const menu = document.querySelector('.menu-principal');

    botonMenu?.addEventListener('click', () => {
        const estaAbierto = menu?.classList.toggle('abierto') ?? false;
        botonMenu.setAttribute('aria-expanded', String(estaAbierto));
    });

    menu?.querySelectorAll('a').forEach((enlace) => {
        enlace.addEventListener('click', () => {
            menu.classList.remove('abierto');
            botonMenu?.setAttribute('aria-expanded', 'false');
        });
    });

    // Validación visual básica para impedir fechas anteriores al día actual.
    const entrada = document.querySelector('#entrada');
    const salida = document.querySelector('#salida');
    const hoy = new Date().toISOString().split('T')[0];

    if (entrada && salida) {
        entrada.min = hoy;
        salida.min = hoy;

        entrada.addEventListener('change', () => {
            salida.min = entrada.value || hoy;

            if (salida.value && salida.value < entrada.value) {
                salida.value = entrada.value;
            }
        });
    }
});
