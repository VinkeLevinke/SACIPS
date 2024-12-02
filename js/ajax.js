/// Función expandirMenu con promesas
function expandirMenu(header) {
  const barraLateral = header.closest('.barra-lateral');
  const blBody = barraLateral.querySelector('.bl-body');

  // Abre o cierra la barra lateral
  return new Promise((resolve) => {
    if (barraLateral.classList.contains('expanded')) {
      // Cierra la barra lateral
      barraLateral.classList.remove('expanded');
      blBody.style.height = '0';
      blBody.style.opacity = '0';
      setTimeout(() => {
        blBody.style.visibility = 'hidden'; // Oculta la sección de texto después de la transición
        resolve(true);
      }, 200); // Debe coincidir con la duración de la transición CSS
    } else {
      // Abre la barra lateral
      barraLateral.classList.add('expanded');
      blBody.style.visibility = 'visible';
      requestAnimationFrame(() => {
        blBody.style.height = 'auto';
        const height = blBody.offsetHeight + 'px';
        blBody.style.height = '0';
        blBody.offsetHeight; // Forzar la reflujo
        blBody.style.height = height;
        blBody.style.opacity = '1';
        setTimeout(() => {
          resolve(true);
        }, 200); // Espera a que se complete la animación
      });
    }
  });
}

// 1)  ---- GILBER PARTE DE USUARIOS EN ADMINISTRADOR EDITAR LOS DATOS DE USUARIO EN admCONFIG ---- //
function enviar_form() {
  const form_edit = document.getElementById("edit_submit");
  form_edit.submit();
}

//=======//Variables Globales//========//

var IdUsuarioEdit = '';

// 2) /*------- admins aprueban el cambio---------------*/
document.addEventListener("DOMContentLoaded", function () {
  // Definir la función en el ámbito global
  window.manejarSolicitud = function (idSolicitud, accion) {
    let xhr = new XMLHttpRequest();
    xhr.open(
      "POST",
      "./componentes/administrador/solicitudes/manejar_solicitud.php",
      true
    );
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        alert(xhr.responseText);
        location.reload();
      }
    };

    xhr.send(
      "idSolicitud=" +
      encodeURIComponent(idSolicitud) +
      "&accion=" +
      encodeURIComponent(accion)
    );
  };
});



/* -- telefono -- */

document.addEventListener("DOMContentLoaded", function () {
  // Definir la función en el ámbito global
  window.manejarSolicitud_tf = function (idSolicitud, accion) {
    let xhr = new XMLHttpRequest();
    xhr.open(
      "POST",
      "./componentes/administrador/solicitudes/manejar_solicitud_telefono.php",
      true
    );
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        alert(xhr.responseText);
        location.reload();
      }
    };

    xhr.send(
      "idSolicitud=" +
      encodeURIComponent(idSolicitud) +
      "&accion=" +
      encodeURIComponent(accion)
    );
  };
});




document.addEventListener("DOMContentLoaded", function () {
  // Definir la función en el ámbito global
  window.manejarSolicitud_egreso = function (idSolicitud, accion) {
    let xhr = new XMLHttpRequest();
    xhr.open(
      "POST",
      "./componentes/administrador/solicitudes/egreso/egreso_modal.php",
      true
    );
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        alert(xhr.responseText);
        location.reload();
      }
    };

    xhr.send(
      "idSolicitud=" +
      encodeURIComponent(idSolicitud) +
      "&accion=" +
      encodeURIComponent(accion)
    );
  };
});
/*---------------- FIN SOLI ADMIN APROBAR ------------------- */


//-============---Formulario por partes---===============//  

let pagina = 0;

function nextSection() {


  //==/Aqui Verifico en que pagina estamos/==//

  for (i = 1; i <= 8; i++) {
    if (document.querySelector('#seccion' + i).classList.contains('active') === true) {
      pagina = i;
    }
  }



  //==========================================================//

  if (pagina != 9) {

    let paginaButton = document.querySelectorAll('.pgn_button');

    paginaButton.forEach((boton, paginaActual) => {
      if (paginaActual - 2 === pagina) {
        boton.classList.add('PaginaActiva');
      } else {
        boton.classList.remove('PaginaActiva');
      }

    });

    const currentSection = document.querySelector('.registro-seccion.active');
    const nextSection = currentSection.nextElementSibling;

    if (currentSection) {
      currentSection.classList.add('slide-out'); // Desliza hacia afuera  
      setTimeout(() => {
        currentSection.classList.remove('active', 'slide-out'); // Oculta la sección  
        if (nextSection) {
          nextSection.classList.add('active'); // Muestra la siguiente  
        }
      }, 400); // El tiempo debe coincidir con la duración de la transición  
    }
  }
}

function prevSection() {

  //==/Aqui Verifico en que pagina estamos/==//

  for (i = 1; i <= 8; i++) {
    if (document.querySelector('#seccion' + i).classList.contains('active') === true) {
      pagina = i;
    }
  }

  //==========================================================//

  if (pagina != 1) {

    let paginaButton = document.querySelectorAll('.pgn_button');

    paginaButton.forEach((boton, paginaActual) => {
      if (paginaActual === pagina) {
        boton.classList.add('PaginaActiva');
      } else {
        boton.classList.remove('PaginaActiva');
      }

    });

    const currentSection = document.querySelector('.registro-seccion.active');
    const prevSection = currentSection.previousElementSibling;

    if (currentSection) {
      currentSection.classList.add('slide-out'); // Desliza hacia afuera  
      setTimeout(() => {
        currentSection.classList.remove('active', 'slide-out'); // Oculta la sección  
        if (prevSection) {
          prevSection.classList.add('active'); // Muestra la anterior  
        }
      }, 400); // El tiempo debe coincidir con la duración de la transición  
    }

  }
}
// ------------------------------------------ //

//===//Ahora la Funcion del Paginado//===//

function Paginado(paginaSelec) {

  const currentSection = document.querySelector('.registro-seccion.active');

  let paginaButton = document.querySelectorAll('.pgn_button');

  // Epa Mañana agrego lo demas webo es que

  if (paginaSelec != 'ultima' && paginaSelec != 'primera') {
    paginaButton.forEach((boton, paginaActual) => {
      if (paginaActual - 1 == paginaSelec) {
        boton.classList.add('PaginaActiva');
        if (currentSection) {
          currentSection.classList.add('slide-out'); // Desliza hacia afuera  
          setTimeout(() => {
            currentSection.classList.remove('active', 'slide-out'); // Oculta la sección  
            document.getElementById('seccion' + paginaSelec).classList.add('active');
          }, 400); // El tiempo debe coincidir con la duración de la transición  
        }
      } else {
        boton.classList.remove('PaginaActiva');
      }

    });
  } else if (paginaSelec == 'ultima') {

    paginaButton.forEach((boton, paginaActual) => {
      if (paginaActual - 1 == 7) {
        boton.classList.add('PaginaActiva');
        if (currentSection) {
          currentSection.classList.add('slide-out'); // Desliza hacia afuera  
          setTimeout(() => {
            currentSection.classList.remove('active', 'slide-out'); // Oculta la sección  
            document.getElementById('seccion7').classList.add('active');
          }, 400); // El tiempo debe coincidir con la duración de la transición  
        }
      } else {
        boton.classList.remove('PaginaActiva');
      }

    });
  } else if (paginaSelec == 'primera') {
    paginaButton.forEach((boton, paginaActual) => {
      if (paginaActual - 1 == 1) {
        boton.classList.add('PaginaActiva');
        if (currentSection) {
          currentSection.classList.add('slide-out'); // Desliza hacia afuera  
          setTimeout(() => {
            currentSection.classList.remove('active', 'slide-out'); // Oculta la sección  
            document.getElementById('seccion1').classList.add('active');
          }, 400); // El tiempo debe coincidir con la duración de la transición  
        }
      } else {
        boton.classList.remove('PaginaActiva');
      }

    });
  }


}


//=======================================//
//=======Filtrado intelillente B) gilber=====//
function filtrarPorRango() {
  // Obtener las fechas de los inputs
  let fechaInicio = new Date(document.getElementById('inicio').value);
  let fechaFin = new Date(document.getElementById('fin').value);

  // Asegurarse de que las fechas sean válidas
  if (isNaN(fechaInicio) || isNaN(fechaFin)) {
    alert('Por favor, ingrese fechas válidas.');
    return;
  }

  // Asegurarse de que fechaFin es mayor o igual que fechaInicio
  if (fechaFin < fechaInicio) {
    alert('La fecha de fin debe ser mayor o igual que la fecha de inicio.');
    return;
  }

  // Seleccionar todas las filas de la tabla
  let selecTabla = document.querySelectorAll('.tableMovsGeneral tbody tr');

  selecTabla.forEach(fila => {
    // Seleccionar todas las celdas (td) dentro de la fila
    let celdas = fila.querySelectorAll('td');

    // Comprobar si hay al menos cuatro celdas
    if (celdas.length >= 4) {
      // Tomar la cuarta celda (índice 3 porque los índices comienzan en 0)
      let terceraCelda = celdas[3];
      console.log(celdas[0].textContent.trim());
      let monto = 0;
      monto['ingreso'] = 0;
      monto['egreso'] = 0;

      if (celdas[0].textContent.trim() == 'Donación') {
        console.log('sibro');
        calc = celdas[2].textContent.trim();
        console.log(calc);
        //monto['ingreso'] +=;
      }
      // Obtener la fecha de la celda y convertirla a un objeto Date
      let fecha = terceraCelda.textContent.trim();
      let hora = '';
      [fecha, hora] = fecha.split(' ');
      console.log(fecha);

      let [dia, mes, ano] = fecha.split('/');
      let fechaCelda = new Date(`${ano}-${mes}-${dia}T${hora}`);

      // Comparar la fecha de la celda con el rango
      if (fechaCelda >= fechaInicio && fechaCelda <= fechaFin) {
        fila.style.display = '';
      } else {
        fila.style.display = 'none';
      }
    } else {
      // Si la fila no tiene suficientes celdas, la oculta
      fila.style.display = 'none';
    }
  });
}




// --------------------------------------------//

// 4) /*------------- Funcion de AJAX -------------- */


function ajax(page, extension = "php") {
  const url = `${page}.${extension}`;

  $.ajax({
    type: "GET",
    url: url,
    success: function (response) {
      document.getElementById("af-container").innerHTML = response;

      // Llamar a la función de inicialización del formulario
      initializeForm();

      //////////////////////////////////////////--------------------------------------------------///////////
      $(document).ready(function () {
        (function () {
          // Variables definidas en un ámbito superior
          var cuentas = JSON.parse($('#cuentasData').val());
          var totalCuentas = parseInt($('#totalAccounts').val());
          var currentIndex = parseInt($('#currentAccountIndex').val());

          function updateNavigation() {
            $('#currentNavigation').text((currentIndex + 1) + '/' + totalCuentas);
          }

          function updateAccountView() {
            if (cuentas.length > 0) {
              var row = cuentas[currentIndex];

              // Primera parte de la animación
              $('#bankAccount').addClass('fade-out');

              setTimeout(function () {
                $('#bankAccount').html(`
                            <div class="textGroup">
                                <input type="hidden" id="id_cuenta" value="${htmlspecialchars(row.id)}" readonly>
                                <label>Propietario de la Cuenta:</label>
                                <input type="text" id="propietarioCuenta" value="${htmlspecialchars(row.propietario_cuenta)}" readonly>
                                <label>Método de pago:</label>
                                <input type="text" id="metodoPago" value="${htmlspecialchars(row.nombre_tipo_operacion)}" readonly data-id="${row.formato_cuenta}">
                                <label>Nombre del Banco:</label>
                                <input type="text" id="nombreBanco" value="${htmlspecialchars(row.nombre_banco)}" readonly data-id="${row.id_banco}">
                                <label>Tipo de Cuenta:</label>
                                <input type="text" id="tipoCuenta" value="${htmlspecialchars(row.tipo_cuenta)}" readonly>
                                <label>Número de Cuenta:</label>
                                <input type="text" id="numeroCuenta" value="${htmlspecialchars(row.numero_cuenta)}" readonly>
                                <label>Cédula / RIF de la Cuenta:</label>
                                <input type="text" id="cedulaRif" value="${htmlspecialchars(row.cedula_rif)}" readonly>
                                <label>Teléfono de la Cuenta:</label>
                                <input type="text" id="telefonoCuenta" value="${htmlspecialchars(row.telefono_cuenta)}" readonly>
                                <label>Información Adicional:</label>
                                <input type="text" id="infoAdicional" value="${htmlspecialchars(row.informacion_adicional)}" readonly>
                            </div>
                            <div class="button-group">
                                <button type="button" class="button-update-account" onclick="abrirModal_actCuentaInstitucion()">Actualizar Cuenta</button>
                               <button type="button" class="button-delete-account" onclick="abrirModal_EliminarCuenta()">Eliminar Cuenta</button>

                            </div>
                        `);

                $('#bankAccount').removeClass('fade-out').addClass('fade-in');
                $('#currentAccountIndex').val(currentIndex);

                // Finalizar la animación
                setTimeout(function () {
                  $('#bankAccount').removeClass('fade-in');
                }, 300);

                // Actualiza la navegación
                updateNavigation();
              }, 300);
            }
          }

          function prevAccount() {
            if (currentIndex > 0) {
              currentIndex--;
              updateAccountView();
            }
          }

          function nextAccount() {
            if (currentIndex < totalCuentas - 1) {
              currentIndex++;
              updateAccountView();
            }
          }

          $('#prevAccount').on('click', function () {
            prevAccount();
          });

          $('#nextAccount').on('click', function () {
            nextAccount();
          });

          function htmlspecialchars(string) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(string));
            return div.innerHTML;
          }

          // Recursos iniciales
          updateAccountView();
          updateNavigation(); // Mostrar el conteo al inicio
        })();
      });






      /* cambiar cuentas bancarias en el menu de configuracion /*/
      /////////////////////////////////////
      //////////////////////////////////////////
      // 2) Aquí puedes incluir el código para la previsualización de la imagen y el envío de formularios...
      function handleImagePreview() {
        const inputImagen = document.getElementById("imagen");
        const imagenPrevia = document.getElementById("imagenPrevia");

        inputImagen.addEventListener("change", function (event) {
          const archivo = event.target.files[0];
          if (archivo) {
            const urlImagen = URL.createObjectURL(archivo);
            imagenPrevia.src = urlImagen;
            imagenPrevia.style.display = "block"; // Mostrar la previsualización
          }
        });
      }
      // Llamando a la función para cada formulario
      if (document.getElementById("comprobante_donacion")) {
        handleImagePreview("comprobante_donacion", "imagenPrevia_donacion"); // Aportes por Donación
      }

      if (document.getElementById("comprobante_patronal")) {
        handleImagePreview("comprobante_patronal", "imagenPrevia_patronal"); // Aportes Patronales
      }

      // Función para el envío del formulario
      function handleFormSubmission() {
        const form = document.getElementById("form");
        const InputImage = document.getElementById("imagen_perfil");

        InputImage.addEventListener("change", () => {
          // Envía automáticamente el formulario cuando se selecciona un archivo
          if (InputImage.files.length > 0) {
            form.submit();
          }
        });
      }

      // Verificación de la página actual
      if (document.getElementById("imagen")) {
        handleImagePreview(); // Estamos en afiliadosAportes.php
      }

      if (document.getElementById("imagen_perfil")) {
        handleFormSubmission(); // Estamos en admConfig.php
      }

      // ------------------------------------------------------------------------------------------------------//

      /***********************************BUSQUEDA PERZONALIZADA******************** */

      /* ** BANCO ** */
      $('#busquedaBanco').on('input', function () {
        var query = $('#busquedaBanco').val();
        var criterio = $('#criterio').val();

        if (query.length > 0) {
          $.ajax({
            type: 'POST',
            url: './componentes/administrador/solicitudes/banco/buscar_banco.php',
            data: {
              query: query,
              criterio: criterio
            },
            success: function (response) {
              $('#resultadoBusquedaBanco').html(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
              console.error('Error en la solicitud AJAX: ' + textStatus, errorThrown);
            }
          });
        } else {
          $('#resultadoBusquedaBanco').html('');
        }
      });

      /* ** BANCO FIN ** */

      /**************FIN BUSQUEDA PERSONALIZADA************** */

      //--------------------------------------------------------------------------------------------------//

      /*------ PREVENIR EL USO DEL ENTER Y QUE ESTE RECARGUE LA PAGINA EN LOS FORMULARIOS DE REPORTES-------*/
      /*-------------------- AGREGAR FORMULARIO NUEVO A LOS FORMULARIOS DE  EGRESOS Y APORTES CON UN MODAL--------------------- */

      document.getElementById('agregarNuevoEgreso').addEventListener('submit', function (event) {
        event.preventDefault(); // Evita que el formulario se envíe de la manera tradicional
        aggNewEgreso(); // Llama a la función para agregar el nuevo egreso
      });





      /* -------------------- FIN JS DE MOISES --------------------------------------- */
      // ------------------------------------------------------------------------------------------------------//

      // 3) --------------------CODIGO PARA ABRIR LA NOTIFICACIONES EN EL HEADER --------------------------------//

      // --------------------CODIGO PARA ABRIR LA NOTIFICACIONES EN EL HEADER FIN --------------------------------//
      // ------------------------------------------------------------------------------------------------------//


      //////////////////////////////

      /*----------------- MANDAR EL FORMULARIO PARA ADMINISTRADORES Y APROBAR EL CAMBIO DE CORREO ----------- */




      /* FIN SOLI */

      /////////////////////////////////////

      /* =========MODAL DE PERFILES AFILIADOS E INVITADOS=-====== */
      // Función para abrir el modal de correo

      /* enviar el formulario */

      //////////////////////////////////////////--------------------------------------------------///////////

      // 4)  Obtiene el elemento del cuerpo de la tabla
      const tableBody = document.getElementById("tableBody");
      tableBody.innerHTML = "";
      data.forEach((row) => {
        const tableRow = document.createElement("tr");
        tableRow.innerHTML = `
          <td>${row.tipo_aporte}</td>
          <td>${row.monto}</td>
          <td>${row.fechaAporte}</td>
          <td>${row.referencia}</td>
          <td>${row.referencia}</td>
          <td><button>Mostrar</button></td>
          <td><button>MRecibo</button></td>
        `;
        tableBody.appendChild(tableRow);
      });

      // El resto del código permanece igual...
    }
  });
} //---- AQUI ACABA LA FUNCION AJAX ----- //


