<?php
class PreguntaModel {

    public function getBanco() {
        return [
// --- PANTALLA 1: DATOS DEL RESPONSABLE (AUTOMATIZADO) ---
    1 => [
        'id' => 1,
        'tipo' => 'formulario', // CAMBIO: de seleccion a formulario
        'pregunta' => 'Responsable del Levantamiento',
        'subtitulo' => 'Técnico asignado por el sistema',
        'campos' => [
            [
                'name' => 'tecnico_nombre', 
                'label' => 'Nombre del Técnico', 
                'tipo' => 'text', 
                'readonly' => true, // El usuario no puede editarlo
                'value' => '' 
            ],
        ],
        'boton_texto' => 'Comenzar Encuesta',
        'saltaA' => 2
    ],

    // --- PANTALLA 2: DATOS GENERALES (Imagen image_1ca045.png) ---
        2 => [
        'id' => 2,
        'tipo' => 'formulario',
        'pregunta' => 'Datos Generales',
        'subtitulo' => 'Información de la unidad productiva',
        'campos' => [
            // 1. FOLIO: Ahora es de solo lectura
            ['name' => 'folio', 'label' => 'Folio', 'tipo' => 'text', 'placeholder' => 'TLP-2026-XXXX', 'readonly' => true],
               ['name' => 'curp', 'label' => 'CURP', 'tipo' => 'text', 'placeholder' => 'Clave Única de Registro de Población'],
            // 2. NOMBRE: Se auto-llena con el usuario logueado
            ['name' => 'nombre_productor', 'label' => 'Nombre de la productora (productor)', 'tipo' => 'text', 'readonly' => true],
            
            ['name' => 'fecha_nacimiento', 'label' => 'Fecha de nacimiento', 'tipo' => 'date', 'readonly' => true],
            
            [
                'name' => 'sexo', 'label' => 'Sexo', 'tipo' => 'radio', 
                'opciones' => [['val' => 'MUJER', 'texto' => 'Mujer'], ['val' => 'HOMBRE', 'texto' => 'Hombre']]
            ],
            
            // 3. TIEMPO DE RESIDENCIA: Ahora es un SELECT (Combo)
            [
                'name' => 'tiempo_residencia', 
                'label' => 'Tiempo de residir en Tlalpan', 
                'tipo' => 'select', // <--- Cambiamos a tipo select
                'opciones' => [
                    ['val' => '0-5', 'texto' => '0 a 5 años'],
                    ['val' => '6-10', 'texto' => '6 a 10 años'],
                    ['val' => '11-20', 'texto' => '11 a 20 años'],
                    ['val' => '21+', 'texto' => 'Más de 20 años']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 3
    ],

    // --- PANTALLA 3: GRUPO ÉTNICO Y ESTADO CIVIL (Imagen image_1d8581.png) ---
3 => [
        'id' => 3,
        'tipo' => 'formulario',
        'pregunta' => 'Datos Demográficos',
        'subtitulo' => 'Información social complementaria',
        'campos' => [
            [
                'name' => 'grupo_etnico', 
                'label' => '¿Pertenece a algún grupo étnico?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'SI', 'texto' => 'SÍ'],
                    ['val' => 'NO', 'texto' => 'NO']
                ]
            ],
            // 🔥 AGREGAMOS LA DEPENDENCIA AQUÍ:
            [
                'name' => 'grupo_etnico_cual', 
                'label' => '¿Cuál? (Si respondió SÍ)', 
                'tipo' => 'text', 
                'placeholder' => 'Especifique el grupo étnico',
                'dependencia' => ['padre' => 'grupo_etnico', 'valor' => 'SI'] // Solo se activa si grupo_etnico == SI
            ],
             // Pregunta Estado Civil
            [
                'name' => 'estado_civil', 
                'label' => 'Estado civil', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'SOLTERA', 'texto' => 'Soltera (o)'],
                    ['val' => 'CASADA', 'texto' => 'Casada (o)'],
                    ['val' => 'DIVORCIADA', 'texto' => 'Divorciada (o)'],
                    ['val' => 'VIUDA', 'texto' => 'Viuda (o)'],
                    ['val' => 'UNION_LIBRE', 'texto' => 'Unión libre'],
                    ['val' => 'NA', 'texto' => 'NA']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 4
    ],

    // --- PANTALLA 4: OCUPACIÓN Y CONTACTO (Imagen image_1d90c2.png) ---
    4 => [
        'id' => 4,
        'tipo' => 'formulario',
        'pregunta' => 'Datos Socioeconómicos',
        'subtitulo' => 'Ocupación y medios de contacto',
        'campos' => [
            // SECCIÓN A: OCUPACIÓN
            [
                'name' => 'ocupacion', 
                'label' => 'Ocupación', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'ESTUDIANTE', 'texto' => 'Estudiante'],
                    ['val' => 'TRAB_NO_REMUNERADO', 'texto' => 'Trabajador sin remuneración o pago'],
                    ['val' => 'PATRON', 'texto' => 'Patrón o empleador'],
                    ['val' => 'TRAB_PRIVADO', 'texto' => 'Trabajador remunerado del sector privado'],
                    ['val' => 'TRAB_PUBLICO', 'texto' => 'Trabajador remunerado del sector público'],
                    ['val' => 'CUENTA_PROPIA', 'texto' => 'Trabajador por cuenta propia'],
                    ['val' => 'HOGAR', 'texto' => 'Realiza quehaceres del hogar'],
                    ['val' => 'PENSIONADA', 'texto' => 'Pensionada (o)'],
                    ['val' => 'OTRO', 'texto' => 'Otro'],
                    ['val' => 'NA', 'texto' => 'NA']
                ]
            ],

            // Separador visual
            ['name' => 'sep_contacto', 'label' => '<hr style="margin: 30px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

           // SECCIÓN CONTACTO CON RESTRICCIONES
            [
                'name' => 'tel_particular', 
                'label' => 'Teléfono Particular', 
                'tipo' => 'tel', // Usamos tel para teclado numérico en móvil
                'maxlength' => 10, 
                'placeholder' => '10 dígitos (solo números)'
            ],
            [
                'name' => 'tel_recados', 
                'label' => 'Teléfono para Recados', 
                'tipo' => 'tel', 
                'maxlength' => 10, 
                'placeholder' => '10 dígitos'
            ],
            [
                'name' => 'email', 
                'label' => 'Correo electrónico', 
                'tipo' => 'email', 
                'placeholder' => 'ejemplo@correo.com',
                'sugerencias' => ['gmail.com', 'outlook.com', 'hotmail.com', 'yahoo.com', 'icloud.com']
            ],
            [
                'name' => 'tiempo_residencia_cdmx', 
                'label' => 'Tiempo de residencia en CDMX', 
                'tipo' => 'select',
                'opciones' => [
                    ['val' => '1-5', 'texto' => '1 a 5 años'],
                    ['val' => '6-10', 'texto' => '6 a 10 años'],
                    ['val' => '11-20', 'texto' => '11 a 20 años'],
                    ['val' => '21-30', 'texto' => '21 a 30 años'],
                    ['val' => '31+', 'texto' => 'Más de 30 años']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 5
    ],

5 => [
        'id' => 5,
        'tipo' => 'formulario',
        'pregunta' => 'Ubicación',
        'subtitulo' => 'Domicilio de la unidad productiva',
        'campos' => [
            // CP ahora es opcional para permitir selección directa del Top 10
            ['name' => 'cp', 'label' => 'Código postal (Opcional si aparece abajo)', 'tipo' => 'tel', 'maxlength' => 5, 'placeholder' => 'Ej. 14000'],
            
            // Contenedor dinámico para radios
            ['name' => 'pueblo_colonia', 'label' => 'Pueblo o colonia', 'tipo' => 'radio', 'opciones' => []], 
            
            // Campo dependiente
            [
                'name' => 'pueblo_otro', 
                'label' => 'Especifique el nombre de la colonia', 
                'tipo' => 'text', 
                'placeholder' => 'Escriba aquí...',
                'dependencia' => ['padre' => 'pueblo_colonia', 'valor' => 'OTRO'] 
            ],
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 6
    ],
    6 => [
        'id' => 6,
        'tipo' => 'coordenadas',
        'pregunta' => 'Ubicación Geográfica',
        'subtitulo' => 'Georreferenciación automática del predio',
        'campos' => [
            'latitud' => 'latitud',
            'longitud' => 'longitud',
            'calle' => 'calle_numero'
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 7 
    ],
    // --- PANTALLA 7: SITUACIÓN DE LA UNIDAD (Imagen image_1f4f99.png) ---
    7 => [
        'id' => 7,
        'tipo' => 'formulario',
        'pregunta' => 'Situación de la Unidad Productiva',
        'subtitulo' => 'Todos (Agrícolas, Agropecuarios, Huertos, Granjas Familiares y Transformadoras)',
        'campos' => [
            [
                'name' => 'situacion_unidad', 
                'label' => '', // Lo dejamos vacío porque el título ya explica todo
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'ACTIVA', 'texto' => 'Activa'],
                    ['val' => 'REACTIVACION', 'texto' => 'Reactivación'],
['val' => 'NUEVA', 'texto' => 'Nueva']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente', // CAMBIO: Botón Siguiente
        'saltaA' => 8 // Salta a la nueva sección socioeconómica
    ],

    // --- PANTALLA 8: PERFIL SOCIODEMOGRÁFICO (Imagen image_1f5a58.png) ---
    8 => [
        'id' => 8,
        'tipo' => 'formulario',
        'pregunta' => 'Encuesta Socioeconómica',
        'subtitulo' => 'Perfil Sociodemográfico y Bienestar',
        'campos' => [
            // PREGUNTA 1: GRADO DE ESTUDIOS
            [
                'name' => 'grado_estudios', 
                'label' => 'Último grado de estudios', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'SIN_ESTUDIOS', 'texto' => 'Sin estudios'],
                    ['val' => 'PRIMARIA', 'texto' => 'Primaria'],
                    ['val' => 'SECUNDARIA', 'texto' => 'Secundaria'],
                    ['val' => 'CARRERA_TECNICA', 'texto' => 'Carrera Técnica'],
                    ['val' => 'LICENCIATURA', 'texto' => 'Licenciatura'],
                    ['val' => 'POSGRADO', 'texto' => 'Posgrado']
                ]
            ],

            // Separador visual
            ['name' => 'sep_dependientes', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // PREGUNTA 2: DEPENDIENTES ECONÓMICOS
            [
                'name' => 'dependientes_economicos', 
                'label' => '¿Cuántas personas dependen económicamente de usted?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'NINGUNA', 'texto' => 'Ninguna'],
                    ['val' => '1-2', 'texto' => '1-2'],
                    ['val' => '3-4', 'texto' => '3-4'],
                    ['val' => '5_MAS', 'texto' => '5 o más']
                ]
            ],

            // Separador visual
            ['name' => 'sep_salud', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // PREGUNTA 3: SERVICIOS DE SALUD
            [
                'name' => 'servicios_salud', 
                'label' => '¿Cuenta con servicios de Salud/Servicio Social?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'IMSS_ISSSTE', 'texto' => 'Sí, IMSS/ISSSTE'],
                    ['val' => 'IMSS_BIENESTAR', 'texto' => 'Sí, IMSS Bienestar/INSABI'],
                    ['val' => 'OTRO', 'texto' => 'Sí, otro servicio'],
['val' => 'NO_CUENTA', 'texto' => 'No cuenta con servicio']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente', // CAMBIO
        'saltaA' => 9
    ],

    // --- PANTALLA 9: CARACTERÍSTICAS VIVIENDA (Imagen image_1fda1a.png) ---
    9 => [
        'id' => 9,
        'tipo' => 'formulario',
        'pregunta' => 'Características de la Vivienda',
        'subtitulo' => 'Materiales y energía',
        'campos' => [
            // Pisos
            [
                'name' => 'material_pisos', 
                'label' => 'Material predominante en los pisos de su vivienda', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'TIERRA', 'texto' => 'Tierra'],
                    ['val' => 'CEMENTO', 'texto' => 'Cemento/Firme'],
                    ['val' => 'RECUBRIMIENTO', 'texto' => 'Recubrimiento (mosaico, madera, loseta)']
                ]
            ],
            // Separador
            ['name' => 'sep_cocina', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],
            // Combustible
            [
                'name' => 'combustible_cocina', 
                'label' => '¿Qué combustible utiliza principalmente para cocinar?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'LENA_SIN', 'texto' => 'Leña/Carbón sin chimenea'],
                    ['val' => 'LENA_CON', 'texto' => 'Leña/Carbón con chimenea'],
                    ['val' => 'GAS', 'texto' => 'Gas'],
                    ['val' => 'ELECTRICIDAD', 'texto' => 'Electricidad']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 10
    ],

    // --- PANTALLA 10: BIENES Y DIAGNÓSTICO (Imagen image_1fda39.png) ---
    10 => [
        'id' => 10,
        'tipo' => 'formulario',
        'pregunta' => 'Equipamiento y Producción',
        'subtitulo' => 'Seleccione todas las opciones que apliquen',
        'campos' => [
            // Bienes (CHECKBOX - Múltiple)
            [
                'name' => 'bienes_vivienda', 
                'label' => '¿Con qué servicios/bienes cuenta en su vivienda?', 
                'tipo' => 'checkbox', // <--- TIPO NUEVO (Cuadritos)
                'opciones' => [
                    ['val' => 'REFRIGERADOR', 'texto' => 'Refrigerador'],
                    ['val' => 'LICUADORA', 'texto' => 'Licuadora'],
                    ['val' => 'LAVADORA', 'texto' => 'Lavadora'],
                    ['val' => 'TABLET', 'texto' => 'Tablet'],
                    ['val' => 'AUTO', 'texto' => 'Automovil propio'],
                    ['val' => 'MICROONDAS', 'texto' => 'Horno de microondas'],
                    ['val' => 'FREIDORA', 'texto' => 'Freidora de aire'],
                    ['val' => 'INTERNET', 'texto' => 'Internet'],
                    ['val' => 'COMPUTADORA', 'texto' => 'Computadora de escritorio o personal']
                ]
            ],

            // NUEVA SECCIÓN GRANDE (HTML Decorativo)
            ['name' => 'header_diagnostico', 'label' => '<h3 style="color:var(--guinda); margin-top:40px; margin-bottom:15px; font-weight:800; text-transform:uppercase; border-bottom: 2px solid var(--guinda); padding-bottom:5px;">» Diagnóstico Técnico-Productivo</h3>', 'tipo' => 'html'],

            // Agua (CHECKBOX - Múltiple)
            [
                'name' => 'tipo_agua', 
                'label' => '¿Qué tipo de agua utiliza para su producción?', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'TEMPORAL', 'texto' => 'Temporal (Lluvias)'],
                    ['val' => 'RED_PUBLICA', 'texto' => 'Agua de la red pública'],
                    ['val' => 'PIPA', 'texto' => 'Agua de pipa'],
                    ['val' => 'TRATADA', 'texto' => 'Agua tratada'],
['val' => 'POZO', 'texto' => 'Pozo/Manantial/Olla de captación']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente', // CAMBIO: Botón Siguiente
        'saltaA' => 11 // Salta a la continuación del diagnóstico
    ],

    // --- PANTALLA 11: INSUMOS, MAQUINARIA Y PROBLEMÁTICAS (Imagen image_1fe1c1.png) ---
    11 => [
        'id' => 11,
        'tipo' => 'formulario',
        'pregunta' => 'Diagnóstico Técnico',
        'subtitulo' => 'Insumos, equipamiento y retos',
        'campos' => [
            // Insumos
            [
                'name' => 'insumos_agricolas', 
                'label' => '¿Qué insumos agrícolas maneja?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'QUIMICOS', 'texto' => 'Solo químicos'],
                    ['val' => 'ORGANICOS', 'texto' => 'Solo orgánicos'],
                    ['val' => 'MIXTOS', 'texto' => 'Mixtos (ambos)'],
                    ['val' => 'NINGUNO', 'texto' => 'Ninguno']
                ]
            ],

            // Separador
            ['name' => 'sep_maquinaria', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Maquinaria
            [
                'name' => 'maquinaria', 
                'label' => '¿Con qué maquinaria o equipo propio cuenta?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'MANUALES', 'texto' => 'Solo herramientas manuales (pala, pico, etc)'],
                    ['val' => 'LIGERA', 'texto' => 'Maquinaria ligera (desbrozadora, motosierra, etc)'],
                    ['val' => 'PESADA', 'texto' => 'Maquinaria pesada (tractor)']
                ]
            ],

            // Separador
            ['name' => 'sep_problemas', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Problema Principal
            [
                'name' => 'problema_principal', 
                'label' => '¿Cuál considera que es el principal problema para producir?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'FALTA_AGUA', 'texto' => 'Falta de agua'],
                    ['val' => 'PLAGAS', 'texto' => 'Plagas/Enfermedades'],
                    ['val' => 'COSTO_INSUMOS', 'texto' => 'Alto costo de insumos'],
                    ['val' => 'MANO_OBRA', 'texto' => 'Falta de mano de obra'],
['val' => 'CLIMA', 'texto' => 'Clima']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente', // CAMBIO: Botón Siguiente
        'saltaA' => 12 // Salta a Economía
    ],

    // --- PANTALLA 12: ECONOMÍA Y COMERCIALIZACIÓN (Imagen image_203f3b.png) ---
    12 => [
        'id' => 12,
        'tipo' => 'formulario',
        'pregunta' => 'Economía y Comercialización',
        'subtitulo' => 'Ingresos y destino de la producción',
        'campos' => [
            // Ingreso Mensual
            [
                'name' => 'ingreso_mensual', 
                'label' => '¿Cuál es un ingreso mensual promedio?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'MENOS_3000', 'texto' => 'Menos de $3,000'],
                    ['val' => '3000_6000', 'texto' => 'De $3,001 a $6,000'],
                    ['val' => '6000_10000', 'texto' => 'De $6,001 a $10,000'],
                    ['val' => 'MAS_10000', 'texto' => 'Más de $10,000']
                ]
            ],

            // Separador
            ['name' => 'sep_dependencia', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Dependencia Económica
            [
                'name' => 'dependencia_economica', 
                'label' => 'Dependencia económica de la actividad productiva', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'UNICA_FUENTE', 'texto' => 'Es mi única fuente de ingresos'],
                    ['val' => 'COMPLEMENTO', 'texto' => 'Es un complemento a otros ingresos'],
                    ['val' => 'AUTOCONSUMO', 'texto' => 'No genera ingresos (autoconsumo)']
                ]
            ],

            // Separador
            ['name' => 'sep_destino', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Destino Producción
            [
                'name' => 'destino_produccion', 
                'label' => '¿A dónde destina su producción?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'AUTOCONSUMO', 'texto' => 'Autoconsumo'],
                    ['val' => 'LOCAL_DIRECTA', 'texto' => 'Venta local/directa'],
                    ['val' => 'INTERMEDIARIOS', 'texto' => 'Venta a intermediarios'],
                    ['val' => 'TRANSFORMACION', 'texto' => 'Transformación (mermeladas, conservas, etc.)']
                ]
            ],

            // Separador
            ['name' => 'sep_financiamiento', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Financiamiento (CHECKBOX - Múltiple)
            [
                'name' => 'financiamiento', 
                'label' => '¿De dónde obtiene el financiamiento para el ciclo productivo?', 
                'tipo' => 'checkbox', // <--- Cuadritos (Múltiple)
                'opciones' => [
                    ['val' => 'PROPIOS', 'texto' => 'Recursos propios/ahorros'],
                    ['val' => 'FAMILIARES', 'texto' => 'Préstamos familiares'],
                    ['val' => 'BANCARIOS', 'texto' => 'Créditos bancarios/Sofomes'],
                    ['val' => 'PRESTAMISTAS', 'texto' => 'Prestamistas'],
['val' => 'PROGRAMAS', 'texto' => 'Programas sociales']
                ]
            ],

            // Separador (Agregamos esto para la última pregunta de la imagen)
            ['name' => 'sep_dificultades', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Dificultades Comercialización (Última de Economía)
            [
                'name' => 'dificultades_comercializacion', 
                'label' => '¿Tiene dificultades para comercializar su producción? ¿Cuál es la principal?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'NO_VENDO_TODO', 'texto' => 'No, vendo todo'],
                    ['val' => 'BAJO_PRECIO', 'texto' => 'Sí, bajo precio de compra'],
                    ['val' => 'FALTA_TRANSPORTE', 'texto' => 'Sí, falta de transporte'],
                    ['val' => 'FALTA_COMPRADORES', 'texto' => 'Sí, falta de compradores'],
                    ['val' => 'INTERMEDIARIOS', 'texto' => 'Sí, intermediarios']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente', 
        'saltaA' => 13 // Salta a la sección social
    ],

    // --- PANTALLA 13: TEJIDO SOCIAL Y FUTURO (Imagen image_204e5e.png) ---
    13 => [
        'id' => 13,
        'tipo' => 'formulario',
        'pregunta' => 'Tejido Social',
        'subtitulo' => 'Perspectiva a futuro y participación familiar',
        'campos' => [
            // Participación Mujeres
            [
                'name' => 'participacion_mujeres', 
                'label' => '¿Cuál es la principal participación de las mujeres en la unidad productiva?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'ADMIN_VENTA', 'texto' => 'Participan en labores administrativas/venta'],
                    ['val' => 'CAMPO_FISICAS', 'texto' => 'Participan en labores de campo/físicas'],
                    ['val' => 'TITULARES', 'texto' => 'Son titulares/dueñas'],
                    ['val' => 'NO_PARTICIPAN', 'texto' => 'No participan']
                ]
            ],

            // Separador
            ['name' => 'sep_generaciones', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Nuevas Generaciones
            [
                'name' => 'nuevas_generaciones', 
                'label' => '¿Qué tan involucrados se encuentran las nuevas generaciones? (Hijos/Nietos)', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'INVOLUCRADOS', 'texto' => 'Están involucrados e interesados'],
                    ['val' => 'AYUDAN_NO_INTERES', 'texto' => 'Ayudan, pero no les interesa seguir'],
['val' => 'NO_INTERESA', 'texto' => 'No les interesa']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente', // CAMBIO: Botón Siguiente
        'saltaA' => 14 // Salta a la pregunta filtro de Capacitaciones
    ],

    // --- PANTALLA 14: FILTRO CAPACITACIONES (Imagen image_205279.png) ---
    14 => [
        'id' => 14,
        'tipo' => 'seleccion', // Usamos botones para decidir el camino rápido
        'pregunta' => 'Capacitaciones',
        'subtitulo' => '¿Estaría dispuesta (o) a recibir capacitación?',
        'opciones' => [
            ['val' => 'SI', 'texto' => 'SÍ', 'saltaA' => 15], // SI -> Va al detalle (Pantalla 15)
            ['val' => 'NO', 'texto' => 'NO', 'saltaA' => 16]  // NO -> Se salta el detalle y va a Apoyos (Pantalla 16)
        ]
    ],

// --- PANTALLA 15: DETALLE CAPACITACIONES (Dinámica) ---
    15 => [
        'id' => 15,
        'tipo' => 'formulario',
        'pregunta' => 'Temas de Interés',
        'subtitulo' => 'Seleccione el tema prioritario',
        'campos' => [
            [
                'name' => 'tema_capacitacion', 
                'label' => '¿Qué tema cree que urge más?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'PLAGAS', 'texto' => 'Manejo de plagas'],
                    ['val' => 'COMERCIALIZACION', 'texto' => 'Comercialización'],
                    ['val' => 'ABONOS', 'texto' => 'Elaboración de abonos/violes'],
                    ['val' => 'ADMINISTRACION', 'texto' => 'Administración'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ],
            [
                'name' => 'otra_capacitacion', 
                'label' => 'Especifique el tema de interés', 
                'tipo' => 'text', 
                'placeholder' => 'Describa el tema aquí...',
                'dependencia' => ['padre' => 'tema_capacitacion', 'valor' => 'OTRO'] 
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 16 
    ],

    // --- PANTALLA 16: FILTRO APOYOS (Imagen image_2052b7.png) ---
    16 => [
        'id' => 16,
        'tipo' => 'seleccion', // Botones decisión
        'pregunta' => 'Apoyos',
        'subtitulo' => '¿Ha recibido algún apoyo en los últimos dos años?',
        'opciones' => [
            ['val' => 'SI', 'texto' => 'SÍ', 'saltaA' => 17], // SI -> Va al detalle (Pantalla 17)
            ['val' => 'NO', 'texto' => 'NO', 'saltaA' => 18]  // NO -> Finaliza la encuesta
        ]
    ],

    // --- PANTALLA 17: DETALLE APOYOS (Solo si dijo SÍ) ---
    17 => [
        'id' => 17,
        'tipo' => 'formulario',
        'pregunta' => 'Detalle de Apoyos',
        'subtitulo' => 'Especifique el tipo y estatus',
        'campos' => [
            // Tipo de apoyo (CHECKBOX - Múltiple según la imagen)
            [
                'name' => 'tipo_apoyo', 
                'label' => '¿Qué tipo de apoyo recibió?', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'FEDERAL', 'texto' => 'Federal (SADER)'],
                    ['val' => 'ESTATAL', 'texto' => 'Estatal (CORENADR)'],
                    ['val' => 'LOCAL', 'texto' => 'Local (ALCALDÍA)'],
                    ['val' => 'PRIVADO', 'texto' => 'Privado/ONG']
                ]
            ],

            // Separador
            ['name' => 'sep_finiquito', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Carta Finiquito (RADIO)
            [
                'name' => 'carta_finiquito', 
                'label' => '¿Cuenta con carta finiquito?', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'SI', 'texto' => 'SÍ'],
                   ['val' => 'NO', 'texto' => 'NO']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente', // CAMBIO
        'saltaA' => 18
    ],

    // --- PANTALLA 18: TIPO DE PRODUCCIÓN (Imagen image_2a3c97.png) ---
    // Esta es la "Pregunta Madre" que definirá qué sigue.
    18 => [
        'id' => 18,
        'tipo' => 'formulario',
        'pregunta' => 'TIPO DE PRODUCCIÓN',
        'subtitulo' => 'Tipo de Unidad Productiva',
        'campos' => [
            [
                'name' => 'tipo_produccion', 
                'label' => 'Seleccione todas las que apliquen', 
                'tipo' => 'checkbox', // Múltiple selección
                'opciones' => [
                    ['val' => 'AGRICOLA', 'texto' => 'Agrícola'],
                    ['val' => 'PECUARIA', 'texto' => 'Pecuaria'],
                    ['val' => 'HUERTO', 'texto' => 'Huerto'],
                    ['val' => 'GRANJA', 'texto' => 'Granja Integral Familiar'],
                    ['val' => 'TRANSFORMADORA', 'texto' => 'Transformadora de Materia Prima']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        // Aquí empieza la magia: Saltamos a la 19, y la 19 decidirá si se muestra o no.
        'saltaA' => 19 
    ],
    
    // --- PANTALLA 19: (SOLO PARA PRUEBA DE LÓGICA) ---
    // Esta pantalla SOLO saldrá si elegiste "Agrícola" en la 18.
    // Si no, el JS la saltará automáticamente y buscará la 20.
 19 => [
        'id' => 19,
        'tipo' => 'formulario',
        'pregunta' => 'Producción Agrícola',
        'subtitulo' => 'Marque todas las categorías que correspondan',
        'condicion' => ['origen' => 18, 'valor' => 'AGRICOLA'], // <--- EL FILTRO
        'campos' => [
            [
                'name' => 'cats_agricola', 
                'label' => '', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'CEREALES', 'texto' => 'Cereales'],
                    ['val' => 'GRANOS', 'texto' => 'Granos'],
                    ['val' => 'LEGUMINOSAS', 'texto' => 'Leguminosas'],
                    ['val' => 'HORTALIZA', 'texto' => 'Hortaliza'],
                    ['val' => 'FRUTALES', 'texto' => 'Frutales'],
                    ['val' => 'MEDICINALES', 'texto' => 'Plantas medicinales'],
                    ['val' => 'ORNAMENTALES', 'texto' => 'Ornamentales o Forestales'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 20
    ],

    // --- PANTALLA 20: DETALLE CEREALES (Imagen image_2a4aa7.png) ---
    // CONDICIÓN: Solo si en la 19 elegiste "CEREALES"
    20 => [
        'id' => 20,
        'tipo' => 'formulario',
        'pregunta' => 'Cereales',
        'subtitulo' => 'Especifique los cereales',
        'condicion' => ['origen' => 19, 'valor' => 'CEREALES'],
        'campos' => [
            [
                'name' => 'detalle_cereales', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'AVENA', 'texto' => 'Avena'],
                    ['val' => 'CEBADA', 'texto' => 'Cebada'],
                    ['val' => 'CENTENO', 'texto' => 'Centeno'],
                    ['val' => 'TRITICALE', 'texto' => 'Triticale'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 21
    ],

    // --- PANTALLA 21: DETALLE GRANOS (Imagen image_2a4aa7.png) ---
    // CONDICIÓN: Solo si en la 19 elegiste "GRANOS"
    21 => [
        'id' => 21,
        'tipo' => 'formulario',
        'pregunta' => 'Granos',
        'subtitulo' => 'Especifique los granos',
        'condicion' => ['origen' => 19, 'valor' => 'GRANOS'],
        'campos' => [
            [
                'name' => 'detalle_granos', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'MAIZ', 'texto' => 'Maíz'], // <--- Ojo aquí, este activará la siguiente
                    ['val' => 'MIJO', 'texto' => 'Mijo'],
                    ['val' => 'SORGO', 'texto' => 'Sorgo'],
                    ['val' => 'TRIGO', 'texto' => 'Trigo'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 22
    ],

    // --- PANTALLA 22: DETALLE MAÍZ (Nivel 3 de profundidad!) (Imagen image_2a4ae7.png) ---
    // CONDICIÓN: Solo si en la 21 elegiste "MAIZ"
    22 => [
        'id' => 22,
        'tipo' => 'formulario',
        'pregunta' => 'Tipos de Maíz',
        'subtitulo' => 'Variedades de maíz cultivadas',
        'condicion' => ['origen' => 21, 'valor' => 'MAIZ'], // Revisa la anterior (Granos)
        'campos' => [
            [
                'name' => 'detalle_maiz', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'CACAHUACINTLE', 'texto' => 'Cacahuacintle'],
                    ['val' => 'AZUL', 'texto' => 'Azul'],
                    ['val' => 'AMARILLO', 'texto' => 'Amarillo'],
                    ['val' => 'ROJO', 'texto' => 'Rojo'],
                    ['val' => 'NEGRO', 'texto' => 'Negro'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 23
    ],

    // --- PANTALLA 23: DETALLE LEGUMINOSAS (Imagen image_2a4b04.png) ---
    23 => [
        'id' => 23,
        'tipo' => 'formulario',
        'pregunta' => 'Leguminosas',
        'subtitulo' => '',
        'condicion' => ['origen' => 19, 'valor' => 'LEGUMINOSAS'],
        'campos' => [
            [
                'name' => 'detalle_leguminosas', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'ALFALFA', 'texto' => 'Alfalfa'],
                    ['val' => 'FRIJOL', 'texto' => 'Frijol'],
                    ['val' => 'CHICHARO', 'texto' => 'Chícharo'],
                    ['val' => 'HABA', 'texto' => 'Haba'],
                    ['val' => 'GARBANZO', 'texto' => 'Garbanzo'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 24
    ],

    // --- PANTALLA 24: DETALLE HORTALIZAS (Imagen image_2a4b04.png) ---
    24 => [
        'id' => 24,
        'tipo' => 'formulario',
        'pregunta' => 'Hortalizas',
        'subtitulo' => 'Puede marcar varias opciones',
        'condicion' => ['origen' => 19, 'valor' => 'HORTALIZA'],
        'campos' => [
            [
                'name' => 'detalle_hortalizas', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'ESPINACA', 'texto' => 'Espinaca'],
                    ['val' => 'ACELGA', 'texto' => 'Acelga'],
                    ['val' => 'LECHUGA', 'texto' => 'Lechuga'],
                    ['val' => 'BETABEL', 'texto' => 'Betabel'],
                    ['val' => 'ZANAHORIA', 'texto' => 'Zanahoria'],
                    ['val' => 'RABANO', 'texto' => 'Rábano'],
                    ['val' => 'CEBOLLA', 'texto' => 'Cebolla'],
                    ['val' => 'CILANTRO', 'texto' => 'Cilantro'],
                    ['val' => 'PEREJIL', 'texto' => 'Perejil'],
                    ['val' => 'CHILE', 'texto' => 'Chile'],
                    ['val' => 'TOMATE', 'texto' => 'Tomate verde'],
                    ['val' => 'JITOMATE', 'texto' => 'Jitomate'],
                    ['val' => 'PAPA', 'texto' => 'Papa'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 25
    ],

    // --- PANTALLA 25: DETALLE ORNAMENTALES (Imagen image_2a4b20.png) ---
    25 => [
        'id' => 25,
        'tipo' => 'formulario',
        'pregunta' => 'Ornamentales o Forestales',
        'subtitulo' => '',
        'condicion' => ['origen' => 19, 'valor' => 'ORNAMENTALES'],
        'campos' => [
            [
                'name' => 'detalle_ornamentales', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'SUCULENTAS', 'texto' => 'Suculentas y cactáceas'],
                    ['val' => 'ROSAS', 'texto' => 'Rosas'],
                    ['val' => 'CEMPASUCHIL', 'texto' => 'Cempasúchil'],
                    ['val' => 'SOMBRA', 'texto' => 'Plantas de Sombra'],
                    ['val' => 'ALCATRAZ', 'texto' => 'Alcatraz'],
                    ['val' => 'ALELI', 'texto' => 'Alelí'],
                    ['val' => 'CRISALEA', 'texto' => 'Crisalea'],
                    ['val' => 'PERRITO', 'texto' => 'Perrito'],
                    ['val' => 'TERCIOPELO', 'texto' => 'Terciopelo'],
                    ['val' => 'NOCHEBUENA', 'texto' => 'Nochebuena'],
                    ['val' => 'HORTENSIAS', 'texto' => 'Hortensias'],
                    ['val' => 'ORQUIDEAS', 'texto' => 'Orquídeas'],
                    ['val' => 'PINO_AYA', 'texto' => 'Pino Ayacahuite'],
                    ['val' => 'PINO_PSEU', 'texto' => 'Pino Pseudotsuga'],
                    ['val' => 'OTROS_PINOS', 'texto' => 'Otros pinos'],
                    ['val' => 'ABIES', 'texto' => 'Abies religiosa'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 26
    ],

    // --- PANTALLA 26: DETALLE FRUTALES (Imagen image_2a4b47.png) ---
    26 => [
        'id' => 26,
        'tipo' => 'formulario',
        'pregunta' => 'Frutales',
        'subtitulo' => '',
        'condicion' => ['origen' => 19, 'valor' => 'FRUTALES'],
        'campos' => [
            [
                'name' => 'detalle_frutales', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'MANZANA', 'texto' => 'Manzana'],
                    ['val' => 'DURAZNO', 'texto' => 'Durazno'],
                    ['val' => 'PERA', 'texto' => 'Pera'],
                    ['val' => 'CIRUELO', 'texto' => 'Ciruelo o Ciruela'],
                    ['val' => 'HIGO', 'texto' => 'Higo'],
                    ['val' => 'TUNA', 'texto' => 'Tuna o nopal'],
                    ['val' => 'CAPULIN', 'texto' => 'Capulín'],
                    ['val' => 'FRESA', 'texto' => 'Fresa'],
                    ['val' => 'FRAMBUESA', 'texto' => 'Frambuesa'],
                    ['val' => 'MORA', 'texto' => 'Mora o Zarzamora'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 27
    ],

    // --- PANTALLA 27: DETALLE MEDICINALES (Imagen image_2a4b47.png) ---
    27 => [
        'id' => 27,
        'tipo' => 'formulario',
        'pregunta' => 'Plantas Medicinales',
        'subtitulo' => '',
        'condicion' => ['origen' => 19, 'valor' => 'MEDICINALES'],
        'campos' => [
            [
                'name' => 'detalle_medicinales', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'ARNICA', 'texto' => 'Árnica'],
                    ['val' => 'ROMERO', 'texto' => 'Romero'],
                    ['val' => 'RUDA', 'texto' => 'Ruda'],
                    ['val' => 'TORONJIL', 'texto' => 'Toronjil'],
                    ['val' => 'MANZANILLA', 'texto' => 'Manzanilla'],
                    ['val' => 'ALBAHACA', 'texto' => 'Albahaca'],
                    ['val' => 'EPAZOTE', 'texto' => 'Epazote'],
                    ['val' => 'HIERBABUENA', 'texto' => 'Hierbabuena'],
                    ['val' => 'DIENTE', 'texto' => 'Diente de León'],
['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente', // CAMBIO: Ahora sigue a Pecuaria
        'saltaA' => 28 
    ],

    // --- PANTALLA 28: CATEGORÍAS PECUARIAS (Imagen image_2a4f3d.png) ---
    // CONDICIÓN: Solo sale si en la 18 elegiste "PECUARIA"
    28 => [
        'id' => 28,
        'tipo' => 'formulario',
        'pregunta' => 'Producción Pecuaria',
        'subtitulo' => 'Marque todas las categorías que correspondan',
        'condicion' => ['origen' => 18, 'valor' => 'PECUARIA'], // <--- EL FILTRO
        'campos' => [
            [
                'name' => 'cats_pecuaria', 
                'label' => '', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'MAYOR', 'texto' => 'Ganadería mayor'],
                    ['val' => 'MENOR', 'texto' => 'Ganadería menor'],
                    ['val' => 'AVES', 'texto' => 'Aves'],
                    ['val' => 'APICULTURA', 'texto' => 'Apicultura']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 29
    ],

    // --- PANTALLA 29: GANADERÍA MAYOR (Imagen image_2a4f3d.png) ---
    // CONDICIÓN: Solo si en la 28 elegiste "MAYOR"
    29 => [
        'id' => 29,
        'tipo' => 'formulario',
        'pregunta' => 'Ganadería Mayor',
        'subtitulo' => 'Especifique el tipo',
        'condicion' => ['origen' => 28, 'valor' => 'MAYOR'],
        'campos' => [
            [
                'name' => 'detalle_mayor', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'BOVINOS', 'texto' => 'Bovinos (leche y carne)'],
                    ['val' => 'EQUINOS', 'texto' => 'Equinos (uso de tiro y crianza menor)']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 30
    ],

    // --- PANTALLA 30: GANADERÍA MENOR (Imagen image_2a4f3d.png) ---
    // CONDICIÓN: Solo si en la 28 elegiste "MENOR"
    30 => [
        'id' => 30,
        'tipo' => 'formulario',
        'pregunta' => 'Ganadería Menor',
        'subtitulo' => 'Especifique el tipo',
        'condicion' => ['origen' => 28, 'valor' => 'MENOR'],
        'campos' => [
            [
                'name' => 'detalle_menor', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'OVINOS', 'texto' => 'Ovinos'],
                    ['val' => 'CAPRINOS', 'texto' => 'Caprinos'],
                    ['val' => 'PORCINOS', 'texto' => 'Porcinos'],
                    ['val' => 'CONEJOS', 'texto' => 'Conejos'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 31
    ],

    // --- PANTALLA 31: AVES (Imagen image_2a4f44.png) ---
    // CONDICIÓN: Solo si en la 28 elegiste "AVES"
    31 => [
        'id' => 31,
        'tipo' => 'formulario',
        'pregunta' => 'Aves de Corral',
        'subtitulo' => 'Especifique el tipo',
        'condicion' => ['origen' => 28, 'valor' => 'AVES'],
        'campos' => [
            [
                'name' => 'detalle_aves', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'POSTURA', 'texto' => 'Gallinas de postura'],
                    ['val' => 'ENGORDA', 'texto' => 'Pollos de engorda'],
                    ['val' => 'DOBLE', 'texto' => 'Gallinas de doble propósito'],
                    ['val' => 'GUAJOLOTES', 'texto' => 'Guajolotes'],
                    ['val' => 'AVESTRUZ', 'texto' => 'Avestruz'],
                    ['val' => 'CODORNICES', 'texto' => 'Codornices'],
                    ['val' => 'OTRO', 'texto' => 'Otros']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 32
    ],

    // --- PANTALLA 32: APICULTURA (Imagen image_2a4f44.png) ---
    // CONDICIÓN: Solo si en la 28 elegiste "APICULTURA"
    32 => [
        'id' => 32,
        'tipo' => 'formulario',
        'pregunta' => 'Apicultura',
        'subtitulo' => 'Productos obtenidos',
        'condicion' => ['origen' => 28, 'valor' => 'APICULTURA'],
        'campos' => [
            [
                'name' => 'detalle_apicultura', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'PRODUCCION_ANIMAL', 'texto' => 'Producción animal'],
                    ['val' => 'POLEN', 'texto' => 'Polen'],
                    ['val' => 'PROPOLEO', 'texto' => 'Propóleo'],
                    ['val' => 'CERA', 'texto' => 'Cera'],
  ['val' => 'REUBICACION', 'texto' => 'Puede retirar y reubicar panales de abejas']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente', // CAMBIO: Ahora sigue a Huertos
        'saltaA' => 33
    ],

    // --- PANTALLA 33: CONFIGURACIÓN DEL HUERTO (Imagen image_2a52a4.png) ---
    // CONDICIÓN: Solo sale si en la 18 elegiste "HUERTO"
    33 => [
        'id' => 33,
        'tipo' => 'formulario',
        'pregunta' => 'Producción de Huertos',
        'subtitulo' => 'Características generales',
        'condicion' => ['origen' => 18, 'valor' => 'HUERTO'], // <--- EL FILTRO
        'campos' => [
            // Tipo de Huerto
            [
                'name' => 'tipo_huerto', 
                'label' => 'Tipo de Huerto', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'ESCOLARES', 'texto' => 'Huertos Escolares'],
                    ['val' => 'TRASPATIO', 'texto' => 'Huerto de Traspatio Familiar'],
                    ['val' => 'COMUNITARIO', 'texto' => 'Huerto Comunitario'],
                    ['val' => 'INSTITUCIONAL', 'texto' => 'Huerto institucional'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ],

            // Separador
            ['name' => 'sep_sis_prod', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Tipos de Sistema de Producción
            [
                'name' => 'sistema_produccion_huerto', 
                'label' => 'Tipos de Sistema de Producción', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'SUELO', 'texto' => 'Suelo'],
                    ['val' => 'MACETAS', 'texto' => 'Macetas'],
                    ['val' => 'CAMAS', 'texto' => 'Camas'],
                    ['val' => 'HIDROPONIA', 'texto' => 'Hidroponia'],
                    ['val' => 'ACUAPONIA', 'texto' => 'Acuaponia'],
                    ['val' => 'COMPOSTAJE', 'texto' => 'Compostaje'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 34
    ],

    // --- PANTALLA 34: CULTIVOS E INFRAESTRUCTURA (Imágenes 2a52a4 y 2a52c1) ---
    // CONDICIÓN: Solo si es HUERTO
    34 => [
        'id' => 34,
        'tipo' => 'formulario',
        'pregunta' => 'Detalle del Huerto',
        'subtitulo' => 'Cultivos e instalaciones actuales',
        'condicion' => ['origen' => 18, 'valor' => 'HUERTO'],
        'campos' => [
            // Tipos de Cultivos
            [
                'name' => 'cultivos_huerto', 
                'label' => 'Tipos de Cultivos', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'HORTALIZAS', 'texto' => 'Hortalizas'],
                    ['val' => 'MEDICINALES', 'texto' => 'Plantas medicinales'],
                    ['val' => 'ORNAMENTALES', 'texto' => 'Ornamentales'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ],

            // Separador
            ['name' => 'sep_infra', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Infraestructura
            [
                'name' => 'infraestructura_huerto', 
                'label' => '¿Qué infraestructura tiene?', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'RIEGO', 'texto' => 'Sistema de Riego'],
                    ['val' => 'INVERNADERO', 'texto' => 'Invernadero'],
                    ['val' => 'COMPOSTERA', 'texto' => 'Compostera o lombricompostera'],
                    ['val' => 'HERRAMIENTA', 'texto' => 'Herramienta básica'],
                    ['val' => 'MALLA', 'texto' => 'Malla sombra o techado'],
                    ['val' => 'CAPTACION', 'texto' => 'Captación de agua pluvial']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 35
    ],

    // --- PANTALLA 35: NECESIDADES (Imagen image_2a52c1.png) ---
    // CONDICIÓN: Solo si es HUERTO
    35 => [
        'id' => 35,
        'tipo' => 'formulario',
        'pregunta' => 'Necesidades',
        'subtitulo' => 'Requerimientos para el huerto',
        'condicion' => ['origen' => 18, 'valor' => 'HUERTO'],
        'campos' => [
            [
                'name' => 'necesidades_huerto', 
                'label' => 'Necesidades del Huerto', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'HERRAMIENTA', 'texto' => 'Herramienta'],
                    ['val' => 'AGUA', 'texto' => 'Riego o almacenamiento para agua'],
                    ['val' => 'INSUMOS', 'texto' => 'Semillas o abono orgánico'],
                    ['val' => 'INFRAESTRUCTURA', 'texto' => 'Malla sombra o infraestructura'],
                    ['val' => 'CAPACITACION', 'texto' => 'Capacitación técnica'],
                    ['val' => 'ESPACIOS', 'texto' => 'Espacios para vender lo producido'],
['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente', // CAMBIO: Ahora sigue a Granjas
        'saltaA' => 36
    ],

    // --- PANTALLA 36: CONFIGURACIÓN DE LA GRANJA (Imagen image_2a566a.png) ---
    // CONDICIÓN: Solo sale si en la 18 elegiste "GRANJA" (Granja Integral Familiar)
    36 => [
        'id' => 36,
        'tipo' => 'formulario',
        'pregunta' => 'Producción de Granjas',
        'subtitulo' => 'Características generales',
        'condicion' => ['origen' => 18, 'valor' => 'GRANJA'], // <--- EL FILTRO
        'campos' => [
            // Tipo de Granja
            [
                'name' => 'tipo_granja', 
                'label' => 'Tipo de granja', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'AVICOLA', 'texto' => 'Avícola'],
                    ['val' => 'CUNICOLA', 'texto' => 'Cunícola'],
                    ['val' => 'PORCINA', 'texto' => 'Porcina'],
                    ['val' => 'OVICAPRINA', 'texto' => 'Ovicaprina'],
                    ['val' => 'BOVINA', 'texto' => 'Bovina'], // Corregido (era Bobina)
                    ['val' => 'MIXTA', 'texto' => 'Mixta'],
                    ['val' => 'INTEGRAL', 'texto' => 'Integral']
                ]
            ],

            // Separador
            ['name' => 'sep_especies', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Especies
            [
                'name' => 'especies_granja', 
                'label' => 'Especies que cuentan en la granja', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'GALLINAS', 'texto' => 'Gallinas'], // Corregido (era Gallias)
                    ['val' => 'GUAJOLOTE', 'texto' => 'Guajolote'],
                    ['val' => 'CONEJOS', 'texto' => 'Conejos'],
                    ['val' => 'CERDOS', 'texto' => 'Cerdos'],
                    ['val' => 'BORREGOS', 'texto' => 'Borregos'],
                    ['val' => 'CABRAS', 'texto' => 'Cabras'],
                    ['val' => 'VACAS', 'texto' => 'Vacas'],
                    ['val' => 'ABEJAS', 'texto' => 'Abejas'], // Corregido (era Avejas)
                    ['val' => 'OTRAS', 'texto' => 'Otras']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 37
    ],

    // --- PANTALLA 37: DETALLE PRODUCTIVO GRANJA (Imagen image_2a56a2.png) ---
    // CONDICIÓN: Solo si es GRANJA
    37 => [
        'id' => 37,
        'tipo' => 'formulario',
        'pregunta' => 'Detalle Productivo',
        'subtitulo' => 'Alimentación, destino y necesidades',
        'condicion' => ['origen' => 18, 'valor' => 'GRANJA'],
        'campos' => [
            // Alimentación
            [
                'name' => 'alimentacion_granja', 
                'label' => 'Tipo de alimentación', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'COMERCIAL', 'texto' => 'Alimento Comercial'],
                    ['val' => 'NATURAL', 'texto' => 'Forraje natural'],
                    ['val' => 'DESPERDICIOS', 'texto' => 'Desperdicios de cocina'],
                    ['val' => 'MIXTO', 'texto' => 'Mixto']
                ]
            ],

            // Separador
            ['name' => 'sep_prod_obt', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Productos obtenidos
            [
                'name' => 'productos_granja', 
                'label' => 'Productos que obtiene', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'HUEVO', 'texto' => 'Huevo'],
                    ['val' => 'CARNE', 'texto' => 'Carne'],
                    ['val' => 'LECHE', 'texto' => 'Leche'],
                    ['val' => 'MIEL', 'texto' => 'Miel'],
                    ['val' => 'COMPOSTA', 'texto' => 'Composta'],
                    ['val' => 'ABONO', 'texto' => 'Abono orgánico']
                ]
            ],

            // Separador
            ['name' => 'sep_destino_gr', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Destino
            [
                'name' => 'destino_granja', 
                'label' => 'Destino de la producción', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'AUTOCONSUMO', 'texto' => 'Autoconsumo'],
                    ['val' => 'VENTA_LOCAL', 'texto' => 'Venta Local'],
                    ['val' => 'TRUEQUE', 'texto' => 'Trueque/intercambio'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ],

            // Separador
            ['name' => 'sep_necesidades_gr', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Necesidades
            [
                'name' => 'necesidades_granja', 
                'label' => 'Principales necesidades', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'CAPACITACION', 'texto' => 'Capacitación'],
                    ['val' => 'INFRAESTRUCTURA', 'texto' => 'Infraestructura'],
                    ['val' => 'EQUIPAMIENTO', 'texto' => 'Equipamiento'],
 ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente', // CAMBIO: Ahora sigue a Transformación
        'saltaA' => 38
    ],

    // --- PANTALLA 38: MENÚ DE TRANSFORMACIÓN (Imagen image_2aa8db.png) ---
    // CONDICIÓN: Solo sale si en la 18 elegiste "TRANSFORMADORA"
    38 => [
        'id' => 38,
        'tipo' => 'formulario',
        'pregunta' => 'Transformación',
        'subtitulo' => 'Materia prima que transforma',
        'condicion' => ['origen' => 18, 'valor' => 'TRANSFORMADORA'], // <--- EL FILTRO PRINCIPAL
        'campos' => [
            [
                'name' => 'cats_transformacion', 
                'label' => 'Seleccione todas las que apliquen', 
                'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'MIEL', 'texto' => 'Miel'],
                    ['val' => 'MAIZ', 'texto' => 'Maíz y otros granos'],
                    ['val' => 'LECHE', 'texto' => 'Leche'],
                    ['val' => 'CARNE', 'texto' => 'Carne'],
                    ['val' => 'FRUTAS', 'texto' => 'Frutas'],
                    ['val' => 'HORTALIZAS', 'texto' => 'Hortalizas'],
                    ['val' => 'MEDICINALES', 'texto' => 'Plantas Medicinales'], // Corregido (era Medicianales)
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 39
    ],

    // --- PANTALLA 39: DETALLE MIEL (Imagen image_2aa8db.png) ---
    // CONDICIÓN: Solo si eligió MIEL en la 38
    39 => [
        'id' => 39,
        'tipo' => 'formulario',
        'pregunta' => 'Derivados de Miel',
        'subtitulo' => 'Productos elaborados',
        'condicion' => ['origen' => 38, 'valor' => 'MIEL'],
        'campos' => [
            [
                'name' => 'det_trans_miel', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'JABONES', 'texto' => 'Jabones'],
                    ['val' => 'BALSAMOS', 'texto' => 'Bálsamos'],
                    ['val' => 'CREMAS', 'texto' => 'Cremas'],
                    ['val' => 'DULCES', 'texto' => 'Dulces'],
                    ['val' => 'OTROS', 'texto' => 'Otros']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 40
    ],

    // --- PANTALLA 40: DETALLE MAÍZ (Imagen image_2aa8db.png) ---
    // CONDICIÓN: Solo si eligió MAIZ en la 38
    40 => [
        'id' => 40,
        'tipo' => 'formulario',
        'pregunta' => 'Derivados de Maíz',
        'subtitulo' => 'Productos elaborados',
        'condicion' => ['origen' => 38, 'valor' => 'MAIZ'],
        'campos' => [
            [
                'name' => 'det_trans_maiz', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'TORTILLAS', 'texto' => 'Tortillas'],
                    ['val' => 'HARINAS', 'texto' => 'Harinas'],
                    ['val' => 'PINOLE', 'texto' => 'Pinole'],
                    ['val' => 'PANADERIA', 'texto' => 'Panqués, pasteles, galletas'],
                    ['val' => 'TAMALES', 'texto' => 'Tamales'],
                    ['val' => 'ANTOJITOS', 'texto' => 'Gorditas, Tlacoyos, etc'],
                    ['val' => 'CERVEZA', 'texto' => 'Cerveza'],
                    ['val' => 'LICORES', 'texto' => 'Licores'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 41
    ],

    // --- PANTALLA 41: DETALLE LECHE (Imagen image_2aa8ff.png) ---
    // CONDICIÓN: Solo si eligió LECHE en la 38
    41 => [
        'id' => 41,
        'tipo' => 'formulario',
        'pregunta' => 'Derivados de Leche',
        'subtitulo' => 'Productos elaborados',
        'condicion' => ['origen' => 38, 'valor' => 'LECHE'],
        'campos' => [
            [
                'name' => 'det_trans_leche', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'QUESO', 'texto' => 'Queso'],
                    ['val' => 'YOGURT', 'texto' => 'Yogurt'],
                    ['val' => 'CREMA', 'texto' => 'Crema'],
                    ['val' => 'CAJETA', 'texto' => 'Cajeta'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 42
    ],

    // --- PANTALLA 42: DETALLE FRUTAS (Imagen image_2aa8ff.png) ---
    // CONDICIÓN: Solo si eligió FRUTAS en la 38
    42 => [
        'id' => 42,
        'tipo' => 'formulario',
        'pregunta' => 'Transformación de Frutas',
        'subtitulo' => 'Productos elaborados',
        'condicion' => ['origen' => 38, 'valor' => 'FRUTAS'],
        'campos' => [
            [
                'name' => 'det_trans_frutas', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'CONSERVAS', 'texto' => 'Conservas'],
                    ['val' => 'MERMELADAS', 'texto' => 'Mermeladas'],
                    ['val' => 'SALSAS', 'texto' => 'Salsas'],
                    ['val' => 'LICORES', 'texto' => 'Licores artesanales'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 43
    ],

    // --- PANTALLA 43: DETALLE HORTALIZAS (Imagen image_2aa8ff.png) ---
    // CONDICIÓN: Solo si eligió HORTALIZAS en la 38
    43 => [
        'id' => 43,
        'tipo' => 'formulario',
        'pregunta' => 'Transformación de Hortalizas',
        'subtitulo' => 'Productos elaborados',
        'condicion' => ['origen' => 38, 'valor' => 'HORTALIZAS'],
        'campos' => [
            [
                'name' => 'det_trans_hortalizas', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'ENCURTIDOS', 'texto' => 'Encurtidos'],
                    ['val' => 'DESHIDRATADOS', 'texto' => 'Deshidratados'],
                    ['val' => 'SALSAS', 'texto' => 'Salsas'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 44
    ],

    // --- PANTALLA 44: DETALLE CARNE (Imagen image_2aa8ff.png) ---
    // CONDICIÓN: Solo si eligió CARNE en la 38
    44 => [
        'id' => 44,
        'tipo' => 'formulario',
        'pregunta' => 'Transformación de Carne',
        'subtitulo' => 'Productos elaborados',
        'condicion' => ['origen' => 38, 'valor' => 'CARNE'],
        'campos' => [
            [
                'name' => 'det_trans_carne', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'BARBACOA', 'texto' => 'Barbacoa'],
                    ['val' => 'LONGANIZA', 'texto' => 'Longaniza'],
                    ['val' => 'SALCHICHAS', 'texto' => 'Salchichas'],
                    ['val' => 'HAMBURGUESAS', 'texto' => 'Hamburguesas'],
                    ['val' => 'EMBUTIDOS', 'texto' => 'Embutidos'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 45
    ],

    // --- PANTALLA 45: DETALLE MEDICINALES (Imagen image_2aa91d.png) ---
    // CONDICIÓN: Solo si eligió MEDICINALES en la 38
    45 => [
        'id' => 45,
        'tipo' => 'formulario',
        'pregunta' => 'Plantas Medicinales',
        'subtitulo' => 'Productos elaborados',
        'condicion' => ['origen' => 38, 'valor' => 'MEDICINALES'],
        'campos' => [
            [
                'name' => 'det_trans_medicinales', 'label' => '', 'tipo' => 'checkbox', 
                'opciones' => [
                    ['val' => 'CREMAS', 'texto' => 'Cremas'],
                    ['val' => 'INFUSIONES', 'texto' => 'Infusiones'],
                    ['val' => 'POMADAS', 'texto' => 'Pomadas'],
                    ['val' => 'OTRO', 'texto' => 'Otro']
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 46
    ],

    // --- PANTALLA 46: ESPECIFICAR OTRO (Imagen image_2aa91d.png) ---
    // CONDICIÓN: Solo si eligió OTRO en la 38
    // Esta es la última pregunta del formulario.
    46 => [
        'id' => 46,
        'tipo' => 'formulario',
        'pregunta' => 'Otros Productos',
        'subtitulo' => 'Especifique qué otros productos elabora',
        'condicion' => ['origen' => 38, 'valor' => 'OTRO'],
        'campos' => [
 ['name' => 'det_trans_otro_texto', 'label' => 'Describa el producto', 'tipo' => 'text', 'placeholder' => 'Especifique...']
        ],
        'boton_texto' => 'Siguiente', // CAMBIO: Seguimos a la sección final
        'saltaA' => 47
    ],

    // --- PANTALLA 47: DATOS DE PRODUCCIÓN (Imagen image_2aad36.png) ---
    // Esta pantalla le sale a TODOS (ya no tiene 'condicion')
    47 => [
        'id' => 47,
        'tipo' => 'formulario',
        'pregunta' => 'Métricas de Producción',
        'subtitulo' => 'Superficie y volumen total',
        'campos' => [
            // Superficie
            ['name' => 'superficie_prod', 'label' => '¿Cuál es la superficie destinada a la producción? (Hectáreas)', 'tipo' => 'text', 'placeholder' => 'Ej. 2.5'],
            
            // Separador
            ['name' => 'sep_volumen', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Volumen (Cantidad numérica)
            ['name' => 'volumen_prod', 'label' => '¿Cuál es el volumen total de producción obtenida?', 'tipo' => 'text', 'placeholder' => 'Ingrese cantidad (Ej. 500)']
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 48
    ],

    // --- PANTALLA 48: UNIDAD DE MEDIDA (Imagen image_2aad36.png) ---
    48 => [
        'id' => 48,
        'tipo' => 'formulario',
        'pregunta' => 'Unidad de Medida',
        'subtitulo' => 'Seleccione la unidad correspondiente al volumen',
        'campos' => [
            [
                'name' => 'unidad_medida', 
                'label' => '', 
                'tipo' => 'radio', 
                'opciones' => [
                    ['val' => 'KG', 'texto' => 'Kilogramos (kg)'],
                    ['val' => 'TON', 'texto' => 'Toneladas (t)'],
                    ['val' => 'LITROS', 'texto' => 'Litros (L)'],
                    ['val' => 'PIEZAS', 'texto' => 'Piezas'],
                    ['val' => 'COSTALES', 'texto' => 'Costales'],
                    ['val' => 'PACAS_MEC', 'texto' => 'Pacas mecánicas'],
                    ['val' => 'PACAS_AUT', 'texto' => 'Pacas automáticas'], // Corregido el typo "Automaticcas"
                    ['val' => 'ANIMALES', 'texto' => 'Animales'],
                    ['val' => 'COLMENAS', 'texto' => 'Colmenas'],
                    ['val' => 'OTRO', 'texto' => 'Otro (especifique)'] // <--- ESTE ACTIVA LA SIGUIENTE
                ]
            ]
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 49
    ],

    // --- PANTALLA 49: ESPECIFICAR OTRA UNIDAD (Condicional) ---
    // CONDICIÓN: Solo sale si en la 48 eligieron "OTRO"
    49 => [
        'id' => 49,
        'tipo' => 'formulario',
        'pregunta' => 'Otra Unidad',
        'subtitulo' => 'Especifique la unidad de medida',
        'condicion' => ['origen' => 48, 'valor' => 'OTRO'], // <--- AQUÍ ESTÁ LA LÓGICA QUE PEDISTE
        'campos' => [
            ['name' => 'otra_unidad_texto', 'label' => '¿Qué otra unidad?', 'tipo' => 'text', 'placeholder' => 'Especifique...']
        ],
        'boton_texto' => 'Siguiente',
        'saltaA' => 50
    ],

    // --- PANTALLA 50: CIERRE Y COMENTARIOS (Imagen image_2aad36.png) ---
    50 => [
        'id' => 50,
        'tipo' => 'formulario',
        'pregunta' => 'Cierre del Censo',
        'subtitulo' => 'Capacitación y observaciones finales',
        'campos' => [
            // Capacitaciones abiertas
            ['name' => 'capacitaciones_deseadas', 'label' => '¿Qué capacitaciones le gustaría tomar?', 'tipo' => 'text', 'placeholder' => 'Describa los temas de interés'],
            
            // Separador
            ['name' => 'sep_obs', 'label' => '<hr style="margin: 25px 0; border-top: 1px solid #eee;">', 'tipo' => 'html'],

            // Observaciones
            ['name' => 'observaciones', 'label' => 'Observaciones o comentarios adicionales', 'tipo' => 'text', 'placeholder' => 'Escriba aquí...']
        ],
        'boton_texto' => 'Finalizar Encuesta',
        'saltaA' => 51
    ],

    // --- FIN DEFINITIVO DEL SISTEMA ---
    51 => [ 'tipo' => 'fin' ]
];
    }
}
