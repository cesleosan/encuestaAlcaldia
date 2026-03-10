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
        // 1. GESTIÓN DE DEPENDENCIAS
        let depData = campo.dependencia ? `data-depende-de="${campo.dependencia.padre}" data-valor-req="${campo.dependencia.valor}"` : '';
        let divCampo = $(`<div class="campo-wrapper" ${depData} style="margin-bottom:20px;"></div>`);
        
        let label = `<label class="label-input">${campo.label}</label>`;
        let input = "";
        let valorDefecto = "";
        
        // 2. ATRIBUTOS DE ESTADO (Readonly)
        let readonlyAttr = campo.readonly ? "readonly" : "";
        let estiloCenizo = campo.readonly ? 'style="background-color: #f4f4f4;"' : '';

        // 3. AUTO-LLENADO DE FOLIO
        if (campo.name === 'folio') valorDefecto = typeof FOLIO_AUTO !== 'undefined' ? FOLIO_AUTO : '';

        // 4. RENDERIZADO DE INPUTS BÁSICOS (text, date, tel, email)
        if (['text', 'date', 'tel', 'email'].includes(campo.tipo)) {
            
            // 🔥 CORRECCIÓN QUIRÚRGICA: Maxlength dinámico (10 para Tel, 5 para CP, 18 para CURP)
            let maxAttr = campo.maxlength ? `maxlength="${campo.maxlength}"` : (campo.tipo === 'tel' ? 'maxlength="10"' : '');
            
            // 🔥 AÑADIDO: Inputmode para abrir el teclado correcto en móviles (Decimal/Numérico)
            let modeAttr = campo.inputmode ? `inputmode="${campo.inputmode}"` : '';
            
            // GESTIÓN DE SUGERENCIAS DE CORREO
            let listAttr = (campo.tipo === 'email' && campo.sugerencias) ? `list="list-${campo.name}"` : '';
            
            input = `<input type="${campo.tipo}" name="${campo.name}" value="${valorDefecto}" class="input-redondo" placeholder="${campo.placeholder || ''}" ${readonlyAttr} ${estiloCenizo} ${maxAttr} ${modeAttr} ${listAttr} required>`;
            
            // Si el campo es email, inyectamos el datalist para las sugerencias automáticas
            if (campo.tipo === 'email' && campo.sugerencias) {
                input += `<datalist id="list-${campo.name}"></datalist>`;
            }
        } 
        
        // 5. RENDERIZADO DE SELECT (Combos)
        else if (campo.tipo === 'select') {
            input = `<select name="${campo.name}" class="input-redondo" required><option value="">Seleccione...</option>`;
            if(campo.opciones) campo.opciones.forEach(opt => { 
                input += `<option value="${opt.val}">${opt.texto}</option>`; 
            });
            input += `</select>`;
        }

        // 6. RENDERIZADO DE RADIOS (Opción única)
        else if (campo.tipo === 'radio') {
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

        // 7. RENDERIZADO DE CHECKBOX (Opción múltiple)
        else if (campo.tipo === 'checkbox') {
            let checkGroup = $('<div class="radio-group-container" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">');
            if(campo.opciones) campo.opciones.forEach(opt => {
                checkGroup.append(`
                    <label class="checkbox-custom">
                        <input type="checkbox" name="${campo.name}[]" value="${opt.val}">
                        <span class="checkbox-checkmark"></span> ${opt.texto}
                    </label>`);
            });
            input = checkGroup;
        }

        // 8. INYECCIÓN DE HTML PURO (Separadores o Títulos)
        else if (campo.tipo === 'html') {
            divCampo.html(campo.label);
            form.append(divCampo);
            return; // Saltamos el append normal para este tipo
        }

        // 9. ENSAMBLAJE FINAL DEL CAMPO
        divCampo.append(label).append(input);
        form.append(divCampo);
    });

    // 10. RE-EVALUACIÓN DE DEPENDENCIAS AL TERMINAR EL RENDER
    evaluarDependenciasInternas();
}