////////////////////////////////////-----------------------////////////////////////////////////////////'
// 5) ENVIAR EL FORMULARIO DE DONACIONES Y APORTES PATRONALES-------//-//-//-//-//-//-//

document.addEventListener("DOMContentLoaded", initializeForm);

function initializeForm() {
  const tipoAporteSelect = document.getElementById("tipoAporte");
  const tipoOperacionSelect = document.getElementById("tipoOperacion");
  const contenedorBanco = document.getElementById("contenedorBanco");
  const formularioEgreso = document.getElementById("formularioEgreso");

  if (!formularioEgreso) return;

  const formElements = formularioEgreso.querySelectorAll(".br");
  const firmaElements = formularioEgreso.querySelectorAll(".firma");

  hideElements(formElements);
  hideElements(firmaElements);
  contenedorBanco.classList.add('hidden');

  if (tipoAporteSelect.value) {
    showElements(formElements);
    showElements(firmaElements);
  }

  tipoAporteSelect.addEventListener("change", function () {
    const selectedValue = this.value;

    // Ocultar elementos, asegurando que los demás están listos
    const formElements = formularioEgreso.querySelectorAll(".br");
    const firmaElements = document.querySelectorAll(".firma");

    hideElements(formElements);
    hideElements(firmaElements);

    // Cargar contenido según la selección
    setTimeout(() => {
      if (selectedValue) {
        // Añadir la lógica para cargar registraAportes_personas.php
        switch (selectedValue) {
          case "Aportes Patronales":
            loadContentWithAjax("./componentes/administrador/repAportesPatronales.php", selectedValue);
            break;
          case "Donación":
            loadContentWithAjax("./componentes/administrador/repDonaciones.php", selectedValue);
            break;
          case "Aportar por Personas":
            loadContentWithAjax("./componentes/administrador/registraAportes_personas.php", selectedValue);
            break;
          default:
            break;
        }
      }
    }, 300);
  });


  updateBancoVisibility();

  tipoOperacionSelect.addEventListener("change", updateBancoVisibility);
}

function hideElements(elements) {
  elements.forEach(element => {
    element.classList.remove('visible');
    element.classList.add('hidden');
  });
}

function showElements(elements) {
  elements.forEach(element => {
    setTimeout(() => {
      element.classList.remove('hidden');
      element.classList.add('visible');
    }, 10); // Breve retraso para activar la clase 'visible'
  });
}



function updateBancoVisibility() {
  const tipoOperacionSelect = document.getElementById("tipoOperacion");
  const contenedorBanco = document.getElementById("contenedorBanco");
  const codigoBanco = document.getElementById("codigoBanco");

  const selectedOption = tipoOperacionSelect.options[tipoOperacionSelect.selectedIndex];
  const tipoOperacionId = selectedOption.value;

  // Hacer una solicitud AJAX para obtener la categoría de pago
  fetch(`./componentes/administrador/solicitudes/metodo_pago/categoria-pago.php?id=${tipoOperacionId}`)
    .then(response => response.json())
    .then(data => {
      const categoriaPago = data.categoria_pago;

      const isVisible = categoriaPago === 'DIGITAL';

      contenedorBanco.style.display = isVisible ? "flex" : "none";
      codigoBanco.style.display = isVisible ? "flex" : "none";
    })
    .catch(error => console.error('Error al obtener la categoría de pago:', error));
}

function loadContentWithAjax(url, tipoAporte) {
  const container = document.getElementById("af-container");

  // Usa fade out para ocultar el contenido actual
  container.classList.add('hidden');

  fetch(url)
    .then(response => response.text())
    .then(html => {
      const tempDiv = document.createElement("div");
      tempDiv.innerHTML = html;

      setTimeout(() => {
        // Cambia el contenido HTML
        container.innerHTML = tempDiv.innerHTML;
        document.getElementById("tipoAporte").value = tipoAporte;

        const newFormElements = document.querySelectorAll(".br");
        const newFirmaElements = document.querySelectorAll(".firma");
        showElements(newFormElements);
        showElements(newFirmaElements);

        initializeForm();
      }, 500); // Tiempo para permitir que el fade out se complete
    })
    .catch(error => console.error("Error: No pudimos cargar el contenido:", error))
    .finally(() => {
      // Asegúrate de volver a mostrar el contenido después de que se complete la carga
      container.classList.remove('hidden');
    });
}



function updateCodigoBanco() {
  const bancoSelect = document.getElementById("banco");
  const nroCuentaInput = document.getElementById("nro_cuenta");
  const selectedOption = bancoSelect.options[bancoSelect.selectedIndex];

  if (selectedOption.value) {
    const bancoCodigo = selectedOption.value.split(" - ")[0];
    nroCuentaInput.value = bancoCodigo;
    nroCuentaInput.setAttribute('data-banco-codigo', bancoCodigo);
  } else {
    nroCuentaInput.value = "";
    nroCuentaInput.removeAttribute('data-banco-codigo');
  }
}

function validateAccountNumber() {

  const nroCuentaInput = document.getElementById("nro_cuenta");
  const bancoCodigo = nroCuentaInput.getAttribute('data-banco-codigo');
  const accountNumber = nroCuentaInput.value.slice(bancoCodigo.length);

  if (nroCuentaInput.value.slice(0, bancoCodigo.length) !== bancoCodigo) {
    nroCuentaInput.value = bancoCodigo + accountNumber;
  }

  if (accountNumber.length > 17) {
    nroCuentaInput.value = bancoCodigo + accountNumber.slice(0, 17);
  }

  // Actualizar el select del banco en tiempo real
  const bancoSelect = document.getElementById("banco");
  for (let i = 0; i < bancoSelect.options.length; i++) {
    if (bancoSelect.options[i].value.startsWith(bancoCodigo)) {
      bancoSelect.selectedIndex = i;
      break;
    }
  }
}





//////
//////---------------fin form de aportes patronales y donaciones------------------------//
let id_usuario_edit = 0;
let permisos_user;

//--------MUESTRA LOS DATOS DEL USUARIO SELECCIONADO PARA EDITAR SUS PERMISOS----------//
function administrar_permisos(id_usuario_edit_param) {
  //tomar datos------//
  let imgagen_user = document.querySelector(
    "#usuario_" + id_usuario_edit_param + " img"
  ).src;
  let nombre_user = document.querySelector(
    "#usuario_" + id_usuario_edit_param + " .textMod h2"
  ).textContent;
  let tipo_user = document.querySelector(
    "#usuario_" + id_usuario_edit_param + " .textMod .pMod"
  ).textContent;
  permisos_user = document.querySelector(
    "#usuario_" + id_usuario_edit_param + " .textMod #permisos_user"
  ).value;
  let tlf_user = document.querySelector(
    "#usuario_" + id_usuario_edit_param + " .textMod #telefono_usuario"
  ).textContent;
  let email_user = document.querySelector(
    "#usuario_" + id_usuario_edit_param + " .textMod #correo_usuario"
  ).textContent;
  document.getElementById("id_del_usuario").value = id_usuario_edit_param;

  //Mostrar los datos--------//
  document.querySelector(".infoOpcionPrioridad img").src = imgagen_user;
  document.querySelector(".infoTexto #nombre_user_edit").textContent = nombre_user;
  document.querySelector(".infoTexto #tipo_user_edit").textContent = tipo_user;
  document.querySelector(".infoTexto #tlf_user_edit").textContent = tlf_user;
  document.querySelector(".infoTexto #email_user_edit").textContent = email_user;

  for (let j = 0; j < permisos_user.length; j++) {
    let checkbox = document.getElementById("op-" + j);
    if (permisos_user[j] === "1") {
      checkbox.checked = true;
    } else if (permisos_user[j] === "0") {
      checkbox.checked = false;
    }
  }

  let k = 0;
  let checkboxes = document.querySelectorAll(".op_container");
  for (let i = 0; i < checkboxes.length; i++) {
    checkboxes[i].style.display = "flex";
  }

  let checkboxes2 = document.querySelectorAll('input[type="checkbox"]');
  for (let i = 0; i < checkboxes2.length; i++) {
    if (checkboxes2[i].checked) {
      document.querySelector('.checkbox_op[for="op-' + k + '"]').style.background = "#5452ff";
      document.querySelector('.checkbox_op[for="op-' + k + '"] div').style.transform = "translateX(25px)";
    } else {
      document.querySelector('.checkbox_op[for="op-' + k + '"]').style.background = "#a0a0a0";
      document.querySelector('.checkbox_op[for="op-' + k + '"] div').style.transform = "translateX(5px)";
    }
    k++;
  }

  // Mostrar el contenedor y añadir la clase de entrada
  const contenedor = document.querySelector('.ModerarPrioridad');
  contenedor.style.display = 'flex'; // Muestra el contenedor
  contenedor.classList.add('moderarPrioridad-entrar'); // Añade la clase de entrada

  // Remove the entrance animation class if already present
  if (contenedor.classList.contains('moderarPrioridad-salir')) {
    contenedor.classList.remove('moderarPrioridad-salir');
  }
}

function cerrarContenedor() {
  const contenedor = document.querySelector('.ModerarPrioridad');

  // Añadir la clase de salida para la animación de cierre
  contenedor.classList.add('moderarPrioridad-salir');

  // Esperar a que la animación finalice antes de ocultar el contenedor
  setTimeout(function () {
    contenedor.style.display = 'none'; // Ocultar el contenedor
    contenedor.classList.remove('moderarPrioridad-entrar'); // Limpia las clases
    contenedor.classList.remove('moderarPrioridad-salir'); // Limpia las clases
  }, 500); // Debe coincidir con la duración de la transición CSS
}



let numero_check = 0;

function check_on(numero_check_param) {
  let checkboxes3 = document.querySelector('input[id="op-' + numero_check_param + '"]');
  if (!checkboxes3.checked) {
    document.querySelector('.checkbox_op[for="op-' + numero_check_param + '"]').style.background = "#5452ff";
    document.querySelector('.checkbox_op[for="op-' + numero_check_param + '"] div').style.transform = "translateX(25px)";
  } else {
    document.querySelector('.checkbox_op[for="op-' + numero_check_param + '"]').style.background = "#a0a0a0";
    document.querySelector('.checkbox_op[for="op-' + numero_check_param + '"] div').style.transform = "translateX(5px)";
  }
}









//------GUARDA LOS PERMISOS Y ENVIA EL FORMULARIO-----------//

function enviar_form_opction() {
  let nuevos_permisos = "";
  for (j = 0; j < 13; j++) {
    option_select = document.getElementById("op-" + j).checked;
    if (option_select == true) {
      nuevos_permisos = nuevos_permisos + "1";
    } else if (option_select == false) {
      nuevos_permisos = nuevos_permisos + "0";
    }
  }
  console.log(nuevos_permisos);
  document.getElementById("editar_permisos").value = nuevos_permisos;
  document.getElementById("editar_permisos_de_usuario").submit();
}



//----fin de mis funciones------//

function addEventListenerIfElementExists(elementId, event, action) {
  let element = document.getElementById(elementId);
  if (element) {
    element.addEventListener(event, action);
  }
}

function setFocusAndLoad(idButton, page) {
  const button = document.getElementById(idButton);
  if (button) {
    button.focus();
    ajax(page);
    button.classList.add("activo");
  }
}

///---- Las pestañas que cargaran por defecto -----///

/*document.body.addEventListener("click", function (event) {
  if (event.target.id === "admVerification") {
    ajax("./recibo-donacion");
  }
});*/

document.body.addEventListener("click", function (event) {
  if (event.target.id === "open-form-btn") {
    ajax("./componentes/administrador/recibo");
  }
});

document.body.addEventListener("click", function (event) {
  if (event.target.id === "admVigilanciaGeneral") {
    ajax("./componentes/cnjVigilancia/cnjVigilanciaDashboard");
  }
});

document.body.addEventListener("click", function (event) {
  if (event.target.id === "admVigilanciaGeneral") {
    ajax("./componentes/cnjVigilancia/cnjVigilanciaDashboard");
  }
});



/*----------------------- AJAX CARGA CON SESSIONSTORE --------------------- */



// Función para manejar la activación de botones
function activateButton(button) {
  let $button = $(button);
  let isSidebarButton = $button.hasClass("b-bl");

  // Solo elimina el estado activo de los botones de la sidebar si el botón activo no es de la sidebar
  if (isSidebarButton) {
    $(".b-bl").removeClass("activo"); // Elimina 'activo' de todos los botones de la sidebar
    $button.addClass("activo"); // Añade la clase 'activo' al botón pulsado
    // Aquí se elimina el uso de userType
    sessionStorage.setItem("activeButton", $button.attr('id')); // Guarda el botón activo en la sesión
  }
}

// Aquí colocan la ruta de la pestaña que quieren cargar
let componentRoutes = {
  // Rutas de Administrador
  admUsuarios: "./componentes/administrador/admUsuarios",
  admGeneral: "./componentes/administrador/admDashboard",
  admReportEgresos: "./componentes/administrador/repEgreso",
  admConAportesPatronales: "./componentes/administrador/repAportesPatronales",
  admConDonaciones: "./componentes/administrador/repDonaciones",
  volverEgreso: "./componentes/administrador/admEgresos",
  volverAprobarRecibo: "./componentes/administrador/admAportesConsulta",
  admSettings: "./componentes/administrador/admConfig",
  admAportes: "./componentes/administrador/admAportes",
  admConAportes: "./componentes/administrador/admAportesConsulta",
  admEgresos: "./componentes/administrador/admEgresos",
  admGstEgresos: "./componentes/administrador/repEgresoConsulta",
  admTipoEgresos: "./componentes/administrador/gestionarTipoEgreso",
  admGstUsuarios: "./componentes/administrador/admGstionUsuarios",
  admSolicitudes: "./componentes/administrador/solicitudes/aprobarSolicitudes",
  generalUsers: "./componentes/administrador/admGstionUsuarios",
  admCrearUsuarios: "./componentes/administrador/admRegistrarUsuarios",
  admBancos: "./componentes/administrador/bancosConsulta",
  DolarManual: "./componentes/administrador/dolarConsulta",
  admAdministrar: "./componentes/administrador/admAdministrar",
  admOperaciones: "./componentes/administrador/admMetodoPago",
  admPersonalizar: "./componentes/administrador/admPersonalizar",
  volverAportes: "./componentes/administrador/admAportesConsulta",
  gstModeradores: "./componentes/administrador/admModeradores",
  btn_agregar: "./componentes/administrador/admRegistrarModerador",
  volverAmdUsuarios: "./componentes/administrador/admGstionUsuarios",


  // Rutas de Afiliados
  aAportar: "./componentes/afiliados/afiliadosAporte",
  aPerfil: "./componentes/afiliados/afiliadosPerfil",
  btnVolverRecibo: "./componentes/afiliados/afiliadosAporte",
  btnVolverReciboconsulta: "./componentes/afiliados/afiliadosMovs",
  aMovimientos: "./componentes/afiliados/afiliadosMovs",

  // Rutas de Invitado
  invPerfil: "./componentes/invitados/invitadosPerfil",
  inAportar: "./componentes/invitados/invitadosAporte",
  volverAporteInv: "./componentes/invitados/invitadosAporte",
  volverMovimientosInv: "./componentes/invitados/invitadosMovs",
  inMovimientos: "./componentes/invitados/invitadosMovs"
};

/* Función para manejar clic en botones del sidebar */
function handleSidebarClick(event) {
  let $target = $(event.target).closest(".b-bl");
  if ($target.length) {
    activateButton($target);
    sessionStorage.setItem("currentTab", $target.attr('id')); // Guardar pestaña actual
    ajax(componentRoutes[$target.attr('id')]); // Llama a la función AJAX con la ruta correcta
    event.stopPropagation();
  }
}

// Función para manejar clic en otros botones
function handleOtherButtonsClick(event) {
  let $target = $(event.target).closest(".btn_box, .link-btn, .general, .btn-agregar");
  if ($target.length && componentRoutes[$target.attr('id')]) {
    activateButton($target);
    sessionStorage.setItem("currentTab", $target.attr('id')); // Guardar pestaña actual
    ajax(componentRoutes[$target.attr('id')]);

    // Mantener el último botón de la sidebar activo
    let activeButtonId = sessionStorage.getItem("activeButton");
    if (activeButtonId) {
      let $activeButton = $("#" + activeButtonId);
      if ($activeButton.length) {
        $(".b-bl").removeClass("activo");
        $activeButton.addClass("activo");
      }
    }
  }
}

// Función para cargar estado inicial desde sessionStorage
function loadInitialState() {
  let activeButtonId = sessionStorage.getItem("activeButton");
  let currentTab = sessionStorage.getItem("currentTab");
  let currentPath = window.location.href.split("/").pop();

  let defaultTabs = {
    "Admin.php": "admGeneral",
    "afiliados.php": "aPerfil",
    "invitados.php": "invPerfil",
  };

  let defaultTab = defaultTabs[currentPath];

  if (activeButtonId) {
    let $activeButton = $("#" + activeButtonId);
    if ($activeButton.length) {
      $activeButton.addClass("activo");
    }
  }

  if (currentTab && componentRoutes[currentTab]) {
    ajax(componentRoutes[currentTab]); // Cargar la pestaña actual
  } else if (defaultTab && componentRoutes[defaultTab]) {
    ajax(componentRoutes[defaultTab]);
    sessionStorage.setItem("currentTab", defaultTab);
  }
}

// Agrega el listener para los otros botones y el sidebar
$(document).on("click", ".btn_box, .link-btn, .general, .btn-agregar, .b-bl", function (event) {
  handleOtherButtonsClick(event);
  handleSidebarClick(event);
});

// Carga el estado inicial en DOM ready
$(document).ready(function () {
  loadInitialState();
});

/*----------------------- AJAX CARGA CON SESSIONSTORE --------------------- */








document.body.addEventListener("click", function (event) {
  if (event.target.id === "btnFormulario") {
    event.preventDefault(); // Evita el envío predeterminado del formulario
    // Llama a una función para manejar el envío del formulario usando AJAX o envío regular
    handleFormSubmission();
  }
});

// formatear monto EN LOS FORMULARIOS //

