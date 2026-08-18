// Filtra los hoteles y las actividades según el destino elegido
document.addEventListener('DOMContentLoaded', () => {
    const destino = document.querySelector('#reserva-destino');
    const hotel = document.querySelector('#reserva-hotel');
    const actividad = document.querySelector('#reserva-actividad');

    if (!destino || !hotel || !actividad) {
        return;
    }

    const filtrarOpciones = (select, idDestino, conservarPrimera, valorInicial = '') => {
        Array.from(select.options).forEach((opcion, indice) => {
            if (conservarPrimera && indice === 0) {
                opcion.hidden = false;
                return;
            }

            opcion.hidden = opcion.dataset.destino !== idDestino;
        });

        select.disabled = idDestino === '';

        if (valorInicial !== '') {
            select.value = valorInicial;
        } else {
            select.selectedIndex = 0;
        }
    };

    destino.addEventListener('change', () => {
        const idDestino = destino.value;

        filtrarOpciones(hotel, idDestino, true);
        filtrarOpciones(actividad, idDestino, true);

        hotel.options[0].textContent = idDestino
            ? 'Seleccione un hotel'
            : 'Seleccione primero un destino';
    });

    if (destino.value !== '') {
        const hotelInicial = hotel.value;
        const actividadInicial = actividad.value;

        filtrarOpciones(hotel, destino.value, true, hotelInicial);
        filtrarOpciones(actividad, destino.value, true, actividadInicial);
        hotel.options[0].textContent = 'Seleccione un hotel';
    }
});
