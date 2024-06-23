
function validarRut(rutCompleto) {
    console.log(rutCompleto);
    if (!/^[0-9]+[-|‐]{1}[0-9kK]{1}$/.test(rutCompleto)) {
        return false;
    }

    var tmp = rutCompleto.split('-');
    var digv = tmp[1];
    var rut = tmp[0];
    if (digv == 'K') digv = 'k';

    suma = 0;
    n = 2;
    rut.split("").reverse().forEach(function (digito, index) {

        suma += digito * n;
        n = n + 1;
        if (n == 8) {
            n = 2;
        }
    });
    suma = suma % 11;
    dv = 11 - suma;
    if (dv == 10) {
        dv = 'k';
    }
    if (dv == 11) {
        dv = 0;
    }
    if (dv == digv) {
        return true;
    }
    return false;
}





function validarCampo(id, value, tipo) {
    let mensajeError;
    let patron;

    switch (tipo) {
        case "login":
            if (id === "email") {
                mensajeError = "Formato de usuario inválido";
                patron = /^[a-zA-Z0-9]{3,}@[a-zA-Z0-9]{3,}\.[a-zA-Z0-9]{1,3}$/;
            } else if (id === "password") {
                mensajeError = "Formato de contraseña inválido";
                patron = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&/\\-])[a-zA-Z\d@$!%*?&/\\-]{4,16}$/;
            }
            break;
        case "prestamo":
            if (id === "rut") {
                mensajeError = "El rut no es válido";
                if (!validarRut(value)) {
                    return mensajeError;
                } else {
                    return null;
                }
            } else if (id === "nombre" || id === "apaterno" || id === "amaterno") {
                mensajeError = "Sólo puede ingresar letras";
                patron = /^[a-zA-Z]+$/;
            } else if (id === "tipo-usuario") {
                agregarFechaPrestamo();
                mensajeError = "Debe seleccionar un tipo de usuario";
                patron = /^[a-zA-Z]+$/;
            }
            break;
        case "mantenedor":
            if (id === "autor" || id === "editorial") {
                mensajeError = "Sólo puede ingresar letras";
                patron = /^[a-zA-Z]+$/;
            } else if (id === "dia") {
                mensajeError = "El día debe ser de 1 a 31";
                patron = /^(?:[1-9]|[12]\d|3[01])$/;
            } else if (id === "mes") {
                mensajeError = "Debe seleccionar un mes";
                if (value == 'undefined') {
                    return mensajeError;
                }
            } else if (id === "anio") {
                mensajeError = "El año debe ser de 1900 a 2021";
                patron = /^(19[0-9][0-9]|20[0-1][0-9]|2020|2021)$/;
            }
            break;
    }

    return !patron.test(value) ? mensajeError : null;
}

function validarCampos(ids, tipo) {
    ids.forEach(function (id) {
        let input = document.getElementById(id);

        input.addEventListener("blur", function (e) {
            let texto = e.target.value;
            let campo = e.target;
            let mensajeError = validarCampo(id, texto, tipo);
            let sel = campo.parentElement.querySelectorAll('#mensaje-vacio');
            let sel2 = campo.parentElement.querySelectorAll('#mensaje-error');
            let mensajeVacio = '<div class="alert alert-danger" role="alert" id="mensaje-vacio"> Error. El campo no debe quedar vacío </div>';


            if (texto.length === 0 && sel.length === 0) {
                campo.parentElement.insertAdjacentHTML("beforeend", mensajeVacio);
            }
            else if (mensajeError && sel2.length === 0 && texto.length > 0) {
                let mensajeErrorHTML = `<div class="alert alert-secondary" role="alert" id="mensaje-error">${mensajeError}</div>`;
                campo.parentElement.insertAdjacentHTML("beforeend", mensajeErrorHTML);
            }
            if (texto.length > 0 && sel.length > 0) {
                sel[0].remove();
            }
            if (!mensajeError && sel2.length > 0) {
                sel2[0].remove();
            }
        });
    });
}

function validarPagina() {
    const titulo = document.title;
    let ids = [];

    switch (titulo) {
        case PAGE_TITLES.LOGIN:
            ids = ["email", "password"];
            validarCampos(ids, "login");
            break;
        case PAGE_TITLES.PRESTAMO:
            ids = ["rut", "nombre", "apaterno", "amaterno", "tipo-usuario"];
            validarCampos(ids, "prestamo");
            break;
        case PAGE_TITLES.MANTENEDOR:
            ids = ["autor", "editorial", "dia", "mes", "anio"];
            validarCampos(ids, "mantenedor");
            break;
    }
}





function GenerarRut() {


    suma = 0;
    n = 2;

    var rut = (Math.floor(Math.random() * (23000000 - 18500000 + 1)) + 18500000).toString();
    rut.split("").reverse().forEach(function (digito, index) {

        suma += digito * n;
        n = n + 1;
        if (n == 8) {
            n = 2;
        }
    });
    suma = suma % 11;
    dv = 11 - suma;
    if (dv == 10) {
        dv = 'k';
    }
    if (dv == 11) {
        dv = 0;
    }

    return rut + "-" + dv;
}


function agregarAlerta(tipo, mensaje) {
    var alerta = document.createElement("div");
    alerta.className = 'alert ' + tipo;
    alerta.innerText = mensaje;

    var contenedor = document.querySelector(".alerts");
    contenedor.appendChild(alerta);
  
    setTimeout(function() {
      alerta.remove();
    }, 3000);
  }