function formatearMonto() {
  const input = document.getElementById("montoInput");
  const valor = input.value.replace(/\D/g, "");
  const montoFormateado = formatearNumero(valor);
  input.value = montoFormateado || "";
}

function formatearNumero(numero) {
  const valorDecimal = parseFloat(numero) / 100;
  return isNaN(valorDecimal)
    ? ""
    : valorDecimal.toLocaleString("es-VE", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
}


/// esta funcion si que me dio dolor de cabeza entenderla porque me costo hacer para que redireccione
// de nuevo en la pagina anterior, me costo entenderle  att: Kevin
// lo que hace esta funcion es que A la hora de que se le da click al boton de verificar aporte
// y luego se le de aprobar, cargue la ventana anterior o cualquier ventana que yo ponga en el link
//voy a dejarlo comentado por si acaso.

/// ADMINISTRAR LA VERIFICACION DE PAGOS EN AMDAPORTES

function verificarAporte(id, tipo, usuario) {
  let xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "./componentes/administrador/reciboDonacion.php?id=" +
    id +
    "&tipo=" +
    tipo +
    "&usuario=" +
    usuario,
    true
  );
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      document.getElementById("af-container").innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}

function actualizarEstado(accion) {
  /*//===Activacion Del Modal De Carga(AFILIADOS)===//*/
  let Loader = document.getElementById('modal_loader');
  Loader.style.display = 'flex';
  /*//===Fin Del Modal DE Carga ☝🤓===//*/
  let form = document.getElementById("reciboForm");
  let formData = new FormData(form);
  formData.append("accion", accion);

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "./componentes/administrador/actualizarEstado.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      alert(xhr.responseText);
      // Redirigir al contenedor af-container
      //Esto es para ver si funciona B)
      Loader.style.display = 'none';
      cargarAportesMovs();
    }
  };
  xhr.send(formData);
}

function cargarAportesMovs() {
  let xhr = new XMLHttpRequest();
  xhr.open("GET", "./componentes/administrador/admAportesConsulta.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      document.getElementById("af-container").innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}

//FIN //////////////////////////////////////////////////////////////////////////////////////////////////

function ajax2(page, extension = "php") {
  const http = new XMLHttpRequest();
  const url = `${page}.${extension}`;

  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("gstionBody").innerHTML = this.responseText;
    }
  };
  http.open("get", url);
  http.send();
}

//-------cerrar sesion COOKIES DE GILBER ---------//
function CloseSesion() {
  // Función para eliminar una cookie
  function deleteCookie(name) {
    document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
    document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/SACIPS";
    document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/SACIPS/";
  }

  // Eliminar todas las cookies
  const cookies = document.cookie.split("; ");
  for (let i = 0; i < cookies.length; i++) {
    const cookie = cookies[i].split("=")[0];
    deleteCookie(cookie);
  }

  // Eliminar localStorage y sessionStorage
  localStorage.clear();
  sessionStorage.clear();

  // Mostrar alerta y redirigir
  alert("Ha cerrado la sesión.");
  window.location.href = "index.php";
}

// Llamar a la función al hacer clic en el botón de cerrar sesión
document.getElementById("logoutBtn").addEventListener("click", CloseSesion);

//// APROBAR APORTES////

document.body.addEventListener("click", function (event) {
  if (event.target.id === "admVerification") {
    //ajax("./componentes/administrador/aprobarRecibo");
    event.preventDefault();
  }
});

//-------/Enviar la cookie/----------//




// / -------- VALIDACION PARA ENVIARO EL FORMULARIO DE APORTE EN AFILIADO -------///
function submitFormInv() {
  if (validateForm()) {
    // Llama a la función que maneja el envío del formulario  
    handleInvitadoFormSubmission();
  }
}


function validateForm() {
  const selectValue = document.getElementById("banco").value;
  const concepto = document.getElementById("concepto").value;
  const referencia = document.getElementById("referencia").value;
  const montoInput = document.getElementById("montoInput").value;
  const operaciontValue = document.getElementById("tipoOperacion").value;
  const telefonoValue = document.querySelector('input[name="telefono"]').value;
  const cedulaValue = document.getElementById("cedula").value;
  const fileInput = document.getElementById("imagen");

  // Realiza las validaciones  
  if (selectValue === "") {
    alert("Por favor, selecciona un banco antes de continuar.");
    return false;
  }

  if (telefonoValue.trim() === "") {
    alert("Por favor, complete el campo Teléfono.");
    return false;
  }

  if (referencia.trim() === "") {
    alert("Por favor, complete el campo referencia.");
    return false;
  }

  if (montoInput.trim() === "") {
    alert("Por favor, complete el campo Monto.");
    return false;
  }

  if (operaciontValue.trim() === "") {
    alert("Por favor, complete el campo Metodo de Pago.");
    return false;
  }

  if (concepto.trim() === "") {
    alert("Por favor, agregue un concepto de pago.");
    return false;
  }

  if (cedulaValue.trim() === "") {
    alert("Por favor, completa el campo Cédula.");
    return false;
  }

  if (fileInput.files.length === 0) {
    alert("Por favor, seleccione el capture antes de continuar.");
    return false;
  } else {
    const allowedFormats = ['image/png', 'image/jpeg', 'image/jpg'];
    const file = fileInput.files[0];
    if (!allowedFormats.includes(file.type)) {
      alert("Formato de imagen incorrecto. Solo se permiten archivos PNG, JPG y JPEG.");
      return false;
    }
  }

  return true; // Si todo es válido, retorna verdadero  
}


function submitFormAfiliado() {
  event.preventDefault();
  // Llama a la función que valida y maneja el envío del formulario 
  if (validateAfiliadoForm() === true) {
    handleAfiliadoFormSubmission();
  }
}

function validateAfiliadoForm() {
  const bancoValue = document.getElementById("banco").value;
  const conceptoValue = document.getElementById("concepto").value;
  const referenciaValue = document.querySelector('input[name="referencia"]').value;
  const montoInput = document.getElementById("montoInput").value;
  const telefonoValue = document.querySelector('input[name="telefono"]').value;
  const cedulaValue = document.getElementById("cedula").value;
  const fileInput = document.getElementById("imagen");

  // Validaciones  
  if (bancoValue === "") {
    alert("Por favor, selecciona un banco.");
    return false;
  }
  if (telefonoValue.trim() === "") {
    alert("Por favor, complete el campo Teléfono.");
    return false;
  }
  if (referenciaValue.trim() === "") {
    alert("Por favor, complete el campo Referencia.");
    return false;
  }
  if (montoInput.trim() === "") {
    alert("Por favor, complete el campo Monto.");
    return false;
  }
  if (conceptoValue.trim() === "") {
    alert("Por favor, agregue un concepto.");
    return false;
  }
  if (cedulaValue.trim() === "") {
    alert("Por favor, completa el campo Cédula.");
    return false;
  }
  if (fileInput.files.length === 0) {
    alert("Por favor, seleccione la imagen.");
    return false;
  } else {
    const allowedFormats = ['image/png', 'image/jpeg', 'image/jpg'];
    const file = fileInput.files[0];
    if (!allowedFormats.includes(file.type)) {
      alert("Formato de imagen incorrecto. Solo se permiten archivos PNG, JPG y JPEG.");
      return false;
    }
  }
  return true;
}



function handleAfiliadoFormSubmission() {

  /*//===Activacion Del Modal De Carga(AFILIADOS)===//*/
  let Loader = document.getElementById('modal_loader');
  Loader.style.display = 'flex';
  /*//===Fin Del Modal DE Carga ☝🤓===//*/
  const form = document.getElementById("formularioAfiliados");
  const formData = new FormData(form);

  fetch("./componentes/afiliados/procesarPago.php", {
    method: "POST",
    body: formData,
  })
    .then(response => {
      // Verifica si la respuesta es un JSON válido
      if (!response.ok) {
        throw new Error("Error en la respuesta del servidor");
      }
      return response.json();
    })
    .then(data => {
      console.log(data); // Verificar los datos 

      if (data.success) {
        // Si la respuesta indica éxito, maneja el recibo 
        fetch("./componentes/afiliados/reciboPago.php?data=" + JSON.stringify(data))
          .then((response) => response.text())
          .then((html) => {
            document.getElementById("af-container").innerHTML = html;
          });
        //Esto es para ver si funciona B)
        Loader.style.display = 'none';
        alert("Su aporte ha sido registrado correctamente.");
      } else {
        alert(data.message); // Muestra el mensaje de error 

      }
    })
    .catch(error => {
      console.error("Error en la solicitud:", error);
      alert("Ocurrió un error al procesar la solicitud.");
      //Esto es para ver si funciona B)
      Loader.style.display = 'none';
    });
}





// INVITADOS ///
document.getElementById("invSubmit").addEventListener("click", function (event) {
  event.preventDefault(); // Previene el envío predeterminado del formulario  
  handleInvitadoFormSubmission(); // Llama a la función que maneja la validación y envío  
});

function handleInvitadoFormSubmission() {
  /*//===Activacion Del Modal De Carga(invitados)===//*/
  let Loader = document.getElementById('modal_loader');
  Loader.style.display = 'flex';
  /*//===Fin Del Modal DE Carga ☝🤓===//*/
  const form = document.getElementById("formularioInvitado");
  const formData = new FormData(form);

  fetch("./componentes/invitados/procesarPagoinv.php", {
    method: "POST",
    body: formData,
  })
    .then(response => response.json().catch(() => { throw new Error('Invalid JSON'); }))  // Verifica que la respuesta sea JSON
    .then((data) => {
      if (data.success) {
        // Si la respuesta indica éxito, maneja el recibo  
        fetch(
          "./componentes/invitados/reciboPagoinv.php?data=" + JSON.stringify(data)
        )
          .then((response) => response.text())
          .then((html) => {
            document.getElementById("af-container").innerHTML = html;
          });
        //Esto es para ver si funciona B)
        Loader.style.display = 'none';
        alert("Su aporte ha sido registrado correctamente.");
      } else {
        alert(data.message); // Muestra el mensaje de error  
      }
    })
    .catch((error) => {
      console.error("Error en la solicitud:", error);
      alert("Ocurrió un error al procesar la solicitud.");
      //Esto es para ver si funciona B)
      Loader.style.display = 'none';
    });
}


// FIN ENVIAR FORMULARIO//

document.body.addEventListener("click", function (event) {
  if (event.target.id === "btnFormulario") {
    event.preventDefault(); // Prevent default form submission

    // Send form data using AJAX
    ajax("./componentes/afiliados/procesarPago.php");
  }
});
// APORTAR AFILIADOS//

function mostrarVentana(ventanaId) {
  // Ocultar todas las ventanas emergentes
  document.querySelectorAll(".validacion > div").forEach((ventana) => {
    ventana.style.display = "none";
    event.preventDefault();
  });

  // Mostrar la ventana emergente seleccionada
  document.getElementById(ventanaId).style.display = "block";
}


document.body.addEventListener("click", function (event) {
  if (event.target.id === "btnVolverReciboinv") {
    ajax("./componentes/invitados/invitadosAporte");
    event.preventDefault();
  }
});

document.body.addEventListener("click", function (event) {
  if (event.target.id === "btnVolverReciboinvCon") {
    ajax("./componentes/invitados/invitadosMovs");
    event.preventDefault();
  }
});


//=========//Gestion De Usuarios//========//

function HistorialAportes(Usuario) {

  document.cookie = "IdUserSelect=" + Usuario + "; path=/";

  // Solicitud AJAX para obtener HTML (si sigue siendo necesaria)
  ajax("./componentes/administrador/HistorialAporte");

}

function EditarUsuarios(usuarios, tipoUser) {

  document.cookie = "IdUserSelect=" + usuarios + "; path=/";

  //$tipoUsuario = 'Afiliado';
  //$tipoUsuario = 'Invitado';
  //$tipoUsuario = 'Director';
  //$tipoUsuario = 'Vigilante';
  //$tipoUsuario = 'Desconocido';

  if (tipoUser == 'Director') {
    document.querySelector('#PermisoRequire #editar').style.display = 'flex';
    document.querySelector('#PermisoRequire #eliminar').style.display = 'none';
    const Permisos = document.getElementById('PermisoRequire');
    Permisos.style.display = 'flex';
    setTimeout(() => {
      Permisos.style.opacity = '1';
    }, 100);
  } else {
    ajax("./componentes/administrador/editarUsuario");
  }

}

function editarAdmin(Option, editar) {

  const Permisos = document.getElementById('PermisoRequire');
  if (Option === 'cancel') {
    Permisos.style.opacity = '0';
    setTimeout(() => {
      Permisos.style.display = 'none';
    }, 500);
  }
  document.getElementById('continuar').onclick = function () {
    editarAdmin('submit', true);
  };

  if (Option === 'submit' && editar === true) {
    var inputPassword = document.getElementById('adminPassword').value;
    var hiddenPassword = document.getElementById('hiddenPassword').value;
    var errorMessage = document.getElementById('error-message');

    if (inputPassword === '') {
      errorMessage.textContent = 'Por favor, ingrese un valor.';
    } else if (inputPassword !== hiddenPassword) {
      errorMessage.textContent = 'Contraseña incorrecta. Por favor, intente nuevamente.';
    } else {
      ajax("./componentes/administrador/editarUsuario");
    }
  }
  if (Option === 'submit' && editar === false) {
    var inputPassword = document.getElementById('adminPassword').value;
    var hiddenPassword = document.getElementById('hiddenPassword').value;
    var errorMessage = document.getElementById('error-message');

    if (inputPassword === '') {
      errorMessage.textContent = 'Por favor, ingrese un valor.';
    } else if (inputPassword !== hiddenPassword) {
      errorMessage.textContent = 'Contraseña incorrecta. Por favor, intente nuevamente.';
    } else {
      document.querySelector('#PermisoRequire #editar').style.display = 'none';
      document.querySelector('#PermisoRequire #eliminar').style.display = 'flex';
    }
  }
}

function EliminarUsuarios(usuario) {
  const Permisos = document.getElementById('PermisoRequire');
  Permisos.style.display = 'flex';
  setTimeout(() => {
    Permisos.style.opacity = '1';
  }, 100);

  document.getElementById('continuar').onclick = function () {
    editarAdmin('submit', false);
  };
  document.cookie = "IdUserSelect=" + usuario + "; path=/";
  document.getElementById('E-continuar').onclick = function () {
    EliminarUser('submit', `${usuario}`);
  };
}

function EliminarUser(option, usuario) {
  if (option === 'submit') {
    window.location.href = "./componentes/administrador/eliminarUsuario.php";
  }
  const Permisos = document.getElementById('PermisoRequire');
  if (option === 'cancel') {
    Permisos.style.opacity = '0';
    setTimeout(() => {
      Permisos.style.display = 'none';
    }, 500);
  }
}

//======//Fin de Gestion de usuarios//=======//
function filtrarTabla() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const categoryFilter = document.getElementById('categoryFilter').value;
  const rows = document.querySelectorAll('#tablaMostrarMetodoPago tr');

  rows.forEach((row) => {
    const tipo = row.cells[1].textContent.toLowerCase();
    const categoria = row.getAttribute('data-categoria');
    const showRow = (tipo.includes(searchInput) || searchInput === '') &&
      (categoria === categoryFilter || categoryFilter === '');
    row.style.display = showRow ? '' : 'none';
  });
}
//======//Tipos de operacion//========//


/* MODALES PARA LA GESTION DE APORTES, EGRESOS, PERFILES Y CONFIGURACION */

// Abre un modal para editar o procesar una operación
function abrirModalEditar(id, tipo, categoriaPago) {
  document.getElementById('id_tipoOperacion_modal').value = id;
  document.getElementById('tipo_modal').value = tipo;

  // Seleccionamos el radio button correspondiente
  if (categoriaPago) {
    document.querySelector(`input[name="categoriaPagoEditar"][value="${categoriaPago}"]`).checked = true;
  }

  // Mostramos el modal
  let modal = document.getElementById('modalEditar');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);


}



// Abre el modal de eliminación
function abrirModalEliminar(id, tipo) {
  document.getElementById('id_tipoOperacion_eliminar').value = id;
  document.getElementById('tipo_eliminar').innerText = tipo;
  document.getElementById('modalEliminar').style.display = 'block';
}


// Cierra el modal de eliminación
function cerrarModalEliminar() {
  // Verifica si el modal está abierto antes de cerrarlo
  let modal = document.getElementById('modalEliminar');
  if (modal) {
    modal.style.display = 'none';
    setTimeout(function () {
      modal.classList.remove('show');
    }, 10);
  }
}

/* Modales del dashboard y conteo de usuarios del sistema */

// Muestra el modal de usuarios
function mostrarModalDb(tipo) {
  const modal = document.getElementById("modal");
  const modalTitle = document.getElementById("modal-title");
  const modalBody = document.getElementById("modal-body");

  // Realiza solicitud AJAX para datos
  fetch(`./componentes/administrador/solicitudes/dashboard/getUsuarios.php?tipo=${tipo}`)
    .then(response => response.json())
    .then(data => {
      modalTitle.textContent = `Persona | ${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`;
      modalBody.innerHTML = data.map(usuario => `
              <p><strong>Nombre:</strong> ${usuario.nombre} ${usuario.apellido}</p>
              <p><strong>Cédula:</strong> ${usuario.cedula}</p>
              <p><strong>Teléfono:</strong> ${usuario.telefono}</p>
              <p><strong>Correo:</strong> ${usuario.correo}</p>
              <hr>
          `).join('');
      modal.style.display = 'block';
      setTimeout(function () {
        modal.classList.add('show');
      }, 10);
    })
    .catch(error => console.error('Error:', error));
}

// Cierra el modal de usuarios
function cerrarModalDb() {
  const modal = document.getElementById("modal");
  modal.classList.remove('show');

  setTimeout(function () {
    modal.style.display = 'none';
  }, 300);
}

// Función para abrir el modal y cargar los datos actuales de la cuenta