function renderMapaGPS(data, contenedor) {
    let layout = $(`
        <div class="map-container-fluid">
            <div class="coords-header" style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <label class="label-input" style="color: var(--guinda); font-weight: bold;">Latitud</label>
                    <input type="text" id="lat" name="latitud" class="input-redondo" readonly style="background:#f8f9fa; font-weight: 600;">
                </div>
                <div style="flex: 1;">
                    <label class="label-input" style="color: var(--guinda); font-weight: bold;">Longitud</label>
                    <input type="text" id="lon" name="longitud" class="input-redondo" readonly style="background:#f8f9fa; font-weight: 600;">
                </div>
            </div>

            <button type="button" id="btn-forzar-gps" class="btn-guinda" style="width:100%; margin-bottom:15px; background:#4a1e36; gap:10px; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-location-dot"></i> OBTENER UBICACIÓN (GPS)
            </button>

            <div class="mapa-wrapper" style="border: 2px solid var(--guinda); border-radius:15px; overflow:hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div id="mapa-interactivo" style="width:100%; height:400px; background: #eee;"></div>
            </div>
        </div>

        <div style="margin-top:25px;">
            <label class="label-input" style="font-weight: bold;">Dirección Detectada</label>
            <input type="text" id="calle" name="calle_numero" class="input-redondo" placeholder="Buscando dirección o escriba manualmente..." required>
        </div>
    `);
    
    contenedor.append(layout);

    setTimeout(() => {
        // --- 1. INICIALIZACIÓN DEL MAPA ---
        if (window.currentMap) { window.currentMap.remove(); }
        window.currentMap = L.map('mapa-interactivo').setView([19.289, -99.167], 13);
        
        if (navigator.onLine) {
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(window.currentMap);
        }

        // --- 2. DEFINICIÓN DEL PIN GUINDA ---
        const iconGuinda = L.divIcon({
            className: 'custom-pin pin-pulsante',
            html: '<div class="pin-bola"></div>',
            iconSize: [30, 30],
            iconAnchor: [15, 30]
        });

        let marker; // Una sola declaración limpia

        function buscarDireccion(lat, lon) {
            if (!navigator.onLine) return;
            fetch(`https://photon.komoot.io/reverse?lon=${lon}&lat=${lat}`)
                .then(res => res.json())
                .then(resData => {
                    if (resData.features && resData.features.length > 0) {
                        const f = resData.features[0].properties;
                        $("#calle").val(`${f.name || f.street || ""} ${f.housenumber || ""}`.trim());
                    }
                }).catch(e => console.warn("Offline: Photon no disponible"));
        }

        // --- 3. LÓGICA DE CAPTURA GPS (AUTOMÁTICA Y MANUAL) ---
        function capturarGPS() {
            const btn = $("#btn-forzar-gps");
            btn.html('<i class="fa-solid fa-spinner fa-spin"></i> SINCRONIZANDO SATÉLITES...').prop('disabled', true);

            navigator.geolocation.getCurrentPosition(pos => {
                // Aquí es donde nacen las variables latitude y longitude
                const lat = pos.coords.latitude;
                const lon = pos.coords.longitude;

                $("#lat").val(lat.toFixed(7));
                $("#lon").val(lon.toFixed(7));

                window.currentMap.setView([lat, lon], 18);
                
                // Si el marcador ya existe, lo movemos; si no, lo creamos con el Icono Guinda
                if (marker) {
                    marker.setLatLng([lat, lon]);
                } else {
                    marker = L.marker([lat, lon], { 
                        draggable: true,
                        icon: iconGuinda 
                    }).addTo(window.currentMap);
                    
                    marker.on('dragend', function() {
                        const p = marker.getLatLng();
                        $("#lat").val(p.lat.toFixed(7));
                        $("#lon").val(p.lng.toFixed(7));
                        buscarDireccion(p.lat, p.lng);
                    });
                }

                buscarDireccion(lat, lon);
                btn.html('<i class="fa-solid fa-check"></i> UBICACIÓN CAPTURADA').css('background', '#28a745').prop('disabled', false);

            }, (err) => {
                btn.html('<i class="fa-solid fa-location-dot"></i> REINTENTAR GPS').prop('disabled', false);
                console.error("Error GPS:", err);
            }, { enableHighAccuracy: true, timeout: 15000 });
        }

        // --- 4. ASIGNACIÓN DE EVENTOS ---
        $(document).off('click', '#btn-forzar-gps').on('click', '#btn-forzar-gps', capturarGPS);

        window.currentMap.on('click', function(e) {
            const { lat, lng } = e.latlng;
            $("#lat").val(lat.toFixed(7));
            $("#lon").val(lng.toFixed(7));
            
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true, icon: iconGuinda }).addTo(window.currentMap);
            }
            buscarDireccion(lat, lng);
        });

        // EJECUCIÓN AUTOMÁTICA (MODO ONLINE/CARGA INICIAL)
        capturarGPS();

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
    // BLINDAJE 3: CURP en Mayúsculas y sin caracteres especiales
    $(document).on('input', 'input[name="curp"]', function () {
        // Convierte a mayúsculas y quita espacios/especiales
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        
        // Recortar si por alguna razón pega un texto más largo de 18
        if (this.value.length > 18) {
            this.value = this.value.slice(0, 18);
        }
    });
    $(document).on('input', 'input[type="tel"]', function () {
        // Borra cualquier cosa que no sea número al instante
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Doble check de seguridad para el largo máximo definido en el Model
        const max = $(this).attr('maxlength');
        if (max && this.value.length > max) {
            this.value = this.value.slice(0, max);
        }
    });
    // 🔥 BLINDAJE 5: Sugerencias automáticas de correo
    $(document).on('input', 'input[type="email"]', function() {
        const input = $(this);
        const val = input.val();
        const name = input.attr('name');
        const datalist = $(`#list-${name}`);
        
        // Solo actuamos si el usuario NO ha puesto un '@' todavía
        if (val && !val.includes('@')) {
            // Obtenemos las sugerencias desde el banco de preguntas (si están disponibles)
            // Para este ejemplo usamos las estándar de tu modelo
            const sugerencias = ['gmail.com', 'outlook.com', 'hotmail.com', 'yahoo.com', 'icloud.com'];
            let options = '';
            sugerencias.forEach(dom => {
                options += `<option value="${val}@${dom}">`;
            });
            datalist.html(options);
        } else {
            datalist.empty(); // Limpiamos si ya puso el @ para no estorbar
        }
    });

    //  Números y Decimales para Producción
    $(document).on('input', 'input[name="superficie_prod"], input[name="volumen_prod"]', function() {
        // 1. Solo permite números y el punto decimal
        this.value = this.value.replace(/[^0-9.]/g, '');

        // 2. Evita que pongan más de un punto decimal
        const puntos = this.value.split('.').length - 1;
        if (puntos > 1) {
            this.value = this.value.replace(/\.+$/, "");
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

    let errorValidacion = false;

    // --- VALIDACIÓN DE TELÉFONOS (10 DÍGITOS) ---
    $(form).find('input[type="tel"]').each(function() {
        const val = $(this).val();
        if ($(this).attr('maxlength') == "10" && val.length > 0 && val.length < 10) {
            Swal.fire('Teléfono Inválido', 'El número debe ser de exactamente 10 dígitos.', 'warning');
            errorValidacion = true;
            return false; 
        }
    });

    // --- VALIDACIÓN DE CURP (18 CARACTERES) ---
    const inputCurp = $(form).find('input[name="curp"]');
    if (inputCurp.length > 0) {
        const curpVal = inputCurp.val().trim();
        if (curpVal.length > 0 && curpVal.length < 18) {
            Swal.fire('CURP Incompleta', 'La CURP debe tener exactamente 18 caracteres.', 'warning');
            errorValidacion = true;
        }
    }
    // --- VALIDACIÓN DE CORREO (FORMATO) ---
    const inputEmail = $(form).find('input[type="email"]');
    if (inputEmail.length > 0) {
        const emailVal = inputEmail.val().trim();
        // Expresión regular estándar para correos válidos
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (emailVal.length > 0 && !emailRegex.test(emailVal)) {
            Swal.fire({
                icon: 'warning',
                title: 'Correo Inválido',
                text: 'Por favor ingrese un correo válido (ejemplo@dominio.com).',
                confirmButtonColor: 'var(--guinda)'
            });
            errorValidacion = true;
        }
    }

    if (errorValidacion) return; // Si hay error en Tel o CURP, no avanzamos
        
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
                window.location.reload();
            });
        } else {
            // 🔥 MODIFICACIÓN QUIRÚRGICA: Mostramos el error real de MariaDB
            console.error("Detalle técnico del servidor:", data.detalles); // Ver en F12
            Swal.fire({
                title: 'Error de Inserción',
                html: `<p>${data.msg}</p><small style="color:red;">${data.detalles || 'Sin detalles'}</small>`,
                icon: 'error',
                confirmButtonColor: '#773357'
            });
        }
    })
    .catch(err => {
        console.error("Error de conexión:", err);
        guardarEnLocal(payload);
    });
}

    renderPregunta(preguntaActual);

    // CAMBIO 4: Sincronización automática al detectar red
