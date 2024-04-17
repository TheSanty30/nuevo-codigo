const formularios_ajax = document.querySelectorAll(".FormularioAjax");

formularios_ajax.forEach(formularios => {

    formularios.addEventListener("submit", function (e) {
        e.preventDefault();

        Swal.fire({
            title: "¿Estás seguro?",
            text: "¡Quieres realizar la acción solicitada!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, realizar!",
            cancelButtonText: "No, cancelar!"
        }).then((result) => {
            if (result.isConfirmed) {
                /*Swal.fire({
                  title: "Deleted!",
                  text: "Your file has been deleted.",
                  icon: "success"
                });*/
                let data = new FormData(this);
                let method = this.getAttribute("method");
                let action = this.getAttribute("action");

                let encabezados = new Headers();

                let config = {
                    method: method,
                    headers: encabezados,
                    mode: 'cors',
                    cache: 'no-cache',
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
})


function alertas_ajax(alerta) {
    if (alerta.tipo == "simple") {
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.text,
            confirmButtonText: 'Aceptar'
        });
    } else if (alerta.tipo == "recargar") {
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
    }
    else if (alerta.tipo == "limpiar") {
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.text,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                var form = document.querySelectorAll(".FormularioAjax");
                formularios.forEach(function(formulario) {
                    formulario.reset();
                });
                //document.querySelectorAll(".FormularioAjax").reset();
            }
        });
    }
    else if(alert.tipo == "redireccionar"){
        window.location.href = alerta.url;
    }
}

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