function abrirModal_actCuentaInstitucion() {
  let modal = document.getElementById('modal_actCuentaInstitucion');
  modal.style.display = 'block';

  // Cargar la información de los inputs en el modal
  document.getElementById("tituloCuentaUpdate").value = document.getElementById("propietarioCuenta").value;

  // Métodos de pago
  let metodoPagoValor = document.getElementById("metodoPago").dataset.id;
  let selectMetodoPago = document.getElementById("selectMetodoPagoUpdate");
  selectMetodoPago.value = metodoPagoValor;

  // Banco
  let bancoValor = document.getElementById("nombreBanco").dataset.id;
  let bancoSelect = document.getElementById("bancoUpdate");

  // Seleccionar el banco correcto en el select
  Array.from(bancoSelect.options).forEach(option => {
    if (option.value === bancoValor) {
      bancoSelect.value = option.value;
    }
  });

  console.log("Método de Pago:", metodoPagoValor);
  console.log("Banco:", bancoValor);
  console.log("Select Método de Pago:", selectMetodoPago.value);
  console.log("Select Banco:", bancoSelect.value);

  document.getElementById("nro_cuentaUpdate").value = document.getElementById("numeroCuenta").value;

  let tipoCuentaValue = document.getElementById("tipoCuenta").value;
  document.getElementById("tipoCorrienteUpdate").checked = (tipoCuentaValue.toLowerCase() === 'corriente');
  document.getElementById("tipoAhorroUpdate").checked = (tipoCuentaValue.toLowerCase() === 'ahorro');

  let cedulaRifFull = document.getElementById("cedulaRif").value.split('-');
  document.getElementById("tipoRifUpdate").value = cedulaRifFull[0];
  document.getElementById("cedulaRifUpdate").value = cedulaRifFull[1];

  document.getElementById("telefonoCuentaUpdate").value = document.getElementById("telefonoCuenta").value;
  document.getElementById("informacion_adicionalUpdate").value = document.getElementById("infoAdicional").value;
  document.getElementById("idCuentaUpdate").value = document.getElementById("id_cuenta").value;

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}



/* ACTUALIZAR INFORMACION DEL SISTEMA */

function abrirModal_actInfoSistema() {
  let modal = document.getElementById('modal_actInfoSistema');
  modal.style.display = 'block';
  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

function previsualizarLogo(event) {
  const reader = new FileReader();
  reader.onload = function () {
    let logoPreview = document.getElementById('logoSistemaPreview');
    logoPreview.src = reader.result;
  }
  reader.readAsDataURL(event.target.files[0]);
}


// Cierra el modal de actualización de información del sistema
function cerrarModal_infoSistema() {
  let modal = document.getElementById('modal_actInfoSistema');
  let form = document.getElementById('formUpdateSistema');
  form.reset(); // Restablecer el formulario
  modal.classList.remove('show'); // Remover clase para el efecto visual
  setTimeout(function () {
    modal.style.display = 'none'; // Ocultar modal después del efecto
  }, 300); // Duración del efecto al cerrar
}



/* FIN ACT INFO SISTEMA */




// Cierra el modal al hacer clic fuera de él
window.onclick = function (event) {
  const modal = document.getElementById("modal");
  if (event.target == modal) {
    cerrarModalDb();
  }
}

// Cierra el modal al presionar la tecla Escape
document.addEventListener('keydown', function (event) {
  if (event.key === 'Escape') {
    cerrarModalDb();
  }
});

/* Modales para la gestión del dólar */

// Abre el modal para mostrar el precio del dólar
function openModal(precio) {
  document.getElementById('precio_dolar').value = precio;
  document.getElementById('modal').style.display = 'block';
}

// Cierra el modal del dólar
function closeModal() {
  document.getElementById('modal').style.display = 'none';
}

// Abre el modal para confirmar eliminación
function openDeleteModal() {
  document.getElementById('deleteModal').style.display = 'block';
}

// Cierra el modal de eliminación
function closeDeleteModal() {
  document.getElementById('deleteModal').style.display = 'none';
}

// Abre el modal de egreso
function abrirModalEgreso(id, codeEgreso, tipo) {
  let modal = document.getElementById('modalEgreso');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);


  document.getElementById('id_TipoEgreso_modal').value = id;
  document.querySelector('#modalEgreso .codigoEgreso').value = codeEgreso;
  document.getElementById('tipo_modal').value = tipo;
}



function cerrarModalUsuario() {
  let modal = document.getElementById('modalBuscarPersona');
  modal.style.display = 'none'

  modal.classList.remove('show');
}


// Abre el modal para recibir información de egreso
function ModalEgresoResivo(codigoEgreso, tipo, beneficiario, fecha, tipoOperacion, banco, nro_cuenta, concepto, monto) {
  event.preventDefault();

  let modal = document.getElementById('modalEgreso');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);

  document.getElementById('codigoEgresoInput').innerText = codigoEgreso;
  document.getElementById('tipoInput').innerHTML = tipo;
  document.getElementById('beneficiarioInput').value = beneficiario;
  document.getElementById('fechaInput').value = fecha;
  document.getElementById('tipoOperacionInput').value = tipoOperacion;
  document.getElementById('bancoInput').value = banco;
  document.getElementById('nroCuenta').value = nro_cuenta;
  document.getElementById('conceptoInput').value = concepto;
  document.getElementById('montoInput').value = monto;
}

// Abre el modal para añadir un nuevo egreso
function abrirAddEgreso() {
  let modal = document.getElementById('modalEgresoAgg');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

// Abre el modal para añadir un nuevo egreso (agregando)
function abrirAggEgreso() {
  let modal = document.getElementById('modalEgresoAgg');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

// Cierra el modal de agregar egreso
function cerrarAggEgreso() {
  let modal = document.getElementById('modalEgresoAgg');
  modal.style.display = 'none';

  setTimeout(function () {
    modal.classList.remove('show');
  }, 10);

  document.getElementById('id_TipoEgreso_modal').value = '';
  document.getElementById('tipo_modal').value = '';
}

// Abre el modal para añadir método de pago
function abrirAdd_MetdoPago() {
  let modal = document.getElementById('modal_MetodoPago');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

// Abre el modal para añadir nuevo banco
function abrirAdd_banco() {
  let modal = document.getElementById('modal_nuevoBanco');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}


function cerrarModal_editarMp() {
  document.getElementById('id_tipoOperacion_modal').value = '';
  document.getElementById('tipo_modal').value = '';
  let modal = document.getElementById('modalEditar');


  modal.classList.remove('show');


  setTimeout(function () {
    modal.style.display = 'none';
  }, 300);
}


function abrirModalRegistroUsuario() {
  let modal = document.getElementById('modalUsuarioRegistro');
  modal.style.display = 'block';
  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

function cerrarModal_registroUsuario() {
  let modal = document.getElementById('modalUsuarioRegistro');

  modal.classList.remove('show');

  setTimeout(function () {
    modal.style.display = 'none';
  }, 10);
}


// Abre el modal para gestionar correos
function abrirModal_correo() {
  let modal = document.getElementById('correoModal');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

// Abre el modal para gestionar teléfonos
function abrirModal_telefono() {
  let modal = document.getElementById('telefonoModal');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

// Abre el modal para gestionar contraseñas
function abrirModal_clave() {
  let modal = document.getElementById('claveModal');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

// --<cuenta>  
// Abre el modal para gestionar cuenta de la institucion

function abrirModal_eggCuenta() {
  let modal = document.getElementById('modal_eggCuenta');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

/* <div class="seleccionar un solo checkbox"> */
function toggleCheckbox(checkbox) {
  const checkboxes = document.querySelectorAll('input[name="tipoCuenta"]');
  checkboxes.forEach((item) => {
    if (item !== checkbox) item.checked = false;
  });
}
/* </div> */

//--</cuenta>


// Abre el modal para editar bancos
function abrirModalBanco(id, codeEgreso, nombreBanco) {
  let modal = document.getElementById('modal_editarBanco');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);

  document.getElementById('id_bancoTable').value = id;
  document.getElementById('idBancoEditar').value = codeEgreso;
  document.getElementById('nombreBancoEditar').value = nombreBanco;
}

// Abre el modal para buscar bancos
function abrirBuscarBanco() {
  let modal = document.getElementById('modal_BuscarBanco');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

// Abre el modal de vigilancia
function abrirModalVigilancia(tipo, beneficiario, monto, fecha, concepto, comentario) {
  document.getElementById('modalTipo').textContent = tipo;
  document.getElementById('modalBeneficiario').textContent = beneficiario;
  document.getElementById('modalMonto').textContent = monto;
  document.getElementById('modalFecha').textContent = fecha;
  document.getElementById('modalConcepto').textContent = concepto;
  document.getElementById('modalComentario').textContent = comentario;

  let modal = document.getElementById('modalVigilancia');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

// Cierra el modal de vigilancia
function cerrarModalVigilancia() {
  let modal = document.getElementById('modalVigilancia');
  modal.classList.remove('show');
  modal.classList.add('hide');

  setTimeout(function () {
    modal.style.display = 'none';
    modal.classList.remove('hide');
  }, 300);
}


// Cierra el modal de cuentas y limpia los inputs
function cerrarModal_eggCuenta() {
  let modal = document.getElementById('modal_eggCuenta');
  modal.style.display = 'none';

  // Limpiar inputs y selects del formulario
  document.getElementById('formAgregarCuenta').reset();

  // Desmarcar checkboxes
  const checkboxes = document.querySelectorAll('.input-tipo');
  checkboxes.forEach(checkbox => checkbox.checked = false);

  setTimeout(function () {
    modal.classList.remove('show');
  }, 10);
}

// Abre el modal para eliminar banco
function abrirModalEliminarBanco(id, codeEgreso, nombreBanco) {
  let modal = document.getElementById('modal_eliminarBanco');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);

  document.getElementById('id_bancoTable').value = id;
  document.getElementById('idBancoEliminar').textContent = codeEgreso;
  document.getElementById('nombreBancoEliminar').textContent = nombreBanco;
}

// Abre el modal para actualizar información del sistema
function abrirModal_actInfoSistema() {
  let modal = document.getElementById('modal_actInfoSistema');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

// Abre el modal para actualizar información de la institución
function abrirModal_actInfoInstitucion() {
  let modal = document.getElementById('modal_actInfoInstitucion');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}


function abrirModalTipoUsuario() {
  let modal = document.getElementById("modalTipoUsuario")
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);
}

function cerrarModalTipoUsuario() {
  let modal = document.getElementById("modalTipoUsuario");
  modal.classList.remove('show');
  setTimeout(function () {
    modal.style.display = 'none';
  }, 10);
}



// Evento para cerrar el modal al presionar Escape
document.addEventListener('keydown', function (event) {
  if (event.key === "Escape") {
    cerrarModales();
  }
});

// Evento para cerrar el modal al hacer clic fuera de él
window.addEventListener('click', function (event) {
  const modales = document.querySelectorAll('.modal');

  modales.forEach(modal => {
    if (event.target === modal) { // Verifica si el clic fue en el modal
      cerrarModales();
    }
  });
});

// Abre el modal de confirmación para eliminar la cuenta
function abrirModal_EliminarCuenta() {

  let modal = document.getElementById('modal_eliminarCuenta');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);

  // Cargar el id de la cuenta que se va a eliminar para usar más tarde
  let idCuenta = document.getElementById("id_cuenta").value;
  document.getElementById("idCuentaEliminar").value = idCuenta;
}

// Cierra el modal de eliminar cuenta
function cerrarModal_EliminarCuenta() {
  let modal = document.getElementById('modal_eliminarCuenta');
  modal.style.display = 'none';
  setTimeout(function () {
    modal.classList.remove('show');
  }, 300);
}


// Cierra el modal para actualizar cuentas de la institución
function cerrarModal_actCuentaInstitucion() {
  let modal = document.getElementById('modal_actCuentaInstitucion');
  modal.style.display = 'none';

  setTimeout(function () {
    modal.classList.remove('show');
  }, 300);
}

// Cierra el modal de eliminación de banco
function cerrarModalEliminarBanco() {
  let modal = document.getElementById('modal_eliminarBanco');
  modal.style.display = 'none';

  setTimeout(function () {
    modal.classList.remove('show');
  }, 300);

  document.getElementById('id_bancoTable').value = '';
  document.getElementById('idBancoEliminar').textContent = '';
  document.getElementById('nombreBancoEliminar').textContent = '';
}

// Cierra el modal de información del sistema
function cerrarModal_infoSistema() {
  let modal = document.getElementById('modal_actInfoSistema');
  modal.style.display = 'none';

  setTimeout(function () {
    modal.classList.remove('show');
  }, 300);
}


/// Cierra el modal de información de la institución
function cerrarModal_infoIntitucion() {
  let modal = document.getElementById('modal_actInfoInstitucion');
  let form = document.getElementById('formUpdateInfo');
  form.reset(); // Restablecer el formulario
  modal.classList.remove('show'); // Remover clase para el efecto visual

  modal.style.display = 'none'; // Ocultar modal después del efecto

}


// Cierra el modal de búsqueda de bancos
function cerrarBuscarBanco() {
  let modal = document.getElementById('modal_BuscarBanco');
  modal.style.display = 'none';

  setTimeout(function () {
    modal.classList.remove('show');
  }, 300);

  document.getElementById('busquedaBanco').value = "";
  document.getElementById('resultadoBusquedaBanco').innerHTML = "";
}

// Cierra el modal de edición de banco
function cerrarModalBanco() {
  let modal = document.getElementById('modal_editarBanco');
  modal.style.display = 'none';

  setTimeout(function () {
    modal.classList.remove('show');
  }, 300);

  document.getElementById('idBancoEditar').value = "";
  document.getElementById('nombreBancoEditar').value = "";
}

// Abre el modal para el mensaje de perfil
function perfilModal_mensaje() {
  let modal = document.getElementById('perfilModal_mensaje');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 300);
}

// Cierra el modal de mensaje de perfil
function cerrarModal_mensaje() {
  let modal = document.getElementById('perfilModal_mensaje');
  modal.style.display = 'none';

  setTimeout(function () {
    modal.classList.remove('show');
  }, 300);

  document.getElementById('mensajeRespuesta').textContent = '';
}

// Cierra el modal de correo
function cerrarModal_correo() {
  let modal = document.getElementById('correoModal');
  modal.style.display = 'none';

  setTimeout(function () {
    modal.classList.remove('show');
  }, 300);

  document.getElementById('mensajeRespuestaCorreo').textContent = '';
  document.getElementById('confirmarCorreo').value = '';
}

// Cierra el modal de clave
function cerrarModal_clave() {
  let modal = document.getElementById('claveModal');
  modal.style.display = 'none';

  setTimeout(function () {
    modal.classList.remove('show');
  }, 300);

  document.getElementById('mensajeRespuestaClave').textContent = '';
  document.getElementById('confirmClave').value = '';
  document.getElementById('nuevaClave').value = '';
  document.getElementById('claveAnterior').value = '';
}

// Cierra el modal de teléfono
function cerrarModal_telefono() {
  let modal = document.getElementById('telefonoModal');
  modal.style.display = 'none';

  setTimeout(function () {
    modal.classList.remove('show');
  }, 300);

  document.getElementById('mensajeRespuesta').textContent = '';
}

// Cierra el modal de egreso
function cerrarModalEgreso() {
  let modal = document.getElementById('modalEgreso');
  modal.classList.remove('show');

  setTimeout(function () {
    modal.style.display = 'none';
  }, 300);

  document.getElementById('tipoEgresoInput').value = '';
  document.getElementById('mensajeRespuesta').textContent = '';
}

function cerrarModalAddEgreso() {
  let modal = document.getElementById('modalEgresoAgg');
  modal.classList.remove('show');

  setTimeout(function () {
    modal.style.display = 'none';
  }, 300);

  document.getElementById('tipoEgresoInput').value = '';
  document.getElementById('mensajeRespuesta').textContent = '';
}


// Cierra el modal de errores
function cerrar_errores() {
  let modal = document.getElementById('modalErrores');
  modal.classList.remove('show');

  setTimeout(function () {
    modal.style.display = 'none';
  }, 300);

  document.getElementById('mensaje_Error').textContent = '';
}

// Cierra el modal de métodos de pago

function cerrarModal_mp() {
  let modal = document.getElementById('modal_MetodoPago');
  modal.classList.remove('show');

  // Resetear el formulario
  document.getElementById('agregarNuevoMetodoPago').reset();

  setTimeout(function () {
    modal.style.display = 'none';
    console.log("Modal ocultado");
  }, 300);
}


// Cierra el modal de nuevo banco
function cerrarModal_banco() {
  let modal = document.getElementById('modal_nuevoBanco');
  modal.classList.remove('show');

  setTimeout(function () {
    modal.style.display = 'none';
  }, 300);

  // Resetear el formulario de banco
  document.getElementById('agregarNuevoBanco').reset();

  document.getElementById('idBanco').value = '';
  document.getElementById('nombreBanco').value = '';
  document.getElementById('mensajeRespuesta').textContent = '';
  document.querySelector('mensaje_banco').reset();
}

// Abre el modal de mensaje para egreso
function modalMensaje() {
  let modal = document.getElementById('modalMensajeEgreso');
  modal.classList.add('show');

  setTimeout(function () {
    modal.style.display = 'block';
  }, 300);
}

// Cierra el modal de mensaje para egreso
function cerrarModalMensajeEgreso() {
  let modal = document.getElementById('modalMensajeEgreso');
  modal.classList.remove('show');

  setTimeout(function () {
    modal.style.display = 'none';
  }, 300);

  document.getElementById('tipoEgresoInput').value = '';
  document.getElementById('mensajeRespuesta').textContent = '';
}

// Abre el modal de mensaje para agregar egreso
function modalMensaje_agg(mensaje) {
  let modal = document.getElementById('modalMensajeEgreso_agg');
  let mensajeSecundario = document.getElementById('mensajeSecundario');
  let mensajeEgreso = document.getElementById('egresoAgregado');


  if (modal && mensajeSecundario && mensajeEgreso) {
    mensajeSecundario.textContent = mensaje;
    mensajeEgreso.textContent = mensaje;

    modal.classList.add('show');
    setTimeout(function () {
      modal.style.display = 'block';
    }, 300);
  } else {
    console.error('El modal o el mensaje secundario no existen.');
  }
}

// Abre el modal de mensaje general
function modalMensaje_general() {
  let modal = document.getElementById('modalMensaje_general');
  modal.classList.add('show');

  setTimeout(function () {
    modal.style.display = 'block';
  }, 300);
}

// Cierra el modal de mensaje general
function cerrarMensaje_general() {
  let modal = document.getElementById('modalMensaje_general');
  modal.classList.remove('show');

  setTimeout(function () {
    modal.style.display = 'none';
  }, 300);
}

// Cierra el modal de mensaje para agregar egreso
function cerrarMoadlMensajeEgreso_agg() {
  let modal = document.getElementById('modalMensajeEgreso_agg');

  if (modal) {
    modal.classList.remove('show');

    setTimeout(function () {
      modal.style.display = 'none';
    }, 300);
  } else {
    console.error('El modal no existe.');
  }

  document.getElementById('tipoEgresoInput').value = '';
  document.getElementById('mensajeRespuesta').textContent = '';
}

// Abre el modal para eliminar egreso
function abrirModalEliminarEgreso(id, tipo) {
  let modal = document.getElementById('modalEliminarEgreso');
  modal.style.display = 'block';

  setTimeout(function () {
    modal.classList.add('show');
  }, 10);

  document.getElementById('id_TipoEgreso_eliminar').value = id;
  document.getElementById('tipo_eliminar').innerText = tipo;
}

// Cierra el modal de eliminación de egreso
function cerrarModalEliminarEgreso() {
  let modal = document.getElementById('modalEliminarEgreso');
  modal.classList.remove('show');

  setTimeout(function () {
    modal.style.display = 'none';
  }, 300);

  document.getElementById('tipoEgresoInput').value = '';
  document.getElementById('mensajeRespuesta').textContent = '';
}


// Cierra el modal de eliminación de egreso
function cerrarModalCapture() {
  let modal = document.getElementById('modalCapture');
  modal.classList.remove('show');

  setTimeout(function () {
    modal.style.display = 'none';
  }, 300);

}



// Abre un modal genérico
function openModal(id_banco, nombre_banco) {
  console.log("ID Banco: " + id_banco + ", Nombre Banco: " + nombre_banco);
  document.getElementById("modal").style.display = "block";
  document.getElementById("id_banco_actual").value = id_banco;
  document.getElementById("id_banco").value = id_banco;
  document.getElementById("nombreBanco").value = nombre_banco;
}

// Cierra el modal para agregar
function closeModalAgregar() {
  let modal = document.getElementById('modalAgregar');
  modal.classList.remove('show');

  setTimeout(function () {
    modal.style.display = 'none';
  }, 300);
}

// Abre el modal para agregar
function openModalAgregar() {
  let modal = document.getElementById('modalAgregar');
  modal.classList.add('show');

  setTimeout(function () {
    modal.style.display = 'block';
  }, 300);
}

// Cierra el modal genérico
function closeModal() {
  document.getElementById("modal").style.display = "none";
}

/* fin de los modales de GESTION DE PERFILES, APORTES, EGRESO Y CONFIGURACION */


window.onclick = function (event) {
  if (event.target == document.getElementById("modal")) {
    closeModal();
  }
  if (event.target == document.getElementById("modalAgregar")) {
    closeModalAgregar();
  }
}


///// MANDAR LOS DATOS DE AFILIADOS ////


///// MANDAR LOS DATOS DE AFILIADOS ////
function mostrarDatos(button) {
  let tipo_aporte = button.getAttribute("data-tipo_aporte");
  let tipo_nombre = button.getAttribute("data-tipo_nombre");
  let tipo_apellido = button.getAttribute("data-tipo_apellido");
  let tipo_cedula = button.getAttribute("data-tipo_cedula");
  let tipo_banco = button.getAttribute("data-tipo_banco");
  let tipo_telefono = button.getAttribute("data-tipo_telefono");
  let monto = button.getAttribute("data-monto");
  let fechaAporte = button.getAttribute("data-fechaAporte");
  let referencia = button.getAttribute("data-referencia");
  let concepto = button.getAttribute("data-concepto");
  let estado = button.getAttribute("data-estado");
  let tipoOperacion = button.getAttribute("data-tipoOperacion");
  let id = button.getAttribute('data-tipo_id');
  let imagen = button.getAttribute("data-imagen"); // New image data

  // Agrupamos todos los datos en un objeto
  const formData = new FormData();
  formData.append("id", id);
  formData.append("tipo_aporte", tipo_aporte);
  formData.append("tipo_nombre", tipo_nombre);
  formData.append("tipo_apellido", tipo_apellido);
  formData.append("tipo_cedula", tipo_cedula);
  formData.append("tipo_banco", tipo_banco);
  formData.append("tipo_telefono", tipo_telefono);
  formData.append("monto", monto);
  formData.append("fechaAporte", fechaAporte);
  formData.append("referencia", referencia);
  formData.append("concepto", concepto);
  formData.append("estado", estado);
  formData.append("tipoOperacion", tipoOperacion);
  formData.append("imagen", imagen); // Add image data

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "./componentes/afiliados/reciboPagoMostrar.php", true);

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.getElementById("af-container").innerHTML = xhr.responseText;
    }
  };

  xhr.send(formData);
  event.preventDefault(); // evitar el comportamiento del botón predeterminado
}


/* APARTADO APORTES ADMIN */


//----------------//-AFILIADO-//---------------//



///// MANDAR LOS DATOS DE AFILIADOS ////
function adminAfiliadosMostrarRef(button) {
  let tipo_aporte = button.getAttribute("data-tipo_aporte");
  let tipo_nombre = button.getAttribute("data-tipo_nombre");
  let tipo_apellido = button.getAttribute("data-tipo_apellido");
  let tipo_cedula = button.getAttribute("data-tipo_cedula");
  let tipo_banco = button.getAttribute("data-tipo_banco");
  let tipo_telefono = button.getAttribute("data-tipo_telefono");
  let monto = button.getAttribute("data-monto");
  let fechaAporte = button.getAttribute("data-fechaAporte");
  let referencia = button.getAttribute("data-referencia");
  let concepto = button.getAttribute("data-concepto");
  let estado = button.getAttribute("data-estado");
  let tipoOperacion = button.getAttribute("data-tipoOperacion");
  let id = button.getAttribute('data-tipo_id');
  let imagen = button.getAttribute("data-imagen"); // New image data

  // Agrupamos todos los datos en un objeto
  const formData = new FormData();
  formData.append("id", id);
  formData.append("tipo_aporte", tipo_aporte);
  formData.append("tipo_nombre", tipo_nombre);
  formData.append("tipo_apellido", tipo_apellido);
  formData.append("tipo_cedula", tipo_cedula);
  formData.append("tipo_banco", tipo_banco);
  formData.append("tipo_telefono", tipo_telefono);
  formData.append("monto", monto);
  formData.append("fechaAporte", fechaAporte);
  formData.append("referencia", referencia);
  formData.append("concepto", concepto);
  formData.append("estado", estado);
  formData.append("tipoOperacion", tipoOperacion);
  formData.append("imagen", imagen); // Add image data

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "./componentes/administrador/afiliadosReferenciaMostrar.php", true);

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.getElementById("af-container").innerHTML = xhr.responseText;
    }
  };

  xhr.send(formData);
  event.preventDefault(); // evitar el comportamiento del botón predeterminado
}





