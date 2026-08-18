// Cambio automático y manual de las imágenes del carrusel

document.addEventListener('DOMContentLoaded', () => {
    const diapositivas = document.querySelectorAll('.diapositiva');
    const indicadores = document.querySelectorAll('.indicador');
    const botonAnterior = document.querySelector('.anterior');
    const botonSiguiente = document.querySelector('.siguiente');
    let indiceActual = 0;
    let intervalo;

    // Muestra una diapositiva y actualiza los indicadores
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

    // Menú adaptable para tabletas y teléfonos
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

    // Filtra los catálogos de la página principal
    const formularioBusqueda = document.querySelector('#buscador-principal');
    const textoBusqueda = document.querySelector('#texto-busqueda');
    const tipoBusqueda = document.querySelector('#tipo-busqueda');
    const elementos = document.querySelectorAll('.elemento-buscable');

    formularioBusqueda?.addEventListener('submit', (evento) => {
        evento.preventDefault();

        const texto = textoBusqueda.value.trim().toLocaleLowerCase('es');
        const tipo = tipoBusqueda.value;
        let primerResultado = null;

        elementos.forEach((elemento) => {
            const coincideTexto = elemento.dataset.busqueda
                .toLocaleLowerCase('es')
                .includes(texto);
            const coincideTipo = tipo === 'todos' || elemento.dataset.tipo === tipo;
            const visible = coincideTexto && coincideTipo;

            elemento.hidden = !visible;

            if (visible && primerResultado === null) {
                primerResultado = elemento;
            }
        });

        primerResultado?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

});
