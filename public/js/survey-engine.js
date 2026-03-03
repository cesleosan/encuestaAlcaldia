// 1. Única declaración de base de datos
const dbLocal = new Dexie("TierraCorazonDB");
dbLocal.version(2).stores({
    encuestas: '++id, folio, fecha',
    catalogos: 'id' 
});

// 2. Precarga de colonias
async function precargarColonias() {
    if (!navigator.onLine) return;

    try {
        const res = await fetch(`${URLROOT}/Encuesta/getTodasLasColonias`);
        
        if (!res.ok) throw new Error("Error en la red");

        const data = await res.json();
        
        // Guardamos el array completo bajo la llave 'colonias'
        await dbLocal.catalogos.put({ id: 'colonias', data: data });
        
        console.log(`✅ Catálogo sincronizado: ${data.length} colonias guardadas localmente.`);
    } catch(e) { 
        console.error("Fallo la precarga: Probablemente la ruta " + URLROOT + " no es alcanzable."); 
    }
}

async function buscarColoniasLocal(cp) {
    try {
        // 1. Obtener el catálogo completo de Dexie
        const registro = await dbLocal.catalogos.get('colonias');
        
        if (!registro || !registro.data) {
            console.warn("Catálogo no encontrado en IndexedDB.");
            return [];
        }

        // 2. Filtrar el array 'data' por el código postal
        // Usamos filter porque un CP puede tener varias colonias (asentamientos)
        const resultados = registro.data.filter(colonia => {
            // Aseguramos que ambos sean strings para comparar bien
            return String(colonia.codigo_postal).trim() === String(cp).trim();
        });

        console.log(`Búsqueda local para CP ${cp}: ${resultados.length} encontradas.`);
        return resultados;

    } catch (error) {
        console.error("Error consultando Dexie:", error);
        return [];
    }
}