//-------------// Invitados //--------------//
function referenciaInvitadoAdmin(event) {
  if (event.target.classList.contains("mandarGetinv")) {
    let tipo_aporte = event.target.getAttribute("data-tipo_aporte");
    let tipo_nombre = event.target.getAttribute("data-tipo_nombre");
    let tipo_apellido = event.target.getAttribute("data-tipo_apellido");
    let tipo_cedula = event.target.getAttribute("data-tipo_cedula");
    let tipo_banco = event.target.getAttribute("data-tipo_banco");
    let tipo_telefono = event.target.getAttribute("data-tipo_telefono");
    let monto = event.target.getAttribute("data-monto");
    let fechaAporte = event.target.getAttribute("data-fechaAporte");
    let referencia = event.target.getAttribute("data-referencia");
    let concepto = event.target.getAttribute("data-concepto");
    let estado = event.target.getAttribute("data-estado");
    let tipoOperacion = event.target.getAttribute("data-tipoOperacion");

    let benefactor = event.target.getAttribute("data-tipo_benefactor");
    let beneficiario = event.target.getAttribute("data-tipo_beneficiario");
    let id = event.target.getAttribute("data-tipo_id");


    var xhr = new XMLHttpRequest();
    xhr.open(
      "GET",
      "./componentes/administrador/referenciaMostrarInvitado.php?tipo_aporte=" +
      encodeURIComponent(tipo_aporte) +
      "&tipo_nombre=" +
      encodeURIComponent(tipo_nombre) +
      "&tipo_apellido=" +
      encodeURIComponent(tipo_apellido) +
      "&tipo_cedula=" +
      encodeURIComponent(tipo_cedula) +
      "&tipo_banco=" +
      encodeURIComponent(tipo_banco) +
      "&tipo_telefono=" +
      encodeURIComponent(tipo_telefono) +
      "&monto=" +
      encodeURIComponent(monto) +
      "&fechaAporte=" +
      encodeURIComponent(fechaAporte) +
      "&referencia=" +
      encodeURIComponent(referencia) +
      "&concepto=" +
      encodeURIComponent(concepto) +
      "&estado=" +
      encodeURIComponent(estado) +
      "&tipoOperacion=" +
      encodeURIComponent(tipoOperacion) +

      "&benefactor=" +
      encodeURIComponent(benefactor) +

      "&beneficiario=" +
      encodeURIComponent(beneficiario) +

      "&id=" +
      encodeURIComponent(id),
      true
    );
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        document.getElementById("af-container").innerHTML = xhr.responseText;
      }
    };
    xhr.send();
    event.preventDefault();
  }
}
document.body.addEventListener("click", handleButtonClick);
/* FIN APARTADO ADMIN REFERENCIA */


function handleButtonClick(event) {
  if (event.target.classList.contains("mandarGetinv")) {
    let tipo_aporte = event.target.getAttribute("data-tipo_aporte");
    let tipo_nombre = event.target.getAttribute("data-tipo_nombre");
    let tipo_apellido = event.target.getAttribute("data-tipo_apellido");
    let tipo_cedula = event.target.getAttribute("data-tipo_cedula");
    let tipo_banco = event.target.getAttribute("data-tipo_banco");
    let tipo_telefono = event.target.getAttribute("data-tipo_telefono");
    let monto = event.target.getAttribute("data-monto");
    let fechaAporte = event.target.getAttribute("data-fechaAporte");
    let referencia = event.target.getAttribute("data-referencia");
    let concepto = event.target.getAttribute("data-concepto");
    let estado = event.target.getAttribute("data-estado");
    let tipoOperacion = event.target.getAttribute("data-tipoOperacion");

    let benefactor = event.target.getAttribute("data-tipo_benefactor");
    let beneficiario = event.target.getAttribute("data-tipo_beneficiario");
    let id = event.target.getAttribute("data-tipo_id");


    var xhr = new XMLHttpRequest();
    xhr.open(
      "GET",
      "./componentes/invitados/reciboPagoMostrar.php?tipo_aporte=" +
      encodeURIComponent(tipo_aporte) +
      "&tipo_nombre=" +
      encodeURIComponent(tipo_nombre) +
      "&tipo_apellido=" +
      encodeURIComponent(tipo_apellido) +
      "&tipo_cedula=" +
      encodeURIComponent(tipo_cedula) +
      "&tipo_banco=" +
      encodeURIComponent(tipo_banco) +
      "&tipo_telefono=" +
      encodeURIComponent(tipo_telefono) +
      "&monto=" +
      encodeURIComponent(monto) +
      "&fechaAporte=" +
      encodeURIComponent(fechaAporte) +
      "&referencia=" +
      encodeURIComponent(referencia) +
      "&concepto=" +
      encodeURIComponent(concepto) +
      "&estado=" +
      encodeURIComponent(estado) +
      "&tipoOperacion=" +
      encodeURIComponent(tipoOperacion) +

      "&benefactor=" +
      encodeURIComponent(benefactor) +

      "&beneficiario=" +
      encodeURIComponent(beneficiario) +

      "&id=" +
      encodeURIComponent(id),
      true
    );
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        document.getElementById("af-container").innerHTML = xhr.responseText;
      }
    };
    xhr.send();
    event.preventDefault();
  }
}
document.body.addEventListener("click", handleButtonClick);



//-------------//
// Variables para controlar el zoom y el cursor

// Variables para controlar el cursorlet isModalOpen = false; // Inicializar la variable correctamente
// Variable para controlar el estado del modal


Copiar
let isModalOpen = false; // Indica si el modal está abierto

/**
 * Función para mostrar la captura en el modal. 
 * Realiza una petición AJAX para obtener la imagen y la muestra en el modal.
 * @param {string} id - El identificador de la captura que se quiere mostrar.
 */
function mostrarCapture(id) {
  const xhr = new XMLHttpRequest();
  xhr.open("GET", `./componentes/administrador/getCapture.php?id=${id}`, true);

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          const response = JSON.parse(xhr.responseText);
          console.log(response);

          if (response.success && response.capture) {
            const captureImage = document.getElementById("captureImage");
            if (captureImage) {
              captureImage.src = "data:image/png;base64," + response.capture;
              const modalCapture = document.getElementById("modalCapture");
              modalCapture.classList.add("show");
              modalCapture.style.display = "block";
              isModalOpen = true;
            } else {
              console.error("El id del documento no ha sido definido");
            }
          } else {
            alert(response.message || "No se pudo cargar el capture.");
          }
        } catch (error) {
          console.error("Error al analizar JSON:", error);
        }
      } else {
        console.error("La solicitud ha fallado con el estado:", xhr.status);
      }
    }
  };

  xhr.send();
}


/**
 * Función para alternar el zoom de la imagen al hacer clic en ella. 
 * Aumenta o reduce la escala de la imagen dependiendo de su estado actual.
 * @param {MouseEvent} event - El evento de clic en la imagen.
 */
function toggleZoom(event) {
  const image = event.currentTarget; // Obtener la imagen que se ha clicado
  const isZoomedIn = image.classList.contains('zoomed-in'); // Verificar si la imagen está en estado "zoomed-in"

  // Alternar la clase 'zoomed-in' y aplicar el efecto de zoom
  if (isZoomedIn) {
    image.classList.remove('zoomed-in'); // Remover clase de zoom
    image.style.transform = 'none'; // Resetear transformación
  } else {
    // Obtener la posición del clic para el origen de transformación
    const rect = image.getBoundingClientRect();
    const offsetX = event.clientX - rect.left;
    const offsetY = event.clientY - rect.top;
    const x = (offsetX / image.clientWidth) * 100; // Calcular porcentaje X
    const y = (offsetY / image.clientHeight) * 100; // Calcular porcentaje Y
    image.classList.add('zoomed-in'); // Añadir clase de zoom
    image.style.transformOrigin = `${x}% ${y}%`; // Establecer el origen del zoom
    image.style.transform = 'scale(4)'; // Aplicar escala
  }
}


// Añadir evento para cerrar el modal usando la tecla Escape
document.addEventListener("keydown", function (event) {
  if (event.key === "Escape" && isModalOpen) { // Cambié `click` por `Escape`
    cerrarModal();
  }
});

// Cerrar el modal al hacer clic fuera del imageContainer
document.addEventListener("click", function (event) {
  const modalCapture = document.getElementById("modalCapture");
  const imageContainer = document.getElementById("imageContainer");
  if (isModalOpen && !imageContainer.contains(event.target)) {
    cerrarModal();
  }
});


////


/*------ SECCION DEL FORMULARIO DE EGRESO PARA AÑADIR NUEVOS EGRESOS PERO EN EL FORMULARIO DE REPORTAR EGRESOS PARA 
FACILITAR LA USABILIDAD DEL SISTEMA-------*/




/*--- AGREGAR NUEVO TIPO DE EGRESO MODAL (SIRVE TANTO EN EL SELECT DEL REPORTE DE EGRESO COMO EN LAS TABLAS DE GESIONAR EGRESO) --- */

/* abrir resivo de egreso */



/* FIN ABAJO ESTA PARA AGREGAR Y EDITAR EGRESOS */



function aggNewEgreso() {
  let tipoEgreso = document.getElementById('tipoEgresoInput').value;
  let codeEgreso = document.getElementById('codeEgresoInput').value;

  // Validación para campos vacíos
  if (tipoEgreso.trim() === "" || codeEgreso.trim() === "") {
    document.getElementById('mensajeRespuesta').textContent = 'No puede haber campos vacíos';
    return; // Salir de la función si el campo está vacío
  }

  // Verificar si el tipo de egreso ya existe
  let xhrCheck = new XMLHttpRequest();
  xhrCheck.open('POST', './componentes/administrador/solicitudes/egreso/verificar_egreso.php', true);
  xhrCheck.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhrCheck.onreadystatechange = function () {
    if (xhrCheck.readyState === 4) {
      if (xhrCheck.status === 200) {
        if (xhrCheck.responseText.trim() === 'existe') {
          document.getElementById('mensajeRespuesta').textContent = 'El tipo de egreso ya existe';
        } else {
          // Proceder con la inserción si no existe
          let xhr = new XMLHttpRequest();
          xhr.open('POST', './componentes/administrador/solicitudes/egreso/egreso_modal.php', true);
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
              if (xhr.status === 200) {
                if (!isNaN(xhr.responseText)) {
                  let idNuevoEgreso = xhr.responseText;
                  updateSelectAndTable(idNuevoEgreso, codeEgreso, tipoEgreso);
                  document.getElementById('tipoEgresoInput').value = ''; // Limpia el campo de entrada
                  document.getElementById('codeEgresoInput').value = ''; // Limpia el campo de entrada
                  cerrarModalEgreso();
                  cerrarAggEgreso();
                  modalMensaje_agg('Tipo de egreso "' + tipoEgreso + '" agregado exitosamente');

                } else {
                  document.getElementById('mensajeRespuesta').textContent = 'Error al agregar el tipo de egreso';
                }
              } else {
                document.getElementById('mensajeRespuesta').textContent = 'Error en la solicitud';
              }
            }
          };
          xhr.send('tipoEgreso=' + encodeURIComponent(tipoEgreso) + '&codeEgreso=' + encodeURIComponent(codeEgreso) + '&accion=agregar_tipo');
        }
      } else {
        document.getElementById('mensajeRespuesta').textContent = 'Error en la solicitud de verificación';
      }
    }
  };
  xhrCheck.send('tipoEgreso=' + encodeURIComponent(tipoEgreso) + '&codeEgreso=' + encodeURIComponent(codeEgreso) + '&accion=verificar_egreso');
}






// 1)  /* -- Agregar nuevo Egreso (ACTUALIZA EN TIEMPO REAL LAS TABLAS Y EL SELECT) -- */
// Función principal para agregar un nuevo método de pago

