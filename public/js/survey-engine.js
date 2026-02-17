$(document).ready(function () {
    const element = document.getElementById('survey-app');
    if (!element) return;

    const rawBanco = element.getAttribute('data-banco');
    const bancoParsed = JSON.parse(rawBanco);
    const banco = {};
    
    // Mapeo seguro de claves
    Object.keys(bancoParsed).forEach(key => {
        banco[parseInt(key)] = bancoParsed[key];
    });

    // VARIABLES DE ESTADO
    let respuestas = {};
    let preguntaActual = 1; // Iniciamos en 1 para el flujo completo
    let historial = [];

    // --- FUNCIÓN MAESTRA DE RENDERIZADO ---
    // --- FUNCIÓN MAESTRA DE RENDERIZADO (VERSIÓN FINAL INTEGRADA) ---
    function renderPregunta(id) {
    id = parseInt(id);

    // 1. VALIDACIÓN FINAL: Si no existe el ID o es el fin, disparamos el guardado
    if (!banco[id] || banco[id].tipo === 'fin') {
        return finalizarEncuesta();
    }

    const data = banco[id];

    // 2. LÓGICA CONDICIONAL: El Filtro para saltar preguntas no aplicables
    if (data.condicion) {
        const idOrigen = data.condicion.origen;
        const valorRequerido = data.condicion.valor;
        const respuestaPrevia = respuestas[idOrigen]; 
        let cumple = false;

        if (Array.isArray(respuestaPrevia)) {
            // Si es checkbox, buscamos si el valor está en el array de objetos
            cumple = respuestaPrevia.some(item => item.value === valorRequerido);
        } else if (typeof respuestaPrevia === 'string' && respuestaPrevia === valorRequerido) {
            cumple = true;
        } else if (respuestaPrevia && respuestaPrevia.value === valorRequerido) {
            cumple = true;
        }

        // Si NO cumple la condición, saltamos automáticamente a la siguiente
        if (!cumple) {
            console.log(`Saltando pantalla ${id}: Condición no cumplida.`);
            renderPregunta(data.saltaA);
            return; 
        }
    }

    const contenedor = $(".card-body");

    // 3. EFECTO VISUAL: Transición de salida
    contenedor.fadeOut(200, function() {
        contenedor.empty();

        // 4. CABECERA: Título y Subtítulo
        let html = `<h2 class="titulo-login">${data.pregunta}</h2>`;
        if(data.subtitulo) html += `<span class="subtitulo">${data.subtitulo}</span>`;
        contenedor.append(html);

        // 5. CREACIÓN DEL FORMULARIO BASE
        // 🔥 FIX QUIRÚRGICO: Siempre creamos y añadimos el form al contenedor primero.
        // Esto garantiza que cuando Leaflet intente inicializarse, el div ya exista en el DOM.
        let form = $(`<form id="form-encuesta" style="text-align:left; margin-top:20px;"></form>`);
        contenedor.append(form);
        
        // 6. RENDERIZADO POR TIPO DE PANTALLA
        if (data.tipo === 'seleccion') {
            renderSeleccion(data, form);
        } else if (data.tipo === 'formulario') {
            renderFormulario(data, form);
        } else if (data.tipo === 'coordenadas') {
            renderMapaGPS(data, form); // Inyecta el HTML del mapa y las coordenadas
        }

        // 7. BOTONES DE NAVEGACIÓN
        let botonesHtml = $(`<div class="botones-navegacion"></div>`);

        // Botón Atrás
        if (historial.length > 0) {
            let btnAtras = $(`<button type="button" class="btn-atras">Atrás</button>`);
            btnAtras.on('click', function() { regresar(); });
            botonesHtml.append(btnAtras);
        } else {
            botonesHtml.append(`<div></div>`); // Espaciador visual
        }

        // Botón Siguiente (No se muestra en 'seleccion' porque los botones de opción ya disparan el avance)
        if (data.tipo !== 'seleccion') {
            let txtBtn = data.boton_texto || 'Siguiente';
            let btnSig = $(`<button type="button" class="btn-encuesta">${txtBtn}</button>`);
            btnSig.on('click', function(e) {
                e.preventDefault();
                validarYSiguiente(id, data.saltaA);
            });
            botonesHtml.append(btnSig);
        }

        // Agregamos botones al formulario
        form.append(botonesHtml);

        // 8. DISPARADORES Y AUTO-LLENADOS ESPECIALES
        
        // Paso 1: Inyectar nombre del Técnico de la sesión
        if (id === 1) { 
            setTimeout(() => {
                $('input[name="tecnico_nombre"]')
                    .val(typeof TECNICO_LOGUEADO !== 'undefined' ? TECNICO_LOGUEADO : '')
                    .prop('readonly', true)
                    .css('background', '#f4f4f4');
            }, 100);
        }
        
        // Paso 5: Cargar Preview de Colonias de Tlalpan
        if (id === 5) {
            setTimeout(() => {
                const containerCol = $('[data-name="pueblo_colonia"]');
                if (typeof COLONIAS_PREVIEW !== 'undefined') {
                    renderListaColonias(COLONIAS_PREVIEW, containerCol);
                }
            }, 150);
        }

        // 9. FINALIZACIÓN DE CICLO
        actualizarProgreso(id);
        contenedor.fadeIn(300);
        window.scrollTo(0, 0); // Regresar arriba al cambiar de pantalla
    });
}
/**
 * Renderiza opciones tipo botón que disparan el avance automático
 * @param {Object} data - Objeto de la pregunta actual del banco
 * @param {jQuery} form - El contenedor del formulario donde se inyectarán los botones
 */
function renderSeleccion(data, form) {
    // 1. Creamos un contenedor específico para las opciones
    let divOpciones = $('<div id="opciones-container" style="display: flex; flex-direction: column; gap: 10px;"></div>');

    // 2. Iteramos sobre las opciones definidas en tu PreguntaModel.php
    data.opciones.forEach(opt => {
        // Creamos el botón con el estilo guinda de la Alcaldía
        let btn = $('<button type="button" class="btn-guinda">')
            .text(opt.texto)
            .css({
                'margin-bottom': '5px',
                'padding': '15px',
                'font-size': '1.1rem'
            });

        // 3. LÓGICA DE CLIC: Guarda y salta de inmediato
        btn.on('click', function (e) {
            e.preventDefault();

            // Guardamos el valor seleccionado en nuestro objeto global de respuestas
            respuestas[data.id] = opt.val; 

            // Registramos este paso en el historial para que el botón "Atrás" funcione
            historial.push(data.id);

            console.log(`Seleccionado en Paso ${data.id}: ${opt.val}`);

            // Disparamos el renderizado de la siguiente pantalla (definida en 'saltaA')
            renderPregunta(opt.saltaA);
        });

        divOpciones.append(btn);
    });

    // 4. Inyectamos los botones en el formulario
    form.append(divOpciones);
}
    function renderFormulario(data, form) {
        data.campos.forEach(campo => {
            let depData = campo.dependencia ? `data-depende-de="${campo.dependencia.padre}" data-valor-req="${campo.dependencia.valor}"` : '';
            let divCampo = $(`<div class="campo-wrapper" ${depData} style="margin-bottom:20px;"></div>`);
            
            let label = `<label class="label-input">${campo.label}</label>`;
            let input = "";
            let valorDefecto = "";
            let readonlyAttr = campo.readonly ? "readonly" : "";
            let estiloCenizo = campo.readonly ? 'style="background-color: #f4f4f4;"' : '';

            if (campo.name === 'folio') valorDefecto = typeof FOLIO_AUTO !== 'undefined' ? FOLIO_AUTO : '';
            if (campo.name === 'nombre_productor') valorDefecto = typeof TECNICO_LOGUEADO !== 'undefined' ? TECNICO_LOGUEADO : '';

            if (['text', 'date', 'tel', 'email'].includes(campo.tipo)) {
                let maxAttr = campo.tipo === 'tel' ? 'maxlength="10"' : '';
                input = `<input type="${campo.tipo}" name="${campo.name}" value="${valorDefecto}" class="input-redondo" placeholder="${campo.placeholder || ''}" ${readonlyAttr} ${estiloCenizo} ${maxAttr} required>`;
            } 
            else if (campo.tipo === 'select') {
                input = `<select name="${campo.name}" class="input-redondo" required><option value="">Seleccione...</option>`;
                if(campo.opciones) campo.opciones.forEach(opt => { input += `<option value="${opt.val}">${opt.texto}</option>`; });
                input += `</select>`;
            }
            else if (campo.tipo === 'radio') {
                // Creamos el contenedor con una clase específica para encontrarlo fácil
                let radioGroup = $(`<div class="radio-group-container" data-name="${campo.name}"></div>`);
                
                if (campo.opciones && campo.opciones.length > 0) {
                    campo.opciones.forEach(opt => {
                        radioGroup.append(`
                            <label class="radio-custom">
                                <input type="radio" name="${campo.name}" value="${opt.val}" required>
                                <span class="radio-checkmark"></span> ${opt.texto}
                            </label>`);
                    });
                }
                input = radioGroup;
            }
            else if (campo.tipo === 'checkbox') {
                let checkGroup = $('<div class="radio-group-container" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">');
                if(campo.opciones) campo.opciones.forEach(opt => {
                    checkGroup.append(`<label class="checkbox-custom"><input type="checkbox" name="${campo.name}[]" value="${opt.val}"><span class="checkbox-checkmark"></span> ${opt.texto}</label>`);
                });
                input = checkGroup;
            }
            else if (campo.tipo === 'html') {
                divCampo.html(campo.label);
                form.append(divCampo);
                return;
            }

            divCampo.append(label).append(input);
            form.append(divCampo);
        });
        evaluarDependenciasInternas();
    }

  function renderMapaGPS(data, contenedor) {
        let layout = $(`
            <div class="map-container-fluid">
                <div class="coords-header" style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label class="label-input" style="color: var(--guinda); font-weight: bold;">Latitud</label>
                        <input type="text" id="lat" name="latitud" class="input-redondo" readonly 
                            style="background:#f8f9fa; border: 1px solid #ddd; cursor:not-allowed; font-weight: 600;">
                    </div>
                    <div style="flex: 1;">
                        <label class="label-input" style="color: var(--guinda); font-weight: bold;">Longitud</label>
                        <input type="text" id="lon" name="longitud" class="input-redondo" readonly 
                            style="background:#f8f9fa; border: 1px solid #ddd; cursor:not-allowed; font-weight: 600;">
                    </div>
                </div>

                <div class="mapa-wrapper" style="border: 2px solid var(--guinda); border-radius:15px; overflow:hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div id="mapa-interactivo" style="width:100%; height:450px; background: #eee;"></div>
                </div>
                
                <p style="font-size:12px; color:#666; margin-top:10px; font-style: italic;">
                    <i class="fa-solid fa-magic"></i> La dirección se actualizará automáticamente al mover el marcador.
                </p>
            </div>

            <div style="margin-top:25px; padding-top: 15px; border-top: 1px solid #eee;">
                <label class="label-input" style="font-weight: bold;">Dirección Detectada (Calle y Número)</label>
                <input type="text" id="calle" name="calle_numero" class="input-redondo" 
                    placeholder="Obteniendo dirección..." style="background: #fff8f8; border-color: var(--guinda);" required>
            </div>
        `);
        
        contenedor.append(layout);

        setTimeout(() => {
            if (window.currentMap) { window.currentMap.remove(); }

            window.currentMap = L.map('mapa-interactivo').setView([19.289, -99.167], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(window.currentMap);

            let marker;

            // --- FUNCIÓN NÚCLEO: GEOCODIFICACIÓN INVERSA ---
            function reverseGeocode(lat, lon) {
                $("#calle").val("Buscando con Photon...");

                // Photon es excelente porque no pide tokens y es muy veloz
                fetch(`https://photon.komoot.io/reverse?lon=${lon}&lat=${lat}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.features && data.features.length > 0) {
                            const f = data.features[0].properties;
                            // Photon separa muy bien los campos
                            const calle = f.name || f.street || "Calle no identificada";
                            const numero = f.housenumber || "S/N";
                            const colonia = f.district || f.locality || "";
                            
                            $("#calle").val(`${calle} ${numero}, ${colonia}`);
                            $("#lat").val(lat.toFixed(7));
                            $("#lon").val(lon.toFixed(7));
                        } else {
                            $("#calle").val("Sin datos en esta zona");
                        }
                    })
                    .catch(error => {
                        console.error("Error en Photon:", error);
                        $("#calle").val("Error de conexión");
                    });
            }

            // CAPTURA GPS INICIAL
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(pos => {
                    const { latitude, longitude } = pos.coords;
                    window.currentMap.setView([latitude, longitude], 18);

                    marker = L.marker([latitude, longitude], { draggable: true }).addTo(window.currentMap);
                    
                    // Disparo inicial
                    reverseGeocode(latitude, longitude);

                    // Disparo al terminar de arrastrar
                    marker.on('dragend', function() {
                        const position = marker.getLatLng();
                        reverseGeocode(position.lat, position.lng);
                    });

                }, () => alert("GPS desactivado."), { enableHighAccuracy: true });
            }

            // Click en el mapa para mover marcador y obtener dirección
            window.currentMap.on('click', function(e) {
                const { lat, lng } = e.latlng;
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng], { draggable: true }).addTo(window.currentMap);
                }
                reverseGeocode(lat, lng);
            });

        }, 500);
    }
    // --- MOTORES DE EVENTOS GLOBALES ---

    // 1. CURP Automático
    $(document).on('blur', 'input[name="curp"]', function() {
        let curp = $(this).val().toUpperCase();
        if (curp.length >= 10) {
            let yy = curp.substring(4, 6), mm = curp.substring(6, 8), dd = curp.substring(8, 10);
            let siglo = (parseInt(yy) < 30) ? '20' : '19';
            $('input[name="fecha_nacimiento"]').val(`${siglo}${yy}-${mm}-${dd}`).css('background', '#eee');
            let letraSexo = curp.substring(10, 11);
            $(`input[name="sexo"][value="${letraSexo === 'H' ? 'HOMBRE' : 'MUJER'}"]`).prop('checked', true);
        }
    });

    // 2. CP y Colonias
    $(document).on('keyup', 'input[name="cp"]', function() {
        let cp = $(this).val();
        const container = $('[data-name="pueblo_colonia"]'); // Usando el data-name que definimos

        // Si borran el CP, regresamos a la PREVIEW de 10
        if (cp.length === 0) {
            renderListaColonias(typeof COLONIAS_PREVIEW !== 'undefined' ? COLONIAS_PREVIEW : [], container);
            return;
        }

        // Al llegar a 5 dígitos, filtramos
        if (cp.length === 5) {
            container.html('<p style="color:var(--guinda); font-size:12px;"><i class="fas fa-spinner fa-spin"></i> Filtrando catálogo...</p>');
            fetch(`${URLROOT}/Encuesta/buscarColonias/${cp}`)
                .then(res => res.json())
                .then(data => renderListaColonias(data, container, cp));
        }
    });

    function renderListaColonias(data, container, cpInput = "") {
        if (!container || container.length === 0) return;
        container.empty();

        if (data && data.length > 0) {
            data.forEach(col => {
                // 🔥 Guardamos el CP en el atributo 'data-cp'
                container.append(`
                    <label class="radio-custom">
                        <input type="radio" name="pueblo_colonia" 
                            value="${col.asentamiento}" 
                            data-cp="${col.codigo_postal}" required>
                        <span class="radio-checkmark"></span> ${col.asentamiento}
                    </label>`);
            });
        } else if (cpInput.length === 5) {
            container.append('<p style="color:#c0392b; font-size:12px; margin-bottom:5px;">CP no encontrado. Por favor use "OTRO".</p>');
        }

        // 🔥 EL BOTÓN "OTRO" ES EL ANCLA: SIEMPRE EXISTE
        container.append(`
            <hr style="margin:10px 0; border-top: 1px solid #eee;">
            <label class="radio-custom">
                <input type="radio" name="pueblo_colonia" value="OTRO" required>
                <span class="radio-checkmark"></span> 
                <strong>OTRA COLONIA (No está en la lista)</strong>
            </label>
        `);

        // Sincronizamos con el sistema de visibilidad
        evaluarDependenciasInternas();
    }
    // 3. Dependencias
    function evaluarDependenciasInternas() {
        $('[data-depende-de]').each(function() {
            const cont = $(this), padre = cont.data('depende-de'), req = cont.data('valor-req');
            let actual = $(`[name="${padre}"]:checked`).val() || $(`[name="${padre}"]`).val();
            if (actual === req) {
                cont.fadeIn(200); cont.find('input, select').prop('disabled', false).attr('required', true);
            } else {
                cont.fadeOut(200); cont.find('input, select').prop('disabled', true).removeAttr('required');
            }
        });
    }

    $(document).on('change', '#form-encuesta input, #form-encuesta select', evaluarDependenciasInternas);
    $(document).on('change', 'input[name="pueblo_colonia"]', function() {
        const cpAsociado = $(this).data('cp');
        
        // Si la colonia tiene un CP asociado (es decir, no es "OTRO")
        if (cpAsociado) {
            $('input[name="cp"]').val(cpAsociado);
        }
    });
    // --- FUNCIONES DE NAVEGACIÓN ---
    function validarYSiguiente(idActual, idSiguiente) {
        const form = document.getElementById('form-encuesta');
        if (form && !form.checkValidity()) { form.reportValidity(); return; }
        
        if (form) respuestas[idActual] = $(form).serializeArray();
        if ($("#lat").length > 0) {
            respuestas[idActual] = { latitud: $("#lat").val(), longitud: $("#lon").val(), calle_numero: $("#calle").val() };
        }

        historial.push(idActual);
        preguntaActual = idSiguiente;
        renderPregunta(idSiguiente);
    }

    function regresar() {
        if (historial.length === 0) return;
        renderPregunta(historial.pop());
    }

    function actualizarProgreso(id) {
        let total = Object.keys(banco).length - 1;
        $("#progress-bar").css("width", (id / total * 100) + "%");
    }

    function finalizarEncuesta() {
        $(".card-body").html('<div style="text-align:center; padding:40px;"><i class="fa-solid fa-circle-notch fa-spin" style="font-size:40px; color:var(--guinda);"></i><h3>Guardando...</h3></div>');
        fetch(`${URLROOT}/Encuesta/guardar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(respuestas)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                $(".card-body").html(`<div style="text-align:center; padding:20px;"><i class="fa-solid fa-check-circle" style="font-size:50px; color:#27ae60;"></i><h2>¡Éxito!</h2><p>Folio: <b>${data.folio}</b></p><br><button onclick="location.reload()" class="btn-guinda">Nueva Encuesta</button></div>`);
            } else {
                alert("Error: " + data.msg); location.reload();
            }
        });
    }

    renderPregunta(preguntaActual);
});