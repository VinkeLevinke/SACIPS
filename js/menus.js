
/* MODALES */



/* modal para perfil */

function abrirModalPerfil() {
    let modal = document.getElementById('abrirModalPerfil');
    modal.style.display = 'block';
    setTimeout(function () {
        modal.classList.add('show');
    }, 10); // Pequeño retraso para permitir que el navegador registre el cambio de display
}


function cerrarModalPerfil() {
    let modal = document.getElementById('abrirModalPerfil');
    modal.classList.remove('show');
    modal.classList.add('hide');
    setTimeout(function () {
        modal.style.display = 'none';
        modal.classList.remove('hide');
    }, 300); // Tiempo de la transición para cerrar el modal

}


function modalUpdate(campo, valor, apellido = '') {
    let modal = document.getElementById('formActualizar');
    let label = document.getElementById('labelActualizar');
    let input = document.getElementById('inputActualizar');
    let apellidoGroup = document.getElementById('apellidoGroup');
    let inputApellido = document.getElementById('inputApellido');

    label.textContent = 'Actualizar ' + campo;
    input.value = valor;

    if (campo === 'Nombre') {
        apellidoGroup.style.display = 'flex';
        inputApellido.value = apellido;
    } else {
        apellidoGroup.style.display = 'none';
    }

    modal.style.display = 'block';
    setTimeout(function () {
        modal.classList.add('show');
    }, 10); // Pequeño retraso para permitir que el navegador registre el cambio de display
}

function cerrarModalActualizar() {
    let modal = document.getElementById('formActualizar');
    modal.classList.remove('show');
    modal.classList.add('hide');
    setTimeout(function () {
        modal.style.display = 'none';
        modal.classList.remove('hide');
    }, 300); // Tiempo de la transición para cerrar el modal
}



function guardarCambios() {
    let id_persona = document.getElementById('inputId-persona').value;
    let nuevoValor = document.getElementById('inputActualizar').value;
    let tipoCampo = document.getElementById('labelActualizar').textContent.replace('Actualizar ', '');
    let apellido = tipoCampo === 'Nombre' ? document.getElementById('inputApellido').value : null;

    let xhr = new XMLHttpRequest();
    xhr.open('POST', './componentes/cnjVigilancia/actualizar_datos.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('Respuesta del servidor:', xhr.responseText);
            if (xhr.responseText.includes("Datos actualizados correctamente")) {
                if (tipoCampo === 'Nombre') {
                    document.getElementById('nombre').textContent = nuevoValor + ' ' + apellido;
                } else if (tipoCampo === 'Cédula') {
                    document.getElementById('cedula').textContent = nuevoValor;
                } else if (tipoCampo === 'Correo') {
                    document.getElementById('correo').textContent = nuevoValor;
                } else if (tipoCampo === 'Telefono') {
                    document.getElementById('telefono').textContent = nuevoValor;
                }
                cerrarModalActualizar();
            } else {
                console.error('Error al actualizar los datos');
            }
        }
    };

    let postData = 'id_persona=' + encodeURIComponent(id_persona) + '&tipoCampo=' + encodeURIComponent(tipoCampo) + '&nuevoValor=' + encodeURIComponent(nuevoValor);
    if (apellido !== null) {
        postData += '&apellido=' + encodeURIComponent(apellido);
    }
    xhr.send(postData);
}


function cerrarSesion() {
    // Borrar todas las cookies
    document.cookie.split(";").forEach(function (c) {
        document.cookie = c.trim().split("=")[0] + "=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/";
    });

    // Borrar el almacenamiento local
    localStorage.clear();

    // Crear un formulario oculto para enviar la solicitud de cierre de sesión
    let form = document.createElement('form');
    form.method = 'POST';
    form.action = './componentes/cnjVigilancia/vigilanciaLogout.php';

    // Añadir el formulario al cuerpo del documento y enviarlo
    document.body.appendChild(form);
    form.submit();
}


function abrirModalCambiarClave() {
    let modal = document.getElementById('modalCambiarClave');
    modal.style.display = 'block';
    setTimeout(function () {
        modal.classList.add('show');
    }, 10);
}