Copy
function aggNewMetodoPago() {
  const metodoPagoInput = document.getElementById('metodoPagoInput');
  const metodoPago = metodoPagoInput.value.trim();
  const categoriaPago = document.querySelector('input[name="categoriaPago"]:checked')?.value;

  // Validación para campos vacíos
  if (!metodoPago) {
    alert('El campo de método de pago no puede estar vacío');
    return;
  }

  if (!categoriaPago) {
    alert('Debe seleccionar una categoría de pago');
    return;
  }

  // Verificar si el método de pago ya existe
  const respuestaContenedor = document.getElementById('mensaje_metodoPago');
  verificarMetodoPago(metodoPago, categoriaPago, respuestaContenedor);
}

function verificarMetodoPago(metodoPago, categoriaPago, respuestaContenedor) {
  xhrPost('./componentes/administrador/solicitudes/metodo_pago/verificar_metodoPago.php', {
    metodoPago,
    accion: 'verificar_metodo'
  }, (responseText) => {
    if (responseText.trim() === 'existe') {
      alert('El método de pago ya existe');
    } else {
      agregarMetodoPago(metodoPago, categoriaPago, respuestaContenedor);
    }
  }, (error) => {
    alert('Error en la solicitud de verificación');
  });
}

function agregarMetodoPago(metodoPago, categoriaPago, respuestaContenedor) {
  xhrPost('./componentes/administrador/solicitudes/metodo_pago/agg_metodoPago.php', {
    metodoPago,
    categoriaPago,
    accion: 'agregar_metodo'
  }, (responseText) => {
    if (!isNaN(responseText)) {
      let idNuevoMetodoPago = responseText;

      updateSelectAndTableMetodoPago(idNuevoMetodoPago, metodoPago, categoriaPago);

      metodoPagoInput.value = ''; // Limpiar campo

      // Cerrar el modal actual y abrir el nuevo modal
      alert(`Método de pago '${metodoPago}' registrado exitosamente!`);
      cerrarModal_mp(); // Cerrar el modal actual
      setTimeout(() => {
        modalMensaje_agg();
      }, 500);
    } else {
      alert('Error al agregar el método de pago');
    }
  });
}

function updateSelectAndTableMetodoPago(idNuevoMetodoPago, metodoPago, categoriaPago) {
  let select = document.getElementById('metodoPagoSelect');
  if (select) {
    let nuevoMetodoPago = document.createElement('option');
    nuevoMetodoPago.value = idNuevoMetodoPago;
    nuevoMetodoPago.textContent = metodoPago;
    select.appendChild(nuevoMetodoPago);
  } else {
    console.error('El elemento select con id "metodoPagoSelect" no existe.');
  }

  let tbody = document.getElementById('tablaMostrarMetodoPago');
  if (tbody) {
    let noMetodoPagoRow = document.getElementById('noMetodoPagoRow');
    if (noMetodoPagoRow) {
      noMetodoPagoRow.style.display = 'none';
    }

    let nuevaFila = document.createElement('tr');
    nuevaFila.setAttribute('data-id', idNuevoMetodoPago);
    nuevaFila.innerHTML = `
          <td>${idNuevoMetodoPago}</td>
          <td>${metodoPago}</td>
          <td>${categoriaPago}</td>
          <td>
              <button type='button' onclick="abrirModalMetodoPago(${idNuevoMetodoPago}, '${metodoPago}')">Modificar</button>
              <button type='button' onclick="abrirModalEliminarMetodoPago(${idNuevoMetodoPago}, '${metodoPago}')" class='eliminar'>Remover</button>
          </td>
      `;
    tbody.appendChild(nuevaFila);
  } else {
    console.error('El elemento tbody no existe.');
  }
}

// Función para enviar una solicitud POST con XMLHttpRequest
function xhrPost(url, data, callback, errorCallback) {
  const xhr = new XMLHttpRequest();
  xhr.open('POST', url, true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        callback(xhr.responseText);
      } else {
        errorCallback(xhr.statusText);
      }
    }
  };

  xhr.send(Object.keys(data).map(key => `${encodeURIComponent(key)}=${encodeURIComponent(data[key])}`).join('&'));
}

function mostrarMensaje(mensaje, esError = false) {
  const contenedor = document.getElementById('mensaje_metodoPago');
  if (contenedor) {
    contenedor.textContent = mensaje;
    contenedor.style.color = esError ? 'red' : 'green';
  }
}
/*/ -- FIN EL MODAL DE AGREGAR NUEVO EGRESO / METODO DE PAGO-- /*/






/* -- MODAL ELIMINAR EGRESO -- */

function eliminarEgreso() {
  var idTipoEgreso = document.getElementById('id_TipoEgreso_eliminar').value;

  // Capturar el nombre del tipo de egreso
  var nombreTipoEgreso = document.querySelector(`tr[data-id='${idTipoEgreso}'] td:nth-child(2)`).textContent;

  // Validación para campos vacíos
  if (idTipoEgreso.trim() === "") {
    var mensajeRespuesta = document.getElementById('mensajeRespuesta');
    if (mensajeRespuesta) {
      mensajeRespuesta.textContent = 'El campo de ID de tipo de egreso no puede estar vacío';
    }
    return; // Salir de la función si el campo está vacío
  }

  var xhr = new XMLHttpRequest();
  xhr.open('POST', './componentes/administrador/solicitudes/egreso/egreso_eliminarTipo.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      let mensajeRespuesta = document.getElementById('mensajeRespuesta');
      let mensajeSecundario = document.getElementById('mensajeSecundario');
      if (xhr.responseText === 'success') {
        // Eliminar la fila de la tabla
        var row = document.querySelector(`tr[data-id='${idTipoEgreso}']`);
        if (row) {
          row.remove();
        }

        // Muestra un mensaje de éxito con el nombre del egreso eliminado
        if (mensajeRespuesta) {


          mensajeSecundario.textContent = `Tipo de egreso "${nombreTipoEgreso}" eliminado exitosamente`;
        }

        // Limpia el campo de entrada
        document.getElementById('id_TipoEgreso_eliminar').value = '';

        modalMensaje();
        cerrarModalEliminarEgreso();

      } else {
        // Muestra un mensaje de error si la respuesta no es exitosa
        if (mensajeRespuesta) {
          mensajeRespuesta.textContent = 'Error al eliminar el tipo de egreso';
        }
      }
    }
  };

  xhr.send('id_TipoEgreso=' + encodeURIComponent(idTipoEgreso) + '&accion=eliminar_tipo');
}







/* modal Eliminar Metodo de pago */


/* Modal Eliminar Método de Pago */
function eliminarMetodoPago() {
  var idMetodoPago = document.getElementById('id_tipoOperacion_eliminar').value;

  // Validación para campos vacíos
  if (idMetodoPago.trim() === "") {
    let mensajeRespuesta = document.getElementById('mensajeRespuestaMetodoPago');
    if (mensajeRespuesta) {
      mensajeRespuesta.textContent = 'El campo de ID de método de pago no puede estar vacío';
    }
    return; // Salir de la función si el campo está vacío
  }

  console.log("ID Método Pago:", idMetodoPago); // Para depuración

  let row = document.querySelector(`tr[data-id='${idMetodoPago}']`);
  let nombreMetodoPago = '';

  if (row) {
    nombreMetodoPago = row.querySelector('td:nth-child(2)').textContent;
  } else {
    console.error(`No se encontró una fila con el ID: ${idMetodoPago}`);
    return; // Salir de la función si no se encuentra la fila
  }

  // Proseguir con la llamada Ajax
  let xhr = new XMLHttpRequest();
  xhr.open('POST', './componentes/administrador/solicitudes/metodo_pago/eliminar_metodoPago.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      let mensajeRespuesta = document.getElementById('mensajeRespuestaMetodoPago');
      let mensajeSecundario = document.getElementById('mensajeSecundario');

      if (xhr.responseText === 'success') {
        // Eliminar la fila de la tabla
        row.remove();

        // Eliminar la opción del select
        let select = document.getElementById('tipoOperacion');
        if (select) {
          for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value === idMetodoPago) {
              select.remove(i);
              break;
            }
          }
        }

        // Mostrar el mensaje de "No hay métodos de pago" si la tabla está vacía
        if (document.querySelectorAll('#tablaMostrarMetodoPago tr').length === 0) {
          let noMetodoPagoRow = document.getElementById('noMetodoPagoRow');
          if (noMetodoPagoRow) {
            noMetodoPagoRow.style.display = '';
          }
        }

        // Muestra un mensaje de éxito con el nombre del método de pago eliminado


        alert(`Método de pago "${nombreMetodoPago}" eliminado exitosamente`); // Mensaje de alerta


        // Limpia el campo de entrada
        document.getElementById('id_tipoOperacion_eliminar').value = '';

        // Cierra el modal
        cerrarModalEliminar();

      } else {
        // Muestra un mensaje de error si la respuesta no es exitosa
        if (mensajeRespuesta) {
          mensajeRespuesta.textContent = 'Error al eliminar el método de pago';
        }
      }
    }
  };

  xhr.send('id_tipoOperacion=' + encodeURIComponent(idMetodoPago) + '&accion=eliminar_metodo');
}


/* -- Agregar nuevo banco -- */
/* -- Agregar nuevo banco -- */

function aggNewBanco() {
  const idBanco = document.getElementById('idBanco').value.trim();
  const nombreBanco = document.getElementById('nombreBanco').value.trim();

  // Validación para campos vacíos
  if (!idBanco) {
    alert('El campo "Código del banco" no puede estar vacío');
    return;
  }

  if (!nombreBanco) {
    alert('El campo "Nombre del banco" no puede estar vacío');
    return;
  }

  const xhr = new XMLHttpRequest();
  xhr.open('POST', './componentes/administrador/solicitudes/banco/banco_modal.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        procesarRespuesta(xhr.responseText, idBanco, nombreBanco);
      } else {
        mostrarMensaje('Error en la solicitud');
      }
    }
  };

  xhr.send(`idBanco=${encodeURIComponent(idBanco)}&nombreBanco=${encodeURIComponent(nombreBanco)}&accion=agregar_banco`);
}

function mostrarMensaje(mensaje) {
  document.querySelector('.mensaje_banco').textContent = mensaje;
}

function procesarRespuesta(response, idBanco, nombreBanco) {
  const mensajeBanco = document.querySelector('.mensaje_banco');

  if (!isNaN(response)) {
    updateSelectAndTableBanco(response, idBanco, nombreBanco, 'selectBanco');
    cerrarModal_banco();

    const successMessage = `El banco ${nombreBanco} ha sido agregado con el código ${idBanco}`;
    mostrarMensaje(successMessage);

    // Limpia los campos de entrada
    document.getElementById('idBanco').value = '';
    document.getElementById('nombreBanco').value = '';
  } else {
    mostrarMensaje('Error al agregar el banco');
  }
}

/* -- Agregar nuevo Banco (ACTUALIZA EN TIEMPO REAL LAS TABLAS Y EL SELECT) -- */
function updateSelectAndTableBanco(idNuevoBanco, idBanco, nombreBanco, idSelect2) {
  let select = document.getElementById('banco'); // Asegúrate de que este ID coincida con el ID de tu select

  if (select) {
    let nuevoBanco = document.createElement('option');
    nuevoBanco.value = idNuevoBanco;
    nuevoBanco.textContent = idBanco + " - " + nombreBanco;
    select.appendChild(nuevoBanco);
  } else {
    console.error('El elemento select con id "banco" no existe.');
  }

  // Actualiza el segundo select (de la otra página)
  let select2 = document.getElementById(idSelect2);
  if (select2) {
    let nuevoBanco2 = document.createElement('option');
    nuevoBanco2.value = idNuevoBanco; // Assumimos que el valor será el mismo id
    nuevoBanco2.textContent = idBanco + " - " + nombreBanco;
    select2.appendChild(nuevoBanco2);
  } else {
    console.error('El elemento select con id "' + idSelect2 + '" no existe.');
  }
}

// Crea el nuevo contenedor del banco
let nuevoContenedorBanco = document.createElement('div');
nuevoContenedorBanco.classList.add('bancoShape'); // Asegúrate de que tenga la clase correspondiente
nuevoContenedorBanco.setAttribute('data-id', idNuevoBanco);
nuevoContenedorBanco.innerHTML = `
          <div class="admBancos">
              <img src="./img/bank.svg" alt="" class="bancoImg">
              <div class="bancoTexto">
                  <div class="bancos">
                      <p>Código:</p>
                      <div></div>
                      <p class="inputText" id="cde_banco">${idBanco}</p>
                  </div>
                  <div class="bancos">
                      <p>Banco:</p>
                      <div></div>
                      <p class="inputText" id="bncoNombre" readonly>${nombreBanco}</p>
                  </div>
              </div>
              <div class="editBox">
                  <button type="button" onclick="abrirModalBanco(${idNuevoBanco}, '${nombreBanco}')">Editar</button>
                  <div></div>
                  <button type="button" class="bankDelete" onclick="abrirModalEliminarBanco(${idNuevoBanco}, '${nombreBanco}')">Eliminar</button>
              </div>
          </div>
      `;

// Agregar el nuevo contenedor al contenedor principal de bancos (asegúrate de tener uno)
let contenedorBancos = document.querySelector('.ConsultaBancos');
if (contenedorBancos) {
  contenedorBancos.appendChild(nuevoContenedorBanco);
} else {
  console.error('El contenedor de bancos no existe.');
}




function editNewBanco() {
  let idBancoTable = document.getElementById('id_bancoTable').value;
  let idBancoEditar = document.getElementById('idBancoEditar').value;
  let nombreBancoEditar = document.getElementById('nombreBancoEditar').value;

  // Validación para campos vacíos
  if (idBancoEditar.trim() === "" || nombreBancoEditar.trim() === "") {
    document.getElementById('mensajeRespuestaMetodoPago').textContent = 'Los campos de código y nombre del banco no pueden estar vacíos';
    return; // Salir de la función si un campo está vacío
  }

  let xhr = new XMLHttpRequest();
  xhr.open('POST', './componentes/administrador/solicitudes/banco/editar_banco.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        console.log('Respuesta del servidor:', xhr.responseText);
        if (xhr.responseText.includes("Banco actualizado correctamente")) {
          // Actualiza el contenido del div directamente
          let bancoDiv = document.querySelector('#bancoInfo[data-id="' + idBancoTable + '"]');
          if (bancoDiv) {
            // Mantener el ID original
            bancoDiv.querySelector('.inputText[data-type="id_banco"]').textContent = idBancoEditar; // Actualizar el código
            bancoDiv.querySelector('.inputText[data-type="nombre_banco"]').textContent = nombreBancoEditar; // Actualizar el nombre
            document.getElementById('mensajeBanco_agg').textContent = 'Banco actualizado correctamente';
            cerrarModalBanco();
            modalMensaje_general();
          }
        } else {
          document.getElementById('mensajeRespuestaMetodoPago').textContent = 'Error al actualizar el banco';
        }
      } else {
        document.getElementById('mensajeRespuestaMetodoPago').textContent = 'Error en la solicitud';
      }
    }
  };

  // Cambia el nombre de las variables a las que estás usando en PHP
  xhr.send('id_bancoTable=' + encodeURIComponent(idBancoTable) + '&idBanco=' + encodeURIComponent(idBancoEditar) + '&nombreBanco=' + encodeURIComponent(nombreBancoEditar));
}



function eliminarBanco() {
  let idBancoTable = document.getElementById('id_bancoTable').value;

  let xhr = new XMLHttpRequest();
  xhr.open('POST', './componentes/administrador/solicitudes/banco/eliminar_banco.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        console.log('Respuesta del servidor:', xhr.responseText);
        if (xhr.responseText.includes("Banco eliminado correctamente")) {
          // Elimina el div del banco directamente
          let bancoDiv = document.querySelector('#bancoInfo[data-id="' + idBancoTable + '"]');
          if (bancoDiv) {
            bancoDiv.remove();
            document.getElementById('mensajeBanco_agg').textContent = 'Banco eliminado correctamente';
            cerrarModalEliminarBanco()
            modalMensaje_general();
          }
        } else {
          document.getElementById('mensajeRespuestaMetodoPago').textContent = 'Error al eliminar el banco';
        }
      } else {
        document.getElementById('mensajeRespuestaMetodoPago').textContent = 'Error en la solicitud';
      }
    }
  };

  xhr.send('id_bancoTable=' + encodeURIComponent(idBancoTable));
}

/*----------------------------------------------------*/






/* validad*/

console.log('Archivo scripts.js cargado correctamente');


function validarMDFormulario(formulario) {
  console.log('Validando formulario:', formulario.id);
  const inputs = formulario.querySelectorAll('input, select, textarea');
  let mensajeError = '';

  inputs.forEach(input => {
    if (input.required && input.value === '') {
      mensajeError += `La casilla '${input.name}' está vacía.<br>`;
    }
  });

  if (mensajeError !== '') {
    mostrarModalError(mensajeError);
    return false;
  }

  return true;
}

function validarYEnviarFormulario(formulario, urlDestino) {
  if (validarMDFormulario(formulario)) {
    const datos = new FormData(formulario);
    const xhr = new XMLHttpRequest();

    xhr.open('POST', urlDestino, true);

    xhr.onload = function () {
      console.log('Estado de la respuesta AJAX:', xhr.status);
      if (xhr.status === 200) {
        const respuesta = JSON.parse(xhr.responseText);
        if (respuesta.status === 'success') {
          console.log('Respuesta del servidor:', respuesta.message);
          alert(respuesta.message);
          limpiarFormulario(formulario);
        } else {
          console.error('Error del servidor:', respuesta.message);
          alert('Error del servidor: ' + respuesta.message);
        }
      } else {
        console.error('Error en la solicitud AJAX:', xhr.statusText);
        alert('Error en la solicitud AJAX: ' + xhr.statusText);
      }
    };

    xhr.onerror = function () {
      console.error('Error de red.');
      alert('Error de red. Por favor, verifica tu conexión a Internet.');
    };

    xhr.send(datos);
  }
}

function limpiarFormulario(formulario) {
  formulario.reset(); // Esto restablece todos los campos del formulario
}