$(document).ready(function () {
    const element = document.getElementById('survey-app');
    if (!element) return;

    const rawBanco = element.getAttribute('data-banco');
    const bancoParsed = JSON.parse(rawBanco);
    const banco = {};
    precargarColonias();
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

function renderMapaGPS(data, form) {
    // 1. EL LAYOUT 100% COMPLETO (La dirección no se quita nunca)
    let layout = $(`
        <div class="map-container-fluid">
            <div class="coords-header" style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label class="label-input">Latitud <span style="color:red">*</span></label>
                    <input type="text" id="lat" name="latitud" class="input-redondo" required 
                           style="background:#f4f4f4; border: 2px solid #773357; font-weight: bold;" readonly>
                </div>
                <div style="flex: 1;">
                    <label class="label-input">Longitud <span style="color:red">*</span></label>
                    <input type="text" id="lon" name="longitud" class="input-redondo" required 
                           style="background:#f4f4f4; border: 2px solid #773357; font-weight: bold;" readonly>
                </div>
            </div>

            <button type="button" id="btn-gps-manual" class="btn-guinda" style="width:100%; margin-bottom:15px; background:#773357; height:50px; font-weight:bold; color:white; border-radius: 8px;">
                <i class="fa-solid fa-location-crosshairs"></i> OBTENER GPS AHORA
            </button>

            <div id="mapa-interactivo" style="width:100%; height:350px; border-radius:15px; background: #ddd; position:relative; overflow:hidden; border: 2px solid #773357;">
                <div id="fallback-msg" style="display:none; position:absolute; top:40%; width:100%; text-align:center; color:#333; z-index:1000; font-weight:bold; background: rgba(255,255,255,0.8); padding: 10px;">
                   🌍 Mapa visual no disponible offline.<br>Pero el GPS sigue funcionando.
                </div>
            </div>
            
            <div style="margin-top:20px;">
                <label class="label-input">Dirección Completa (Calle, Núm, Colonia) <span style="color:red">*</span></label>
                <input type="text" id="calle" name="calle_numero" class="input-redondo" 
                       placeholder="Escriba la dirección manualmente..." 
                       style="border: 2px solid #773357; background: #fff;" required>
            </div>
        </div>
    `);
    
    form.append(layout);

    // 2. INICIAR MAPA VISUAL (Protegido con Try/Catch para que no rompa el offline)
    let mapLoaded = false;
    try {
        if (typeof L !== 'undefined') {
            window.currentMap = L.map('mapa-interactivo').setView([19.289, -99.167], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                useCache: true,
                crossOrigin: true
            }).addTo(window.currentMap);
            mapLoaded = true;
        } else {
            $("#fallback-msg").show();
        }
    } catch(e) {
        console.error("Leaflet offline error:", e);
        $("#fallback-msg").show();
    }

    // 3. NÚCLEO DURO: OBTENER GPS CON DOBLE INTENTO (A prueba de fallos)
    function obtenerUbicacionHardware() {
        if (!navigator.geolocation) {
            Swal.fire('Error', 'Este dispositivo no soporta GPS.', 'error');
            return;
        }

        $("#btn-gps-manual").html('⏳ BUSCANDO SATÉLITES...').prop('disabled', true).css('background', '#e67e22');

        // Función de ÉXITO (Llena la latitud, longitud y avisa a la dirección)
        function exitoGPS(pos) {
            const lat = pos.coords.latitude.toFixed(7);
            const lon = pos.coords.longitude.toFixed(7);
            
            // LLENADO GARANTIZADO DE INPUTS
            $("#lat").val(lat);
            $("#lon").val(lon);

            // Intentar mover el marcador visual si el mapa existe
            if (mapLoaded) {
                try {
                    if (window.currentMarker) window.currentMap.removeLayer(window.currentMarker);
                    window.currentMarker = L.marker([lat, lon], {draggable: true}).addTo(window.currentMap);
                    window.currentMap.setView([lat, lon], 18);
                    
                    window.currentMarker.on('dragend', function() {
                        const p = window.currentMarker.getLatLng();
                        $("#lat").val(p.lat.toFixed(7));
                        $("#lon").val(p.lng.toFixed(7));
                        if(navigator.onLine && typeof reverseGeocode === 'function') reverseGeocode(p.lat, p.lng);
                    });
                } catch(e) { console.error("Error visual marcador:", e); }
            }

            // GESTIÓN DE LA DIRECCIÓN COMPLETA
            if (navigator.onLine && typeof reverseGeocode === 'function') {
                reverseGeocode(lat, lon); // Intenta autollenar si hay red
            } else {
                // Mantiene el campo libre y pone el foco para que el técnico escriba
                $("#calle").attr("placeholder", "Escriba la dirección aquí (Sin internet)").focus();
            }

            $("#btn-gps-manual").html('✅ UBICACIÓN OBTENIDA').css('background', '#27ae60').prop('disabled', false);
        }

        // Función de ERROR (Si falla el satélite puro, hace un segundo intento menos exigente)
        function errorGPS(err) {
            console.warn("Fallo GPS Alta Precisión, intentando recuperar caché de ubicación...");
            
            const opcionesBaja = { enableHighAccuracy: false, timeout: 10000, maximumAge: 60000 };
            
            navigator.geolocation.getCurrentPosition(exitoGPS, (errFinal) => {
                $("#btn-gps-manual").html('⚠️ REINTENTAR GPS').css('background', '#c0392b').prop('disabled', false);
                Swal.fire('GPS Falló', 'No se pudo obtener la latitud y longitud. Asegúrate de tener la ubicación encendida en el teléfono y sal al exterior.', 'warning');
            }, opcionesBaja);
        }

        // Intento 1: Alta precisión estricta
        const opcionesAlta = { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 };
        navigator.geolocation.getCurrentPosition(exitoGPS, errorGPS, opcionesAlta);
    }

    // 4. DISPARAR GPS AUTOMÁTICAMENTE AL ENTRAR A LA PREGUNTA
    setTimeout(obtenerUbicacionHardware, 500); // Pequeña pausa para que el HTML cargue bien
    
    // Asignar evento al botón de forzar captura
    $("#btn-gps-manual").off('click').on('click', obtenerUbicacionHardware);
}

function reverseGeocode(lat, lon) {
    if (!navigator.onLine) {
        $("#calle").attr("placeholder", "Modo offline: Escriba la dirección a mano");
        return;
    }

    // 1. AVISO VISUAL: Le decimos al técnico que el sistema está trabajando
    $("#calle").attr("placeholder", "Buscando dirección por satélite...");

    // Nota: Photon prefiere el orden lon, lat en su URL oficial
    fetch(`https://photon.komoot.io/reverse?lon=${lon}&lat=${lat}`)
        .then(res => {
            if (!res.ok) throw new Error("Saturación del servidor Photon");
            return res.json();
        })
        .then(data => {
            if (data.features && data.features.length > 0) {
                const p = data.features[0].properties;
                const calle = p.name || p.street || "";
                const num = p.housenumber || "";
                const colonia = p.district || p.locality || "";
                
                // 2. LIMPIEZA: Evitamos que queden comas sueltas si falta algún dato
                let direccionFormateada = `${calle} ${num}, ${colonia}`.trim();
                direccionFormateada = direccionFormateada.replace(/^[,\s]+|[,\s]+$/g, '').replace(/,\s*,/g, ',');

                // 3. LLENADO SEGURO
                if($("#calle").val() === "") {
                    // Si encontró algo válido (más de 3 letras) lo pone, si no, avisa.
                    if (direccionFormateada.length > 3) {
                        $("#calle").val(direccionFormateada);
                    } else {
                        $("#calle").attr("placeholder", "Dirección no detallada. Escríbala a mano.");
                    }
                }
            } else {
                // Si la API no sabe qué hay en esas coordenadas
                $("#calle").attr("placeholder", "Zona sin registros. Escriba la dirección a mano.");
            }
        })
        .catch((e) => {
            console.warn("Fallo Photon:", e);
            // 4. PLAN B VISUAL: Si el internet es súper lento o Photon bloquea, el técnico sabe qué hacer.
            $("#calle").attr("placeholder", "Fallo de conexión. Escriba la dirección manualmente.");
        });
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
   $(document).on('keyup', 'input[name="cp"]', async function() {
    let cp = $(this).val();
    const container = $('[data-name="pueblo_colonia"]');
    if (cp.length === 5) {
        try {
            const cat = await dbLocal.catalogos.get('colonias');
            if (cat && cat.data) {
                const filtradas = cat.data.filter(c => c.codigo_postal == cp);
                renderListaColonias(filtradas, container, cp);
            } else { throw new Error(); }
        } catch(e) {
            // Si falla lo local, hace tu fetch de siempre
            fetch(`${URLROOT}/Encuesta/buscarColonias/${cp}`)
                .then(res => res.json())
                .then(data => renderListaColonias(data, container, cp));
        }
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
    
    // Si estamos en la pantalla de coordenadas, capturamos los inputs manuales
    if ($("#lat").length > 0) {
        respuestas[idActual] = {
            latitud: $("#lat").val(),
            longitud: $("#lon").val(),
            calle_numero: $("#calle").val()
        };
    } else {
        respuestas[idActual] = $(form).serializeArray();
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

async function finalizarEncuesta() {
    const payload = { 
        folio: FOLIO_AUTO, 
        datos: respuestas, 
        fecha: new Date().toISOString() 
    };
    
    if (navigator.onLine) {
        enviarAlServidor(payload);
    } else {
        await dbLocal.encuestas.add(payload);
        Swal.fire('Modo Offline', 'Sin internet. Encuesta guardada en el celular.', 'info')
            .then(() => location.reload());
    }
}



function guardarEnLocal(payload) {
    dbLocal.encuestas.add(payload).then(() => {
        Swal.fire({
            title: 'Guardado en el Dispositivo',
            html: `Estás en una zona sin internet.<br>La encuesta con folio <b>${payload.folio}</b> se ha guardado en la memoria del celular y se subirá sola al detectar red.`,
            icon: 'info',
            confirmButtonColor: '#773357'
        }).then(() => {
            window.location.href = "<?php echo URLROOT; ?>/Encuesta";
        });
    });
}

function enviarAlServidor(payload) {
    fetch(`${URLROOT}/Encuesta/guardar`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload.datos)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                title: '¡Guardado Exitoso!',
                text: `Folio: ${data.folio}`,
                icon: 'success',
                confirmButtonColor: '#773357'
            }).then(() => {
                window.location.reload(); // Recarga para nueva encuesta
            });
        } else {
            // Si el servidor responde error (ej. CURP duplicado)
            Swal.fire('Atención', data.msg, 'warning');
        }
    })
    .catch(() => {
        // Error de red: Guardar en local automáticamente
        guardarEnLocal(payload);
    });
}

    renderPregunta(preguntaActual);

    // CAMBIO 4: Sincronización automática al detectar red
window.addEventListener('online', async () => {
    // 1. Indicador visual
    $('.status-indicator').css({'background-color': '#28a745'});

    const pendientes = await dbLocal.encuestas.toArray();
    if (pendientes.length === 0) return;

    console.log(`Iniciando sincronización de ${pendientes.length} encuestas...`);

    for (const item of pendientes) {
        try {
            // USAR LA RUTA COMPLETA PARA EVITAR FALLOS DE REDIRECCIÓN
            const res = await fetch(`${URLROOT}/index.php?url=Encuesta/guardar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(item.datos)
            });

            // Si el servidor devuelve error de texto en lugar de JSON, esto fallará
            const data = await res.json();

            if (data.status === 'success') {
                await dbLocal.encuestas.delete(item.id);
                console.log(`✅ Folio ${item.folio} sincronizado.`);
            } else {
                console.error(`❌ Error del servidor para folio ${item.folio}: ${data.msg}`);
            }
        } catch(e) { 
            console.error("Fallo de red o error de ruta. La sincronización se pausó.", e);
            break; // Detenemos el ciclo si hay un error de conexión real
        } 
    }
});

    // También actualizamos el de offline para que sea consistente
    window.addEventListener('offline', () => {
        $('.status-indicator').css({
            'background-color': '#dc3545', 
            'box-shadow': '0 0 5px rgba(220, 53, 69, 0.5)'
        });
    });
});