window.addEventListener('online', async () => {
    // 1. Indicador visual de red
    $('.status-indicator').css({'background-color': '#28a745'});

    const pendientes = await dbLocal.encuestas.toArray();
    if (pendientes.length === 0) return;

    // 🔥 ACUMULADOR PARA EL REPORTE FINAL
    let foliosSincronizados = [];

    console.log(`Iniciando sincronización de ${pendientes.length} encuestas...`);

    for (const item of pendientes) {
        try {
            const res = await fetch(`${URLROOT}/index.php?url=Encuesta/guardar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(item.datos)
            });

            const data = await res.json();

            if (data.status === 'success') {
                await dbLocal.encuestas.delete(item.id);
                foliosSincronizados.push(item.folio); // Guardamos para el reporte
                console.log(`✅ Folio ${item.folio} sincronizado.`);
            }
        } catch(e) { 
            console.error("Fallo de red en medio de la sincronización.", e);
            break; 
        } 
    }

    // 🔥 MOSTRAR REPORTE FINAL AL TÉCNICO
    if (foliosSincronizados.length > 0) {
        Swal.fire({
            title: '¡Sincronización Exitosa!',
            html: `
                <div style="text-align: left;">
                    <p>Se subieron <b>${foliosSincronizados.length}</b> encuestas pendientes:</p>
                    <ul style="max-height: 150px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 10px; list-style: none;">
                        ${foliosSincronizados.map(f => `<li>✅ Folio: <b>${f}</b></li>`).join('')}
                    </ul>
                </div>
            `,
            icon: 'success',
            confirmButtonColor: '#773357'
        });
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