function abrirModalReciboPago(id, monto, concepto, fecha, realizadoPor, tipoRecibo) {
    // Suponiendo que 'fecha' incluye la fecha y la hora en el formato correcto
    let [fechaPart, horaPart] = fecha.split(' '); // Divide la fecha y la hora

    document.getElementById('reciboID').textContent = id;
    document.getElementById('reciboMontoPago').textContent = monto;
    document.getElementById('reciboConceptoPago').textContent = concepto;
    document.getElementById('reciboFechaPago').textContent = fechaPart;
    document.getElementById('reciboHoraPago').textContent = horaPart;
    document.getElementById('reciboRealizadoPorPago').textContent = realizadoPor;

    // Establecer el valor del tipo de recibo en el select
    let tipoReciboElement = document.getElementById('tipoRecibo');
    if (tipoReciboElement) {
        if (tipoRecibo === 'egreso') {
            tipoReciboElement.value = 'egreso'; // Establecer "Egreso" en el select
        } else {
            switch (tipoRecibo) {
                case 'Donación':
                    tipoReciboElement.value = 'aporte_donacion';
                    break;
                case 'Aporte Patronal':
                    tipoReciboElement.value = 'aporte_patronal';
                    break;
                case 'Estatuto':
                    tipoReciboElement.value = 'aporte_afiliado';
                    break;
                default:
                    tipoReciboElement.value = ''; // Por si acaso no coincide ningún caso
            }
        }
    } else {
        console.error('El elemento tipoRecibo no se encontró en el DOM');
    }

    let modal = document.getElementById('modalReciboPago');
    modal.style.display = 'block';
    setTimeout(function () {
        modal.classList.add('show');
    }, 10);
}






function cerrarModalReciboPago() {
    let modal = document.getElementById('modalReciboPago');
    modal.classList.remove('show');
    modal.classList.add('hide');
    setTimeout(function () {
        modal.style.display = 'none';
        modal.classList.remove('hide');
    }, 300);
}


function cerrarModalCambiarClave() {
    let modal = document.getElementById('modalCambiarClave');
    modal.classList.remove('show');
    modal.classList.add('hide');
    setTimeout(function () {
        modal.style.display = 'none';
        modal.classList.remove('hide');
    }, 300);
}


function guardarComentarioPago() {
    /*//===Activacion Del Modal De Carga(AFILIADOS)===//*/
    let Loader = document.getElementById('modal_loader');
    Loader.style.display = 'flex';
    /*//===Fin Del Modal DE Carga ☝🤓===//*/
    let idReciboElement = document.getElementById('reciboID');
    let comentarioElement = document.getElementById('comentarioVigilantePago');
    let tipoReciboElement = document.getElementById('tipoRecibo');

    if (!idReciboElement || !comentarioElement || !tipoReciboElement) {
        console.error('Uno o más elementos no se encontraron en el DOM');
        return;
    }

    let idRecibo = idReciboElement.textContent;
    let comentario = comentarioElement.value;
    let tipoRecibo = tipoReciboElement.value;

    let xhr = new XMLHttpRequest();
    xhr.open('POST', './componentes/cnjVigilancia/guardar_comentario.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert('Comentario guardado correctamente');
            //Esto es para ver si funciona B)
            Loader.style.display = 'none';
            cerrarModalReciboPago();
        }
    };
    let postData = 'id_recibo=' + encodeURIComponent(idRecibo) + '&comentario=' + encodeURIComponent(comentario) + '&tipo_recibo=' + encodeURIComponent(tipoRecibo);
    xhr.send(postData);
}

function guardarCambioClave() {
    let claveActual = document.getElementById('claveActual').value;
    let claveNueva = document.getElementById('claveNueva').value;
    let confirmarClave = document.getElementById('confirmarClave').value;

    // Validar que las contraseñas nuevas coincidan
    if (claveNueva !== confirmarClave) {
        alert("Las claves nuevas no coinciden!");
        return;
    }

    let xhr = new XMLHttpRequest();
    xhr.open('POST', './componentes/cnjVigilancia/cambiar_clave.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let respuesta = JSON.parse(xhr.responseText);
            alert(respuesta.message); // Mostrar el mensaje de respuesta
            if (respuesta.success) {
                cerrarModalCambiarClave();
            }
        }
    };

    // Enviar solo las contraseñas sin encriptar al servidor
    let postData = 'claveActual=' + encodeURIComponent(claveActual) + '&claveNueva=' + encodeURIComponent(claveNueva);
    xhr.send(postData);
}




/* MODALES */