function mostrarModalError(mensaje) {
  let mensajeErrorDiv = document.getElementById('mensaje_Error');
  let modal = document.getElementById('modalErrores');

  mensajeErrorDiv.innerHTML = mensaje;
  modal.classList.add('show');

  setTimeout(function () {
    modal.style.display = 'block';
  }, 300);

}

function cerrar_errores() {
  let modal = document.getElementById('modalErrores');
  modal.classList.remove('show');

  setTimeout(function () {
    modal.style.display = 'none';
  }, 300);
}

window.onclick = function (event) {
  const modal = document.getElementById('modalErrores');
  if (event.target === modal) {
    modal.style.display = 'none';
  }
};

// Función para borrar ciertos mensajes de la consola
console.clear = function () {
  const originalClear = console.clear;
  originalClear();
  console.log = function () { };
  console.warn = function () { };
  console.error = function () { };
};


function agregarNuevaCuenta() {
  // Obtener referencias de los elementos del formulario
  const tituloCuenta = document.getElementById("tituloCuenta").value.trim();
  const metodoPago = document.getElementById("selectMetodoPago").value;
  const banco = document.getElementById("banco").value;
  const numeroCuenta = document.getElementById("nro_cuenta").value.trim();
  const tipoCorriente = document.getElementById("tipoCorriente").checked;
  const tipoAhorro = document.getElementById("tipoAhorro").checked;
  const tipoRif = document.getElementById("tipoRif").value;
  const cedulaRif = document.getElementById("cedulaRif").value.trim();
  const telefonoCuenta = document.getElementById("telefonoCuenta").value.trim();
  const infoAdicional = document.getElementById("informacion_adicional").value.trim();

  // Validación básica
  if (!tituloCuenta || !metodoPago || !banco || !numeroCuenta || (!tipoCorriente && !tipoAhorro) || !tipoRif || !cedulaRif || !telefonoCuenta) {
    alert("Por favor, complete todos los campos obligatorios.");
    return;
  }

  // Crear objeto para enviar por AJAX
  const formData = new FormData();
  formData.append('tituloCuenta', tituloCuenta);
  formData.append('metodoPago', metodoPago);
  formData.append('banco', banco);
  formData.append('numeroCuenta', numeroCuenta);
  formData.append('tipoCuenta', tipoCorriente ? 'corriente' : 'ahorro');
  // Se asegura de concatenar solo una vez
  formData.append('cedula_rif', `${cedulaRif}`);
  formData.append('telefonoCuenta', telefonoCuenta);
  formData.append('infoAdicional', infoAdicional);

  // Enviar datos mediante AJAX
  fetch('./componentes/administrador/solicitudes/institucion/agregarCuenta.php', {
    method: 'POST',
    body: formData,
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Cuenta registrada exitosamente.");
        cerrarModal_eggCuenta(); // Cerrar el modal por éxito

        // Recargar la pestaña suavemente
        $('body').fadeOut(300, function () {
          location.reload(); // recarga la página
        });
      } else {
        alert("Error al registrar la cuenta: " + data.message);
      }
    })
    .catch(error => {
      console.error("Ocurrió un error:", error);
      alert("Error en la conexión.");
    });
}




async function fetchCuentas() {
  try {
    const response = await fetch('./componentes/administrador/solicitudes/institucion/obtenerCuentas.php');
    const cuentasActualizadas = await response.json();

    // Mostrar las cuentas actualizadas
    if (Array.isArray(cuentasActualizadas)) {
      // Asegurarse de que las variables sean accesibles
      cuentas = cuentasActualizadas; // Actualiza el estado local
      totalCuentas = cuentas.length; // Actualiza el total de cuentas si es necesario
      currentIndex = Math.min(currentIndex, totalCuentas - 1); // Evitar que currentIndex esté fuera de rango

      // Actualiza la vista de la cuenta
      updateAccountView();
    } else {
      console.error("Error al obtener cuentas:", cuentasActualizadas);
    }
  } catch (error) {
    console.error("Error en la solicitud:", error);
  }
}

async function fetchCuentas() {
  try {
    const response = await fetch('./componentes/administrador/solicitudes/institucion/obtenerCuentas.php');
    const cuentasActualizadas = await response.json();

    // Mostrar las cuentas actualizadas
    if (Array.isArray(cuentasActualizadas)) {
      cuentas = cuentasActualizadas; // Actualiza el estado local
      totalCuentas = cuentas.length; // Actualiza el total de cuentas si es necesario

      // Si el índice actual está fuera del nuevo rango, ajustarlo
      if (currentIndex >= totalCuentas) {
        currentIndex = totalCuentas - 1;
      }

      // Actualiza la vista de la cuenta
      updateAccountView();
    } else {
      console.error("Error al obtener cuentas:", cuentasActualizadas);
    }
  } catch (error) {
    console.error("Error en la solicitud:", error);
  }
}


async function actCuentaInstitucion() {
  // Capturar los valores de los inputs
  const idCuenta = document.getElementById("idCuentaUpdate").value;
  const titular = document.getElementById("tituloCuentaUpdate").value.trim();
  const metodoPago = document.getElementById("selectMetodoPagoUpdate").value;
  const banco = document.getElementById("bancoUpdate").value;
  const nroCuenta = document.getElementById("nro_cuentaUpdate").value.trim();
  const tipoCuentaCorriente = document.getElementById("tipoCorrienteUpdate").checked;
  const tipoCuentaAhorro = document.getElementById("tipoAhorroUpdate").checked;
  const tipoRif = document.getElementById("tipoRifUpdate").value;
  const cedulaRif = document.getElementById("cedulaRifUpdate").value.trim();
  const telefono = document.getElementById("telefonoCuentaUpdate").value.trim();
  const infoAdicional = document.getElementById("informacion_adicionalUpdate").value.trim();

  // Validaciones
  if (!titular || !metodoPago || !banco || !nroCuenta || (!tipoCuentaCorriente && !tipoCuentaAhorro) || !tipoRif || !cedulaRif || !telefono) {
    alert("Por favor, complete todos los campos obligatorios.");
    return;
  }

  // Crear un objeto con los datos a enviar
  const datos = {
    id: idCuenta,
    propietario: titular,
    metodoPago: metodoPago,
    banco: banco,
    numeroCuenta: nroCuenta,
    tipoCuenta: tipoCuentaCorriente ? 'corriente' : 'ahorro',
    cedulaRif: tipoRif + '-' + cedulaRif,
    telefono: telefono,
    informacionAdicional: infoAdicional
  };

  // Enviar los datos al script PHP usando fetch
  try {
    const response = await fetch('./componentes/administrador/solicitudes/institucion/actualizarCuenta.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(datos)
    });

    const result = await response.json();

    if (response.ok) {
      alert(result.message);
      cerrarModal_actCuentaInstitucion();

      // Recargar la pestaña suavemente
      $('body').fadeOut(300, function () {
        location.reload(); // recarga la página
      });
    } else {
      alert(result.message || "Error al actualizar los datos.");
    }
  } catch (error) {
    console.error("Error en la actualización:", error);
    alert("Error en la solicitud de actualización.");
  }
}


// Confirma la eliminación de la cuenta
async function confirmarEliminarCuenta() {
  let idCuenta = document.getElementById("idCuentaEliminar").value;

  try {
    const response = await fetch('./componentes/administrador/solicitudes/institucion/borrarCuenta.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id: idCuenta })
    });

    const result = await response.json();

    if (response.ok) {
      alert(result.message);
      cerrarModal_EliminarCuenta();

      // Recargar la pestaña suavemente
      $('body').fadeOut(300, function () {
        location.reload(); // recarga la página
      });
    } else {
      alert(result.message || "Error al eliminar la cuenta.");
    }
  } catch (error) {
    console.error("Error en la solicitud de eliminación:", error);
    alert("Error en la solicitud de eliminación.");
  }
}

// Función para previsualizar la imagen cargada
function previsualizarImagenRapida(event) {
  const input = event.target; // Obtener el evento del input
  const imagenPrevia = document.getElementById('imagenPrevia'); // Contenedor de la previsualización
  const imagenPreviaSrc = document.getElementById('imagenPreviaSrc'); // Elemento de la imagen

  // Verificamos si hay un archivo seleccionado
  if (input.files && input.files[0]) {
    const reader = new FileReader(); // Creando un objeto FileReader

    // Definir la función de carga del archivo
    reader.onload = function (e) {
      imagenPreviaSrc.src = e.target.result; // Asignar la imagen cargada a la fuente
      imagenPrevia.style.display = 'block'; // Mostrar la previsualización
    };

    // Leer el archivo como URL
    reader.readAsDataURL(input.files[0]); // Esto activará onload
  } else {
    // Si no hay archivo, ocultar la previsualización
    imagenPrevia.style.display = 'none';
    imagenPreviaSrc.src = ''; // Limpiar la fuente de la imagen
  }
}






function validarCampo(campo, mensaje) {
  if (!campo) {
    alert(mensaje);
    return false;
  }
  return true;
}

function validarRif(rifNumero) {

  if (rifNumero.length !== 8 || !/^\d{8}$/.test(rifNumero)) {

    alert("El Rif debe tener 8 caracteres.");
    return false;
  }
  return true;
}


function validarComprobante(comprobante) {
  if (!comprobante) {
    alert("Por favor, suba un comprobante.");
    return false;
  }
  return true;
}

function validarFormulario(event) {
  event.preventDefault(); // Prevenir el envío del formulario hasta que se validen los campos

  // Obtener valores de los campos
  const tipoAporte = document.getElementById('tipoAporte').value;
  const razonSocial = document.getElementById('razonsocial').value;
  const rifNumero = document.getElementById('rif').value;
  const monto = document.getElementById('montoInput').value;
  const fechaPago = document.querySelector('input[name="fechaPagoIngreso"]').value;
  const tipoOperacion = document.getElementById('tipoOperacion').value;
  const banco = document.getElementById('banco').value;
  const concepto = document.getElementById('rConcept').value;
  const comprobante = document.getElementById('comprobante').files[0];

  // Validaciones individuales
  if (!validarCampo(tipoAporte, "Por favor, complete el campo 'Tipo de Aporte'.")) return;
  if (!validarCampo(razonSocial, "Por favor, complete el campo 'Razón Social'.")) return;
  if (!validarCampo(rifNumero, "Por favor, complete el campo 'RIF'.")) return;
  if (!validarCampo(monto, "Por favor, complete el campo 'Monto'.")) return;
  if (!validarCampo(fechaPago, "Por favor, complete el campo 'Fecha de Pago'.")) return;
  if (!validarCampo(tipoOperacion, "Por favor, complete el campo 'Metodo de pago'.")) return;
  if (!validarCampo(banco, "Por favor, complete el campo 'Banco'.")) return;
  if (!validarCampo(concepto, "Por favor, complete el campo 'Concepto'.")) return;

  if (!validarRif(rifNumero)) return;
  if (!validarComprobante(comprobante)) return;

  // Submit del formulario
  document.getElementById('formularioEgreso').submit();
}


function validarFormularioDonacion(event) {
  event.preventDefault(); // Prevenir el envío del formulario hasta que se validen los campos

  // Obtener valores de los campos
  const tipoAporte = document.querySelector('#tipoAporte').value;
  const nombreDonante = document.querySelector('#nombreDonante').value;
  const rifNumero = document.querySelector('#rif').value;
  const monto = document.querySelector('#montoInput').value;
  const fechaPago = document.querySelector('input[name="fechaPagoIngreso"]').value;
  const tipoOperacion = document.querySelector('#tipoOperacion').value;
  const beneficiario = document.querySelector('#beneficiario').value;
  const concepto = document.querySelector('#rConcept').value;
  const comprobante = document.querySelector('#comprobante').files[0];

  // Validaciones individuales
  if (!validarCampo(tipoAporte, "Por favor, complete el campo 'Tipo de Aporte'.")) return;
  if (!validarCampo(nombreDonante, "Por favor, complete el campo 'A nombre de'.")) return;
  if (!validarCampo(rifNumero, "Por favor, complete el campo 'RIF'.")) return;
  if (!validarCampo(monto, "Por favor, complete el campo 'Monto'.")) return;
  if (!validarCampo(fechaPago, "Por favor, complete el campo 'Fecha de Emisión'.")) return;
  if (!validarCampo(tipoOperacion, "Por favor, complete el campo 'Método de Pago'.")) return;
  if (!validarCampo(beneficiario, "Por favor, complete el campo 'Beneficiario'.")) return;
  if (!validarCampo(concepto, "Por favor, complete el campo 'Concepto'.")) return;

  // Comprobaciones adicionales
  if (!validarRif(rifNumero)) return;
  if (!validarComprobante(comprobante)) return;

  // Submit del formulario
  document.querySelector('form#formularioEgreso').submit();
}



/*///////////////////////////*/

function actualizarInfoSistema() {
  const formData = new FormData(document.getElementById('formUpdateSistema'));

  // Validaciones: Solo verifica si hay al menos un campo de texto que ha cambiado y está lleno
  const hasTextFieldDataToUpdate = [
    'nombreSistemaUpdate',
    'tituloSistemaUpdate',
    'subtituloSistemaUpdate'
  ].some(key => formData.get(key));

  // Si no hay campos de texto llenos y no se ha cambiado el logo, solicita que se complete al menos uno.
  if (!hasTextFieldDataToUpdate && !formData.has('logoSistemaInput')) {
    alert('Por favor, complete al menos un campo: Nombre, Título o Subtítulo.');
    return;
  }

  fetch('./componentes/administrador/solicitudes/institucion/actualizarInfoSistema.php', {
    method: 'POST',
    body: formData,
  })
    .then(response => response.json())
    .then(data => {
      // Manejar la respuesta del servidor
      if (data.success) {
        alert('Información actualizada correctamente.');
        cerrarModal_infoSistema();
        location.reload();
      } else {
        alert('Error al actualizar la información: ' + data.message);
      }
    })
    .catch(error => console.error('Error:', error));
}

///////////////////////////////Aqui se envia el correo de registrar usuarios admin, ademas de agregarse el modal de carga.////////////////////////////////////////
/* REGISTRAR AL USUARIO */


function usuarioValidarCamposVacios() {
  const campos = {
    'tipo_usuario': 'Tipo de usuario es obligatorio.',
    'nombre': 'El nombre es obligatorio.',
    'apellido': 'El apellido es obligatorio.',
    'cedula': 'La cédula es obligatoria.',
    'telefono': 'El teléfono es obligatorio.',
    'usuario': 'El nombre de usuario es obligatorio.',
    'estatusLaboral': 'El estatus laboral es obligatorio.',
    'condicionSalud': 'La condición de salud es obligatoria.',
    'estadoCivil': 'El estado civil es obligatorio.',
    'correo': 'El correo electrónico es obligatorio.',
    'clave': 'La contraseña es obligatoria.',
    'pregunta1': 'La primera pregunta de seguridad es obligatoria.',
    'respuesta1': 'La respuesta a la primera pregunta es obligatoria.',
    'pregunta2': 'La segunda pregunta de seguridad es obligatoria.',
    'respuesta2': 'La respuesta a la segunda pregunta es obligatoria.',
    'pregunta3': 'La tercera pregunta de seguridad es obligatoria.',
    'respuesta3': 'La respuesta a la tercera pregunta es obligatoria.'
  };

  return Object.entries(campos)
    .filter(([campo]) => {
      const elemento = document.getElementById(campo);
      return !elemento || !elemento.value.trim();
    })
    .map(([, mensaje]) => mensaje);
}



function usuarioValidarCamposDuplicados(data, callback) {
  const xhr = new XMLHttpRequest();
  xhr.open('POST', './componentes/administrador/validarDuplicados.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function () {
    if (xhr.status === 200) {
      const response = xhr.responseText.split("\n").filter(line => line.trim() !== '');
      callback(response);
    } else if (xhr.status === 204) {
      // Validación exitosa sin errores
      callback([]); // Sin errores
    } else {
      callback(["Error en el servidor, por favor intenta más tarde."]);
    }
  };


  xhr.send(new URLSearchParams(data).toString());
}


// Función para registrar un usuario
async function registrarUsuario() {
  /* === Activación del Modal de Carga === */
  let Loader = document.getElementById('modal_loader');
  Loader.style.display = 'flex';  // Mostrar el modal de carga mientras se procesa
  /* === Fin del Modal de Carga === */

  // Validar campos vacíos
  const mensajesErrores = usuarioValidarCamposVacios();
  if (mensajesErrores.length > 0) {
    // Mostrar errores en el modal
    mostrarErrores(mensajesErrores);
    Loader.style.display = 'none';  // Ocultar el modal de carga
    return; // Detener la ejecución si hay errores
  }

  const form = document.getElementById('registro-form');
  const formData = new FormData(form);  // Formulario con los datos a enviar
  const data = Object.fromEntries(formData);  // Convertir los datos del formulario a un objeto

  // Validar duplicados
  await new Promise((resolve, reject) => {
    usuarioValidarCamposDuplicados(data, (response) => {
      if (response.length > 0) {
        mostrarErrores(response);
        Loader.style.display = 'none';  // Ocultar el modal de carga
        reject(); // Termina la promesa ya que hay errores
      } else {
        resolve(); // Continuamos si no hay errores
      }
    });
  }).catch(() => {
    return; // Si hay errores, simplemente retornamos
  });

  try {
    // Enviar los datos al servidor Node.js para registrar y enviar el correo
    const response = await fetch('https://test-nodejs-fcya.onrender.com/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)  // Enviar los datos como JSON
    });

    const result = await response.json();

    // Si la respuesta de Node.js es exitosa
    if (response.ok) {
      alert(result.message);  // Mostrar el mensaje de éxito de Node.js

      // Enviar los datos al servidor PHP para registrar el usuario en la base de datos
      const phpResponse = await fetch('./componentes/administrador/RegistrarUsuarios.php', {
        method: 'POST',
        body: formData  // Enviar los datos como FormData para ser procesados por PHP
      });

      // Si la respuesta de PHP es exitosa
      if (phpResponse.ok) {
        // Ocultar el modal de carga
        Loader.style.display = 'none';
        // Redirigir al administrador después de registrar correctamente
        window.location.href = './Admin.php';
      } else {
        // Si ocurre un error con PHP
        throw new Error('Error al registrar en la base de datos.');
      }

    } else {
      // Si hay un error al registrar en el servidor Node.js
      throw new Error(result.message);
    }

  } catch (error) {
    // Manejo de errores en cualquiera de las partes del proceso
    console.error('Error al registrar:', error);
    alert('Hubo un error al registrar al usuario.');  // Mostrar mensaje de error

    // Ocultar el modal de carga si ocurre un error
    Loader.style.display = 'none';
  }
}





