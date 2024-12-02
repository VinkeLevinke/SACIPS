// Espera a que el DOM se cargue completamente
document.addEventListener("DOMContentLoaded", function() {
    // Obtiene referencias a los elementos del DOM que se usarán
    const cedulaForm = document.getElementById('cedulaForm');
    const cedulaInput = document.getElementById('cedula');
    const seccion2 = document.getElementById('seccion2');
    const seccionPreguntas = document.getElementById('seccionPreguntas');
    const seccionClave = document.getElementById('seccionClave');
    const usnm = document.getElementById('usnm');
    const inicio = document.getElementById('inicio');
    const modal = document.getElementById('modalApi');
    const alertaRecuperacion = document.querySelector('.alt-recuperacion');
    const modalCloseApi = document.getElementById('modalCloseApi');
    const preguntaTexto = document.getElementById('preguntaTexto');
    const intentosRestantes = document.getElementById('intentosRestantes');
    const btnClave = document.getElementById('btn-clve');
    const btnUsnm = document.getElementById('btn-usnm');

    

    // Inicializa variables necesarias para el funcionamiento
    let metodoSeleccionado = '';
    let intentos = 3;
    let preguntasDeSeguridad = [];
    let respuestasDeSeguridad = [];
    let preguntaActual = '';
    let preguntaIndex = '';
    let respuestaCorrecta = '';



      // Función para mostrar la sección actual y actualizar la leyenda y la barra de carga
      function mostrarSeccion(seccionID, leyenda) {

        document.querySelectorAll('.form-section').forEach(sec => sec.style.display = 'none'); // Oculta todas las secciones
        document.getElementById(seccionID).style.display = 'block'; // Muestra la sección de interés
        document.getElementById('seccion-leyenda').innerText = `Sección: ${leyenda}`; // Actualiza la leyenda

        // Actualiza la barra de progreso
        const sections = ['inicio', 'seccion2', 'seccionPreguntas', 'seccionClave', 'usnm'];
        const currentIndex = sections.indexOf(seccionID);
        const progressPercentage = ((currentIndex + 1) / sections.length) * 100; // Calcula el porcentaje de avance
        document.querySelector('.progress-bar').style.width = `${progressPercentage}%`; // Ajusta el ancho de la barra
    }



    // Maneja el evento de envío del formulario de cédula
    if (cedulaForm) {
        cedulaForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el comportamiento por defecto del formulario
            if (cedulaInput.value.trim() === '') {
                mostrarModal("Por favor, ingrese su cédula."); // Muestra modal si la cédula está vacía
                return;
            }

            mostrarCargando(true); // Mostrar carga al inicio

            const formData = new FormData(cedulaForm);
            fetch('./componentes/conexiones/validar_cedula.php', {
                method: 'POST',
                body: formData
            }).then(response => response.text()).then(data => {
                const parts = data.split(";"); // Divide la respuesta del servidor
                mostrarCargando(false); // Cerrar carga al completar la llamada

                if (parts[0].trim() === "true") {
                    // Si la cédula es válida, muestra la siguiente sección
                    mostrarSeccion('seccion2', 'Seleccione Método de Recuperación');
                    inicio.style.display = 'none';
                    seccion2.style.display = 'block';
                    preguntasDeSeguridad = parts.slice(1, 4); // Recoge las preguntas de seguridad
                    respuestasDeSeguridad = parts.slice(4); // Recoge las respuestas de seguridad
                } else {
                    mostrarModal("Cédula no Registrada."); // Muestra un error si la cédula no es válida
                }
            }).catch(error => {
                mostrarCargando(false); // Cerrar carga en caso de error
                mostrarModal("Error al verificar cédula."); // Notifica el error
            });
        });
    }
    

    // Maneja el evento de cerrar el modal
    if (modalCloseApi) {
        modalCloseApi.addEventListener('click', function() {
            ocultarModal(); // Oculta el modal cuando se hace clic en cerrar
        });
    }

    // Función para volver al inicio de la aplicación
    window.volverAlInicio = function() {
        inicio.style.display = 'block'; // Muestra la sección de inicio
        seccion2.style.display = 'none'; // Oculta la sección 2
        seccionPreguntas.style.display = 'none'; // Oculta preguntas
        seccionClave.style.display = 'none'; // Oculta sección de clave
        usnm.style.display = 'none'; // Oculta sección de nombre de usuario


        mostrarCargando(true); // Mostrar carga al inicio
        setTimeout(() => {
    
            mostrarCargando(false); // Oculta la carga
        }, 950);
        mostrarSeccion('inicio', 'Seleccione una cedula');
        // Limpia todos los campos de entrada
        cedulaInput.value = '';
        document.getElementById('respuestas').value = '';
        document.getElementById('clave1').value = '';
        document.getElementById('conclave2').value = '';
        document.getElementById('nuevo_usuario').value = '';
        document.getElementById('confirmar_usuario').value = '';

        // Reinicia los intentos
        intentos = 3;
        intentosRestantes.innerHTML = '';
        preguntaTexto.innerHTML = '';
        ocultarModal(); // Oculta el modal si está abierto
    };

    // Función para procesar la selección del método de recuperación
    window.procesarSeleccion = function() {
        const metodoSelect = document.getElementById('metodoRecuperacion');
        metodoSeleccionado = metodoSelect.value; // Obtiene el método de recuperación seleccionado
        if (metodoSeleccionado === "clave" || metodoSeleccionado === "nombre_usuario") {
            mostrarCargando(true); // Muestra la carga mientras se procesa
            mostrarSeccion('seccionPreguntas', 'Preguntas de Seguridad');
            setTimeout(() => {
                seccion2.style.display = 'none'; // Oculta sección 2
                seccionPreguntas.style.display = 'block'; // Muestra preguntas
                mostrarPreguntaAleatoria(); // Muestra una pregunta aleatoria
                intentosRestantes.innerHTML = `Intentos restantes: ${intentos}`; // Muestra intentos restantes
                mostrarCargando(false); // Oculta la carga
            }, 1500);
        } else {
            mostrarModal("Por favor, seleccione un método de recuperación."); // Solicita seleccionar un método
        }
    };

    // Función para mostrar una pregunta aleatoria de seguridad
    function mostrarPreguntaAleatoria() {
        if (preguntasDeSeguridad.length > 0) {
            preguntaIndex = Math.floor(Math.random() * preguntasDeSeguridad.length); // Selecciona un índice aleatorio
            preguntaActual = preguntasDeSeguridad[preguntaIndex]; // Obtiene la pregunta correspondiente
            preguntaTexto.innerHTML = preguntaActual; // Muestra la pregunta en el DOM
            respuestaCorrecta = respuestasDeSeguridad[preguntaIndex]; // Guarda la respuesta correcta
        }
    }

    // Función para validar las preguntas de seguridad
    window.validarPreguntas = function() {
        const respuesta = document.getElementById('respuestas').value; // Obtiene la respuesta ingresada
        const formData = new FormData();
        if (respuesta.trim() === '') {
            mostrarModal("Por favor, ingrese una respuesta."); // Notifica si no se ingresó respuesta
            return;
        }
        formData.append('respuesta', respuesta); // Agrega la respuesta al FormData
        formData.append('preguntaIndex', preguntaIndex); // Agrega el índice de la pregunta
        formData.append('cedula', cedulaInput.value); // Agrega la cédula
        formData.append('metodo', metodoSeleccionado); // Agrega el método seleccionado
        
        if (respuesta.trim().toLowerCase() === respuestaCorrecta.toLowerCase()) {
            // Si la respuesta es correcta, verifica el método para continuar
            if (metodoSeleccionado === "clave") {
                seccionPreguntas.style.display = 'none'; // Oculta preguntas
                seccionClave.style.display = 'block'; // Muestra sección de clave
                mostrarSeccion('seccionClave', 'Establecer Nueva Clave');
            } else if (metodoSeleccionado === "nombre_usuario") {
                seccionPreguntas.style.display = 'none'; // Oculta preguntas
                usnm.style.display = 'block'; // Muestra sección de nombre de usuario
                mostrarSeccion('usnm', 'Establecer Nuevo Nombre de Usuario');
            }
        } else {
            // Si la respuesta es incorrecta, decrementa los intentos
            intentos--;
            intentosRestantes.innerHTML = `Intentos restantes: ${intentos}`;
            if (intentos <= 0) {
                volverAlInicio(); // Si no hay intentos restantes, vuelve al inicio
                mostrarModal("Por motivos de seguridad, por favor vuelva a empezar");
            } else {
                mostrarModal("Respuesta incorrecta. Por favor, intente de nuevo."); // Notifica respuesta incorrecta
                mostrarPreguntaAleatoria(); // Muestra otra pregunta aleatoria
            }
        }
    };

    // Maneja el evento del botón de clave
    btnClave.addEventListener('click', function(event) {
        event.preventDefault(); // Evita el comportamiento por defecto
        const nuevaClave = document.getElementById('clave1').value; // Obtiene la nueva clave
        const confirmarClave = document.getElementById('conclave2').value; // Obtiene la confirmación de clave
        const respuesta = document.getElementById('respuestas').value; // Obtiene la respuesta a la pregunta de seguridad
        if (nuevaClave.trim() === '' || confirmarClave.trim() === '' || respuesta.trim() === '') {
            mostrarModal("Por favor, complete todos los campos."); // Notifica campos vacíos
            return;
        }
        if (nuevaClave !== confirmarClave) {
            mostrarModal("Las claves no coinciden. Por favor, inténtelo de nuevo."); // Notifica si las claves no coinciden
            return;
        }
        const formData = new FormData(); // Prepara datos para enviar
        formData.append('cedula', cedulaInput.value); // Agrega la cédula
        formData.append('metodo', 'clave'); // Define el método como clave
        formData.append('nueva_clave', nuevaClave); // Agrega la nueva clave
        formData.append('respuesta', respuesta); // Agrega la respuesta
        formData.append('preguntaIndex', preguntaIndex); // Agrega el índice de la pregunta
        mostrarCargando(true); // Muestra carga al enviar solicitud
        fetch('./componentes/conexiones/validar_cedula.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text()).then(data => {
            mostrarCargando(false); // Oculta carga tras recibir respuesta
            if (data === "clave_actualizada") {
                mostrarModal("Clave actualizada correctamente."); // Notifica que la clave fue actualizada
                setTimeout(function() {
                    ocultarModal(); // Oculta el modal tras unos segundos
                    window.location.href = 'index.php'; // Redirige a la página de inicio
                }, 3000);
            } else {
                mostrarModal("Error al actualizar la clave: " + data); // Notifica error al actualizar
            }
        }).catch(error => {
            console.error('Error:', error);
            mostrarModal("Error al procesar la solicitud."); // Notifica si hubo error en la solicitud
            mostrarCargando(false); // Oculta carga
        });
    });


    // Maneja el evento del botón de nombre de usuario
    btnUsnm.addEventListener('click', function(event) {
        event.preventDefault(); // Evita el comportamiento por defecto
        const nuevoUsuario = document.getElementById('nuevo_usuario').value; // Obtiene el nuevo nombre de usuario
        const confirmarUsuario = document.getElementById('confirmar_usuario').value; // Obtiene la confirmación
        const respuesta = document.getElementById('respuestas').value; // Obtiene la respuesta a la pregunta de seguridad
        if (nuevoUsuario.trim() === '' || confirmarUsuario.trim() === '' || respuesta.trim() === '') {
            mostrarModal("Por favor, complete todos los campos."); // Notifica campos vacíos
            return;
        }
        if (nuevoUsuario !== confirmarUsuario) {
            mostrarModal("Los nombres de usuario no coinciden. Por favor, inténtelo de nuevo."); // Notifica si no coinciden
            return;
        }
        const formData = new FormData(); // Prepara datos para enviar
        formData.append('cedula', cedulaInput.value); // Agrega la cédula
        formData.append('metodo', 'nombre_usuario'); // Define el método como nombre de usuario
        formData.append('nuevo_usuario', nuevoUsuario); // Agrega el nuevo nombre de usuario
        formData.append('respuesta', respuesta); // Agrega la respuesta a la pregunta de seguridad
        formData.append('preguntaIndex', preguntaIndex); // Agrega el índice de la pregunta
        mostrarCargando(true); // Muestra carga al enviar solicitud
        fetch('./componentes/conexiones/validar_cedula.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text()).then(data => {
            mostrarCargando(false); // Oculta carga tras recibir respuesta
            if (data.includes("nombre_usuario_actualizado")) {
              
               
                setTimeout(function() {
                 
                   
                    mostrarCargando(false); // Muestra carga durante la redirección
                    mostrarModal("Nombre de usuario actualizado correctamente"); // Notifica éxito
                    setTimeout(function() {
                 
                        window.location.href = 'index.php'; // Redirige a la página de inicio
                       
                    }, 500);
                }, 2000);   mostrarCargando(true); // Muestra carga durante la redirección
              

                
            
            } else if (data.includes("respuesta_incorrecta")) {
                mostrarModal("La respuesta de la pregunta de seguridad es incorrecta. Inténtalo de nuevo."); // Notifica respuesta incorrecta
            } else {
                mostrarModal("Error al actualizar nombre de usuario: " + data); // Notifica error al actualizar
            }
        }).catch(error => {
            console.error('Error:', error);
            mostrarModal("Error al procesar la solicitud."); // Notifica si hubo error en la solicitud
            mostrarCargando(false); // Oculta carga
        });
    });

    // Función para mostrar un modal con un mensaje
    function mostrarModal(mensaje) {
        alertaRecuperacion.innerHTML = mensaje; // Establece el mensaje en el modal
        modal.style.display = 'block'; // Muestra el modal
        setTimeout(function() {
            modal.classList.add('show'); // Agrega clase para la animación de entrada
        }, 10);
    }

    // Función para ocultar el modal
    function ocultarModal() {
        modal.classList.remove('show'); // Remueve clase para animación
        setTimeout(function() {
            modal.style.display = 'none'; // Oculta el modal
        }, 300);
    }

    
    // Función para mostrar un indicador de carga
    function mostrarCargando(estado) {
        const loadingElement = document.getElementById('loading');
        loadingElement.style.display = estado ? 'flex' : 'none'; // Muestra/oculta cargando
        const modalLoading = document.getElementById('modalLoading');
        modalLoading.style.display = estado ? 'block' : 'none'; // Muestra/oculta modal de carga
    }

    // Función para manejar clics en el modal
    window.onclick = function(event) {
        if (event.target === modal) {
            ocultarModal(); // Oculta el modal si se clickea en él
        }
    }

    // Función para manejar la tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            ocultarModal(); // Oculta el modal si se presiona Escape
        }
    });

    
});

// ABRIR DROWPDOWN
// Función para procesar la selección del método de recuperación desde un dropdown
function procesarSeleccion() {
    const metodoSelect = document.getElementById('metodoRecuperacion');
    const metodoSeleccionado = metodoSelect.value; // Obtiene el método seleccionado
    if (metodoSeleccionado === "clave") {
        seccionClave.style.display = 'block'; // Muestra sección de clave
        seccionPreguntas.style.display = 'none'; // Oculta preguntas
        usnm.style.display = 'none'; // Oculta nombre de usuario
    } else if (metodoSeleccionado === "nombre_usuario") {
        usnm.style.display = 'block'; // Muestra sección de nombre de usuario
        seccionClave.style.display = 'none'; // Oculta sección de clave
        seccionPreguntas.style.display = 'none'; // Oculta preguntas
    } else {
        alert("Por favor, seleccione un método de recuperación."); // Solicita seleccionar un método
    }
}


