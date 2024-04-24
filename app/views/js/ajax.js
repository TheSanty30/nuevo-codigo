const formularios_ajax = document.querySelectorAll(".FormularioAjax");

formularios_ajax.forEach(formulario => {
    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        Swal.fire({
            title: "¿Estás seguro?",
            text: "¡Quieres realizar la acción solicitada!",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Si, realizar!",
            cancelButtonText: "Cancelar!"
        }).then((result) => {
            if (result.isConfirmed) {
                let data = new FormData(this);
                let method = this.getAttribute("method");
                let action = this.getAttribute("action");

                let config = {
                    method: method,
                    body: data
                };

                fetch(action, config)
                    .then(respuesta => respuesta.json())
                    .then(respuesta => {
                        return alertas_ajax(respuesta);
                    })
            }
        });

    });
});

function alertas_ajax(alerta) {
    switch (alerta.tipo) {
        case "simple":
            Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.text,
                confirmButtonText: 'Aceptar'
            });
            break;
        case "recargar":
            Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.text,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
            break;
        case "limpiar":
            Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.text,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var formularios = document.querySelectorAll(".FormularioAjax");
                    formularios.forEach(function (formulario) {
                        formulario.reset();
                    });
                }
            });
            break;
        case "redireccionar":
            window.location.href = alerta.url;
            break;
        default:
            console.error("Tipo de alerta no reconocido");
    }
}

const login_form = document.querySelector(".frmLogin");

login_form.addEventListener("submit", function (e) {
    e.preventDefault();
    let data = new FormData(this);
    let method = this.getAttribute("method");
    let action = this.getAttribute("action");

    let config = {
        method: method,
        body: data
    };

    fetch(action, config)
        .then(respuesta => respuesta.json())
        .then(respuesta => {
            switch (respuesta.success) {
            case true:
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: respuesta.message,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    if (respuesta.redirect) {
                        window.location.href = respuesta.redirect;
                    }
                });
                break;
            case false:
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: respuesta.message,
                    confirmButtonText: 'Aceptar'
                });
                break;
            default:
                // Manejar otro caso si es necesario
                break;
        }
        });
});


/*let btn_exit = document.getElementById("btn_exit");

btn_exit.addEventListener("click", function(e){
    e.defaultPrevented();

    Swal.fire({
        title: "¿Quieres salir del sistema?",
        text: "La sesión actual se cerrará",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, salir!",
        cancelButtonText: "Cancelar!"
    }).then((result) => {
        if (result.isConfirmed) {
           let url = this.getAttribute("href");
           window.location.href = url;
        }
    });

});*/