function mostrarErrores(mensajes) {
  const alertaErrores = document.getElementById('alertaErrores');
  alertaErrores.innerHTML = mensajes.length
    ? '<ul>' + mensajes.map(mensaje => '<li>' + mensaje + '</li>').join('') + '</ul>'
    : '<p>No hay errores que mostrar.</p>';

  document.getElementById('modalUsuarioRegistro').style.display = 'flex'; // Mostrar el modal
}

function abrirPestanaErrores(erroresDetalles) {
  let erroresHTML = '<html><head><title>Detalles de Errores</title></head><body><h1>Detalles de Errores</h1><ul>';
  erroresDetalles.forEach((error) => {
    erroresHTML += '<li>' + error + '</li>';
  });
  erroresHTML += '</ul></body></html>';

  const nuevaVentana = window.open('', '_blank');
  nuevaVentana.document.write(erroresHTML);
  nuevaVentana.document.close(); // Cerrar el documento para que se renderice
}



/////////////////////////////Fin de la funcion./////////////////////////////////////


function cambiarPagina(pagina) {
  var tablaMostrarMetodoPago = document.getElementById('tablaMostrarMetodoPago');

  // Añadir clase para la animación de salida
  tablaMostrarMetodoPago.classList.add('table-leave');

  // Ajustar el tiempo de espera hasta que la animación de salida termine
  setTimeout(function () {
    // Hacer una llamada AJAX a un script PHP que retorna los nuevos datos
    var xhr = new XMLHttpRequest();
    xhr.open('GET', './componentes/administrador/admMetodoPago.php?pagina=' + pagina, true);
    xhr.onload = function () {
      if (this.status === 200) {
        var response = JSON.parse(this.responseText);

        // Actualizar HTML
        tablaMostrarMetodoPago.innerHTML = response.html; // Apuntamos al HTML devuelto

        // Limpiar la clase de salida y hacer que la tabla esté oculta momentáneamente
        tablaMostrarMetodoPago.classList.remove('table-leave');

        // Añadir la clase de entrada
        tablaMostrarMetodoPago.classList.add('table-enter');

        // Eliminar la clase de entrada después de la transición
        setTimeout(function () {
          tablaMostrarMetodoPago.classList.remove('table-enter');
        }, 500); // Duración de la animación de entrada

        // Actualizar la paginación
        document.querySelector('.pagination').innerHTML = response.pagination;
      }
    };

    xhr.send();
  }, 300); // Duración de la animación de salida
}


function previsualizarFirma(event) {
  const imagenFirma = document.getElementById('previsualizacionFirma');
  const archivo = event.target.files[0];

  if (archivo) {
    const lector = new FileReader();
    lector.onload = function (e) {
      imagenFirma.src = e.target.result;
      imagenFirma.style.display = 'block';
    }
    lector.readAsDataURL(archivo);
  } else {
    imagenFirma.src = '';
    imagenFirma.style.display = 'none';
  }
}

function actualizar_infoInstitucion() {
  const razonSocial = document.getElementById('razon_social').value;
  const siglas = document.getElementById('siglas').value;
  const direccion = document.getElementById('direccion').value;
  const rifInstitucion = document.getElementById('rif_institucion').value;
  const telefono = document.getElementById('telefono').value;
  const correo = document.getElementById('correo').value;
  const firmaDigital = document.getElementById('firma_digital').files.length;

  // Validaciones
  if (!razonSocial) {
    alert('El campo "Razón Social" no puede estar vacío.');
    return;
  }
  if (!siglas) {
    alert('El campo "Siglas" no puede estar vacío.');
    return;
  }
  if (!direccion) {
    alert('El campo "Dirección" no puede estar vacío.');
    return;
  }
  if (!rifInstitucion) {
    alert('El campo "RIF" no puede estar vacío.');
    return;
  }
  if (!telefono) {
    alert('El campo "Teléfonos" no puede estar vacío.');
    return;
  }
  if (!correo) {
    alert('El campo "Correo Electrónico" no puede estar vacío.');
    return;
  }
  if (firmaDigital === 0) {
    alert('Por favor, sube la firma digital.');
    return;
  }

  const form = document.getElementById('formUpdateInfo');
  const formData = new FormData(form);

  fetch('./componentes/administrador/solicitudes/institucion/actualizarInfoInstitucion.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.text())
    .then(data => {
      alert(data);
      // Agregar efecto de desvanecimiento antes de recargar
      document.body.style.transition = 'opacity 0.5s';
      document.body.style.opacity = '0';

      // Esperar a que termine la transición antes de recargar
      setTimeout(() => {
        location.reload(); // Recargar la página
      }, 500); // Este tiempo debe coincidir con la duración de la transición
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Ocurrió un error al actualizar la información.');
    });
}



function selectTipoUsuario(value, text) {
  document.getElementById("tipo_usuario").value = value; // Asigna el valor al input oculto
  document.getElementById("btnTipoUsuario").innerText = text; // Cambia el texto del botón
  cerrarModalTipoUsuario(); // Cierra el modal
}

// Cierra el modal cuando se hace clic fuera de él
window.onclick = function (event) {
  let modal = document.getElementById("modalTipoUsuario");
  if (event.target == modal) {
    cerrarModalTipoUsuario();
  }
};



function filterData() {
  const input = document.querySelector('.busquedaHeader').value.toLowerCase();
  const filterBy = document.getElementById('filterBy').value;
  const userType = document.getElementById('userType').value;
  const personas = document.querySelectorAll('.afiliados');

  // Dividir la entrada en partes según los espacios
  const inputParts = input.split(' ');
  const nombreBuscado = inputParts[0] || ''; // Primer parte
  const apellidoBuscado = inputParts.length > 1 ? inputParts.slice(1).join(' ') : ''; // Resto como apellido

  personas.forEach((persona) => {
    const nombre = persona.getAttribute('data-nombre');
    const apellido = persona.getAttribute('data-apellido');
    const correo = persona.getAttribute('data-correo');
    const cedula = persona.getAttribute('data-cedula');
    const tipoUsuario = persona.getAttribute('data-tipo-usuario'); // Asegúrate de tener este atributo en el HTML

    // Convertir nombre completo a minúsculas para la comparación
    const nombreCompleto = (nombre + ' ' + apellido).toLowerCase();

    // Lógica de búsqueda
    let visible = true;

    // Filtro de tipo de usuario
    if (userType && tipoUsuario !== userType) {
      visible = false;
    }

    // Verificar nombre y apellido en ambas combinaciones si aún es visible
    if (visible) {
      if ((nombre.includes(nombreBuscado) && apellido.includes(apellidoBuscado)) ||
        (apellido.includes(nombreBuscado) && nombre.includes(apellidoBuscado))) {
        visible = true;
      } else if (filterBy === 'correo' && correo.includes(input)) {
        visible = true;
      } else if (filterBy === 'cedula' && cedula.includes(input)) {
        visible = true;
      } else if (filterBy === 'nombreCompleto' && nombreCompleto.includes(input)) {
        visible = true;
      } else {
        visible = false;
      }
    }

    // Mostrar u ocultar según el resultado de la búsqueda
    persona.style.display = visible ? "" : "none"; // Mostrar u ocultar
  });
}



/* CAMBIAR ENTRE AFILIADO, DONACION O APORTE PATRONA EN LA PESTAÑA APORTE */
function aporteConsultaTipoChange() {
  var tipo = document.getElementById("tipoAporteSelect").value;

  // Ocultar todas las secciones
  var sections = [
    document.getElementById("aportesAfiliados"),
    document.getElementById("aportesPatronales"),
    document.getElementById("aportesDonaciones")
  ];

  // Aplicar la clase hide para iniciar la transición
  sections.forEach(function (section) {
    section.classList.add('hide');
  });

  // Esperar a que termine la transición de ocultar antes de mostrar la nueva sección
  setTimeout(function () {
    sections.forEach(function (section) {
      section.style.display = "none"; // Ocultar secciones para que no ocupen espacio
    });

    if (tipo === "afiliados") {
      document.getElementById("aportesAfiliados").style.display = "block";
    } else if (tipo === "patronales") {
      document.getElementById("aportesPatronales").style.display = "block";
    } else if (tipo === "donaciones") {
      document.getElementById("aportesDonaciones").style.display = "block";
    } else {
      // Mostrar todas las secciones si se selecciona "General"
      sections.forEach(function (section) {
        section.style.display = "block";
      });
    }

    // Quitar la clase hide para mostrar la nueva sección con transición
    sections.forEach(function (section) {
      section.classList.remove('hide');
    });
  }, 300); // 300 ms coincide con la duración de la transición en CSS
}

// Llama a la función una vez al cargar la página para iniciar con la vista general
window.onload = function () {
  aporteConsultaTipoChange();
}

function aporteConsultaBuscar() {
  const query = document.getElementById("searchInput").value;
  // Implementar la lógica de búsqueda aquí
}

function abrirModal() {
  document.getElementById("modal_MetodoPago").style.display = "block";
}

function cerrarModal() {
  document.getElementById("modal_MetodoPago").style.display = "none";
}


function abrirModalFiltroMes() {
  var tipoAporte = document.getElementById("tipoAporteSelect").value;

  if (!tipoAporte) {
    // Mostrar un mensaje de advertencia si no se ha seleccionado un tipo de aporte
    alert("Primero seleccione el tipo de aporte");
    return; // Salir de la función sin abrir el modal
  }

  document.getElementById("modal_filtroMes").style.display = "block";
}

function cerrarModalFiltroMes() {
  document.getElementById("modal_filtroMes").style.display = "none";
}


function aporteConsultaDescargar(tipo) {
  alert("Descargando en formato: " + tipo);
  // Implementar lógica de descarga aquí
}

function aporteConsultaPaginacion(direction) {
  alert("Cambiando página: " + direction);
  // Implementar lógica de paginación aquí
}


/* paginacion afiliados */
function afiliadosAHcargarMovimientos(offset) {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "./componentes/afiliados/paginarMovimientos.php?offset=" + offset, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.getElementById('formularioMovs').innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}

function afiliadosAHanterior() {
  var limit = 4; // Asumimos que el límite es 4
  var offset = parseInt(document.getElementById('offset').value, 10);
  if (offset >= limit) { // Asegúrate de que no sea menor a 0
    afiliadosAHcargarMovimientos(offset - limit);
    document.getElementById('offset').value = offset - limit; // Actualiza el offset
  }
}

function afiliadosAHsiguiente(totalRows) {
  var limit = 4; // Asumimos que el límite es 4
  var offset = parseInt(document.getElementById('offset').value, 10);
  if (offset + limit < totalRows) {
    afiliadosAHcargarMovimientos(offset + limit);
    document.getElementById('offset').value = offset + limit; // Actualiza el offset
  }
}


/* filtrado por mes aportes */
function filtrarPorMes(isMesFin) {
  let tipo = document.getElementById("tipoAporteSelect").value;
  let mesInicio = parseInt(document.getElementById("mesInicio").value);
  let mesFin = parseInt(document.getElementById("mesFin").value);

  // Verificar que mesFin no sea menor que mesInicio
  if (mesFin < mesInicio) {
    alert("El mes de fin no puede ser menor que el mes de inicio.");
    return; // Salir de la función si la condición se cumple
  }

  // Array de nombres de meses
  let nombresMeses = [
    "Enero", "Febrero", "Marzo", "Abril",
    "Mayo", "Junio", "Julio", "Agosto",
    "Septiembre", "Octubre", "Noviembre", "Diciembre"
  ];

  // Conversión de los meses a sus respectivos nombres
  let nombreMesInicio = nombresMeses[mesInicio - 1]; // Restamos 1 porque el array comienza en 0
  let nombreMesFin = nombresMeses[mesFin - 1];

  let xhr = new XMLHttpRequest();
  let url = `./componentes/administrador/aportesFiltradoMes.php?tipo=${tipo}&mesInicio=${mesInicio}&mesFin=${mesFin}`;

  xhr.open("GET", url, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      if (tipo === 'afiliados') {
        document.getElementById('aportesAfiliados').innerHTML = xhr.responseText;
      } else if (tipo === 'patronales') {
        document.getElementById('aportesPatronales').innerHTML = xhr.responseText;
      } else if (tipo === 'donaciones') {
        document.getElementById('aportesDonaciones').innerHTML = xhr.responseText;
      }

      // Mostrar las fechas con nombres de meses
      document.querySelector('.fechas-seleccionadas').innerHTML = `
              <h4>Filtrado por Fechas</h4>
              <p>Desde: ${nombreMesInicio} hasta: ${nombreMesFin}</p>
          `;

      if (isMesFin) {
        cerrarModalFiltroMes(true); // Cierra el modal solo cuando se selecciona el mes fin
      }
    }
  };

  xhr.send();
}


/* fin filtrado por mes */

/* invitados */

function invitadosAHanterior() {
  var limit = 4;
  var offset = parseInt(document.getElementById('offset').value, 10);
  if (offset >= limit) {
    cargarMovimientos(offset - limit);
    document.getElementById('offset').value = offset - limit;
  }
}

function invitadosAHsiguiente(totalRows) {
  var limit = 4;
  var offset = parseInt(document.getElementById('offset').value, 10);
  if (offset + limit < totalRows) {
    cargarMovimientos(offset + limit);
    document.getElementById('offset').value = offset + limit;
  }
}

function cargarMovimientos(offset) {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "./componentes/invitados/paginarDonaciones.php?offset=" + offset, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.getElementById('formularioMovs').innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}

/* ---------------------------------- */

function afiliados_invitadosFiltrar() {
  let referencia = document.getElementById('busquedaReferencia').value;
  let fecha = document.getElementById('busquedaFecha').value;
  let monto = document.getElementById('busquedaMonto').value;
  let estatus = document.getElementById('busquedaEstatus').value;

  let query = `?referencia=${encodeURIComponent(referencia)}&fecha=${encodeURIComponent(fecha)}&monto=${encodeURIComponent(monto)}&estatus=${encodeURIComponent(estatus)}`;

  let url = './componentes/conexiones/filtrarMovimientos.php' + query;

  fetch(url)
    .then(response => response.text())
    .then(data => {
      document.getElementById('formularioMovs').innerHTML = data;
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

/* invitados filtros */
function invitados_invitadosFiltrar() {
  let referencia = document.getElementById('busquedaReferencia').value;
  let fecha = document.getElementById('busquedaFecha').value;
  let monto = document.getElementById('busquedaMonto').value;
  let estatus = document.getElementById('busquedaEstatus').value;

  let query = `?referencia=${encodeURIComponent(referencia)}&fecha=${encodeURIComponent(fecha)}&monto=${encodeURIComponent(monto)}&estatus=${encodeURIComponent(estatus)}`;

  let url = './componentes/conexiones/filtrarInvitados.php' + query;

  fetch(url)
    .then(response => response.text())
    .then(data => {
      document.getElementById('formularioMovs').innerHTML = data;
    })
    .catch(error => {
      console.error('Error:', error);
    });
}



function cambiarPaginaEgreso(pagina) {
  var tablaMostrar = document.getElementById('tablaMostrar');
  tablaMostrar.classList.add('table-leave');

  setTimeout(function () {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', './repEgreso.php?pagina=' + pagina, true);
    xhr.onload = function () {
      if (this.status === 200) {
        var response = JSON.parse(this.responseText);
        tablaMostrar.innerHTML = response.html;
        tablaMostrar.classList.remove('table-leave');
      }
    };
    xhr.send();
  }, 300);
}

function filtrarTablaEgreso() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#tablaMostrar tr');

  rows.forEach((row) => {
    const tipo = row.cells[2].textContent.toLowerCase();
    const showRow = tipo.includes(searchInput) || searchInput === '';
    row.style.display = showRow ? '' : 'none';
  });
}



/* PERSONAS */

function mostrarModalTipoUsuario() {
  const tipoUsuario = document.getElementById('tipoUsuario').value;
  // Solo se muestra el modal para ingresar a personas si hay una selección
  if (tipoUsuario) {
    document.getElementById('modalBuscarPersona').style.display = 'block';
  }
}




function mostrarCampoBusqueda() {
  const filtro = document.getElementById('filtroBusq').value;
  const cedulaContainer = document.getElementById('cedulaContainer');

  // Mostrar o esconder el campo para la cédula
  if (filtro === "cedula") {
    cedulaContainer.style.display = "block"; // Asegúrate de que este div esté presente en tu HTML
  } else {
    cedulaContainer.style.display = "none";
  }
}



function mostrarCampoBusqueda() {
  const filtro = document.getElementById('filtroBusq').value;
  const cedulaContainer = document.getElementById('cedulaContainer');

  // Mostrar o esconder el campo para la cédula
  cedulaContainer.style.display = filtro === "cedula" ? "block" : "none";
}

function buscarPersona() {
  const filtro = document.getElementById('filtroBusq').value;
  const busqueda = document.getElementById('campoBusqueda').value;
  const cedulaTipo = document.getElementById('tipoRif').value; // Obtener tipo de cédula si se seleccionó

  // Crear un objeto FormData para enviar los datos
  const data = new FormData();
  data.append('filtro', filtro);
  data.append('busqueda', busqueda);
  if (filtro === 'cedula') {
    data.append('cedulaTipo', cedulaTipo);
  }

  // Lógica AJAX para buscar
  fetch('./componentes/administrador/buscarPersona.php', {
    method: 'POST',
    body: data
  })
    .then(response => response.text())
    .then(data => {
      // Procesar los datos recibidos y mostrarlos en resultadoBusqueda
      document.getElementById('resultadoBusqueda').innerHTML = data;
    })
    .catch(error => {
      console.error('Error:', error);
      document.getElementById('resultadoBusqueda').innerHTML = 'Error en la búsqueda.';
    });
}

function ManualUsuarioAdmin() {
  window.location.href = './componentes/manuales_pdf/Manual de  usuarios (sacips).pdf';
}

function ManualSistemaAdmin() {
  window.location.href = './componentes/manuales_pdf/Manual de Sistema (sacips).pdf';
}

function ManualUsuarioAfi_Invi() {
  window.location.href = './componentes/manuales_pdf/Manual de  usuarios Afiliados e invitados (sacips).pdf';
}
