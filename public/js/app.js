// Variables que apuntan a elementos del DOM
const btnEscanear = document.getElementById('btnEscanear');
const btnReportes = document.getElementById('btnReportes');
const tablaArchivos = document.getElementById('tablaArchivos');
const filtroPeligrosos = document.getElementById('filtroPeligrosos');
const mensajeEstado = document.getElementById('mensajeEstado');
const seccionReportes = document.getElementById('seccionReportes');
const listaReportes = document.getElementById('listaReportes');
const segundosRestantes = document.getElementById('segundosRestantes');

// Contador de segundos para el autoescaneo y bandera que indica si hay un escaneo en curso
let contador = 60;
let escaneoEnCurso = false;

// Eventos principales de la interfaz
btnEscanear.addEventListener('click', () => ejecutarEscaneo(false));
btnReportes.addEventListener('click', cargarReportes);
filtroPeligrosos.addEventListener('change', aplicarFiltro);
tablaArchivos.addEventListener('click', manejarMarcadoPeligroso);

/*
    Se hace un escaneo falso de archivos y actualiza la tabla con los resultados
 */
async function ejecutarEscaneo(esAutomatico) {
    if (escaneoEnCurso) return;

    escaneoEnCurso = true;
    btnEscanear.disabled = true;
    mostrarMensaje(esAutomatico ? 'Ejecutando auto-detección...' : 'Escaneando directorio...');

    try {
        const respuesta = await fetch('api/escanear.php', { method: 'POST' });
        const datos = await leerJson(respuesta);

        if (!respuesta.ok || !datos.success) {
            throw new Error(datos.message || 'Error durante el escaneo.');
        }

        // Cada archivo recibido se convierte en una nueva fila
        datos.archivos.forEach(agregarFilaArchivo);
        mostrarMensaje(`Escaneo #${datos.escaneo_id}: ${datos.cantidad_archivos} archivo(s) detectado(s).`);
        aplicarFiltro();
    } catch (error) {
        mostrarMensaje(error.message, true);
    } finally {
        escaneoEnCurso = false;
        btnEscanear.disabled = false;
        reiniciarContador();
    }
}

/*
    Construye una fila HTML con la información de un archivo nuevo
 */
function agregarFilaArchivo(archivo) {
    const fila = document.createElement('tr');
    fila.dataset.id = archivo.id;
    fila.dataset.peligroso = archivo.peligroso;

    fila.innerHTML = `
        <td>${Number(archivo.id)}</td>
        <td>${escaparHtml(archivo.nombre)}</td>
        <td>${Number(archivo.tamano).toLocaleString()} KB</td>
        <td>${escaparHtml(archivo.fecha_detectado)}</td>
        <td>#${Number(archivo.escaneo_id)}</td>
        <td><span class="etiqueta etiqueta-normal">Normal</span></td>
        <td><button class="boton-marcar" data-id="${Number(archivo.id)}">Marcar peligroso</button></td>
    `;

    // prepend coloca los resultados más recientes al inicio de la tabla
    tablaArchivos.prepend(fila);
}

/*
    Detecta si se hizo clic en un botón para cambiar el estado peligroso
 */
async function manejarMarcadoPeligroso(evento) {
    const boton = evento.target.closest('.boton-marcar');
    if (!boton) return;

    const id = Number(boton.dataset.id);
    boton.disabled = true;

    try {
        const respuesta = await fetch('api/marcar_peligroso.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });

        const datos = await leerJson(respuesta);

        if (!respuesta.ok || !datos.success) {
            throw new Error(datos.message || 'No fue posible actualizar el archivo.');
        }

        actualizarFilaPeligrosa(id, datos.peligroso);
        aplicarFiltro();
    } catch (error) {
        mostrarMensaje(error.message, true);
    } finally {
        boton.disabled = false;
    }
}

// Actualiza el texto, la clase y el botón de una fila sin recargar la página
function actualizarFilaPeligrosa(id, peligroso) {
    const fila = tablaArchivos.querySelector(`tr[data-id="${id}"]`);
    if (!fila) return;

    const estaPeligroso = Number(peligroso) === 1;
    fila.dataset.peligroso = estaPeligroso ? '1' : '0';
    fila.classList.toggle('archivo-peligroso', estaPeligroso);

    const etiqueta = fila.querySelector('.etiqueta');
    etiqueta.textContent = estaPeligroso ? 'Peligroso' : 'Normal';
    etiqueta.className = `etiqueta ${estaPeligroso ? 'etiqueta-peligro' : 'etiqueta-normal'}`;

    const boton = fila.querySelector('.boton-marcar');
    boton.textContent = estaPeligroso ? 'Quitar peligro' : 'Marcar peligroso';
}

// Muestra u oculta las filas de la tabla según el filtro seleccionado
function aplicarFiltro() {
    const soloPeligrosos = filtroPeligrosos.value === 'peligrosos';

    tablaArchivos.querySelectorAll('tr').forEach((fila) => {
        const esPeligroso = fila.dataset.peligroso === '1';
        fila.hidden = soloPeligrosos && !esPeligroso;
    });
}

/*
    Consulta JSONPlaceholder, utiliza las publicaciones como reportes y los muestra en la interfaz
 */
async function cargarReportes() {
    seccionReportes.classList.remove('oculto');
    listaReportes.innerHTML = '<p>Cargando reportes...</p>';
    btnReportes.disabled = true;

    try {
        const respuesta = await fetch('https://jsonplaceholder.typicode.com/posts?_limit=6');
        const reportes = await leerJson(respuesta);

        if (!respuesta.ok) {
            throw new Error('No fue posible consultar la API externa.');
        }

        listaReportes.innerHTML = '';

        reportes.forEach((reporte) => {
            const tarjeta = document.createElement('article');
            tarjeta.className = 'reporte';
            tarjeta.innerHTML = `
                <span>Incidente #${Number(reporte.id)}</span>
                <h3>${escaparHtml(reporte.title)}</h3>
                <p>${escaparHtml(reporte.body)}</p>
            `;
            listaReportes.appendChild(tarjeta);
        });
    } catch (error) {
        listaReportes.innerHTML = `<p class="error">${escaparHtml(error.message)}</p>`;
    } finally {
        btnReportes.disabled = false;
    }
}

// Intenta convertir una respuesta HTTP en JSON, y lanza un error si no es posible
async function leerJson(respuesta) {
    try {
        return await respuesta.json();
    } catch (error) {
        throw new Error('El servidor devolvió una respuesta no válida.');
    }
}

// Muestra mensajes informativos o de error en el encabezado de la tabla
function mostrarMensaje(texto, esError = false) {
    mensajeEstado.textContent = texto;
    mensajeEstado.classList.toggle('error', esError);
}

// Convierte texto externo en contenido seguro antes de insertarlo
function escaparHtml(texto) {
    const elemento = document.createElement('div');
    elemento.textContent = String(texto);
    return elemento.innerHTML;
}

// Reinicia el contador a 60 segundos y actualiza la interfaz
function reiniciarContador() {
    contador = 60;
    segundosRestantes.textContent = contador;
}

// Si el contador llega a cero y no hay un escaneo en curso, se ejecuta un escaneo automático
setInterval(() => {
    if (escaneoEnCurso) return;

    contador -= 1;
    segundosRestantes.textContent = contador;

    if (contador <= 0) {
        ejecutarEscaneo(true);
    }
}, 1000);
