var password_insana = 'Admin123';
const boton = document.getElementById('boton');

function click_boton(event) {
    event.preventDefault();

    const input_nombre = document.getElementById('nombre').value;
    const input_contraseña = document.getElementById('password').value;

    let encontrado = false;
    
    for (let i = 0; i < usuario.length; i++) {
        if (input_nombre !== '' && input_contraseña !== '') {
            if (input_nombre === usuario[i]) {
                encontrado = true; // Usuario encontrado
                if (input_contraseña === password_insana && clave[i] === 'nulo') {
                    document.getElementById('id_usuario').value = id_usuario[i];
                    document.cookie = "User_ID=" + id_usuario[i];
                    document.getElementById('formAdmin').submit();
                } else if (input_contraseña === clave[i]) {
                    document.getElementById('id_usuario').value = id_usuario[i];
                    document.cookie = "User_ID=" + id_usuario[i];
                    document.getElementById('formAdmin').submit();
                } else {
                    document.getElementById('aviso').textContent = 'Contraseña Incorrecta';
                }
                break; // Salir del bucle una vez encontrado
            }
        }
    }
    if (!encontrado) {
        document.getElementById('aviso').textContent = 'Usuario no Registrado';
    }
}


/*if (input_nombre != '' && input_contraseña != '') {
		if (input_nombre != nombre_afiliado && input_nombre != nombre_usuario && input_nombre != Super_Mega_Recontra_Usuario && input_nombre != "") {
			const alerta_nombre = document.getElementById('aviso-2');
			document.getElementById('nombre').focus();
			alerta_nombre.style.display = 'block';
			document.getElementById('aviso-1').style.display = 'none';
		} else if (input_nombre == nombre_afiliado) {
			if (input_contraseña == password_af) {
				sessionStorage.setItem('Programador', 'Yes');
				window.location.href = "./afiliados.html";
			} else if (input_contraseña != password_af) {
				const alerta_contraseña = document.getElementById('aviso-1');
				alerta_contraseña.style.display = 'block';
				document.getElementById('aviso-2').style.display = 'none';
				document.getElementById('password').focus();
			}
		} else if (input_nombre == nombre_usuario) {
			if (input_contraseña == contraseña) {
				sessionStorage.setItem('Programador', 'nope');
				window.location.href = "./Admin.html";
			} else if (input_contraseña != contraseña) {
				const alerta_contraseña = document.getElementById('aviso-1');
				alerta_contraseña.style.display = 'block';
				document.getElementById('aviso-2').style.display = 'none';
				document.getElementById('password').focus();
			}
		} else if (input_nombre == Super_Mega_Recontra_Usuario) {
			if (input_contraseña == password_insana) {
				sessionStorage.setItem('Programador', 'Yes');
				window.location.href = "./Admin.html";
			} else if (input_contraseña != password_insana) {
				const alerta_contraseña = document.getElementById('aviso-1');
				alerta_contraseña.style.display = 'block';
				document.getElementById('aviso-2').style.display = 'none';
				document.getElementById('password').focus();
			}
		}
	}
*/