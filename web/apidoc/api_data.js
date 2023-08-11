define({ "api": [
  {
    "type": "post",
    "url": "/agregarnovedad",
    "title": "Agregar Novedad",
    "name": "AgregarNovedad",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/agregarnovedad/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_viaje",
            "description": "<p>Id del viaje donde se agregara la novedad</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_parada",
            "description": "<p>Id de la parada donde se agregara la novedad</p>"
          },
          {
            "group": "Parameter",
            "type": "String[base64]",
            "optional": false,
            "field": "fotos",
            "description": "<p>Arreglo de Fotos de la novedad.</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "subestatus_viaje_id",
            "description": "<p>Id del sub estado creado en TMS</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "subestatus_viaje_motivo",
            "description": "<p>Id del sub estado motivo creado en TMS, depende del sub estado.</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "observaciones",
            "description": "<p>Observación de la novedad.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "post",
    "url": "/agregarpod",
    "title": "Agregar POD",
    "name": "AgregarPOD",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/agregarpod/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nro_viaje",
            "description": "<p>nro_ Num_OS + &quot;/&quot; + Num_WO + &quot;/&quot; + Contenedor</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_parada",
            "description": "<p>Id de parada donde se agregara el POD</p>"
          },
          {
            "group": "Parameter",
            "type": "String[base64]",
            "optional": false,
            "field": "fotos",
            "description": "<p>Arreglo de Fotos del POD.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "nombre_firma",
            "description": "<p>Nombre de la persona que firma</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "empresa_firma",
            "description": "<p>Nombre de la empresa que firma</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "rut_firma",
            "description": "<p>Rut de la persona que firma</p>"
          },
          {
            "group": "Parameter",
            "type": "base64",
            "optional": true,
            "field": "firma",
            "description": "<p>Imagen de la firma del POD</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "estatus_pod",
            "description": "<p>Estado del POD<ul><li>1 para Entregado</li><li>2 para Entregado Parcial</li><li>3 para Rechazado</li><li>4 Para No entregado</li></ul></p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "post",
    "url": "/cambioestado",
    "title": "Cambio Estado",
    "name": "CambioEstado",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/cambioestado/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_parada",
            "description": "<p>id de la parada a consultar.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_conductor",
            "description": "<p>id del conductor que inicio sesion en la aplicación.</p>"
          },
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "id_accion",
            "description": "<p>Id de la accion a enviar. <br><ul><li>1 - Presentación</li> <li>2 - Documentos</li> <li>3 - En espera</li> <li>4 - Aculatado</li> </ul>.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "fecha",
            "description": "<p>Fecha y hora del envio del estado. (Formato: YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "latitud",
            "description": "<p>latitud del gps.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "longitud",
            "description": "<p>longitud del gps.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "get",
    "url": "/categoriaRendicion",
    "title": "Categoría Rendición",
    "name": "CategoriaRendicion",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/categoriaRendicion/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "post",
    "url": "/engancherampla",
    "title": "Enganche Rampla",
    "name": "EngancheRampla",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/engancherampla/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_parada",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "rampla",
            "description": "<p>rampla enganchada, si se envia nulo, se tomara como desenganche.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "post",
    "url": "/errorrampla",
    "title": "Error de Rampla",
    "name": "EngancheRampla",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/errorrampla/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_parada",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "rampla_correcta",
            "description": "<p>rampla_correcta reportada para modificar la rampla erronea.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "observacion",
            "description": "<p>observacaiones del reporte de error.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "get",
    "url": "/estadonovedad",
    "title": "Estados de novedades",
    "name": "EstadoNovedad",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/estadonovedad/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "post",
    "url": "/estadosnovedades",
    "title": "Estados Novedades",
    "name": "EstadosNovedades",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/estadosnovedades/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "post",
    "url": "/estadospod",
    "title": "Estados POD",
    "name": "EstadosPod",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/estadospod/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "get",
    "url": "/listadonovedadesporviaje",
    "title": "Listado de novedades por viaje",
    "name": "Listado_Novedades_por_viaje",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/listadonovedadesporviaje/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nro_viaje",
            "description": "<p>nro_ Num_OS + &quot;/&quot; + Num_WO + &quot;/&quot; + Contenedor</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "get",
    "url": "/listadopodporviaje",
    "title": "Listado de POD por viaje",
    "name": "Listado_pod_por_viaje",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/listadopodporviaje/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_viaje",
            "description": "<p>Id del viaje de la parada a consultar</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_parada",
            "description": "<p>Id de parada de POD a consultar</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "get",
    "url": "/motivoRendicion",
    "title": "Motivo Rendición",
    "name": "MotivoRendicion",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/motivoRendicion/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "categoria_rendicion_id",
            "description": "<p>Id de la categoria seleccionada.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "get",
    "url": "/tipodocumento",
    "title": "Tipo de documento Rendición",
    "name": "TipoDocumento",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/tipodocumento/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "post",
    "url": "/accionesporparada",
    "title": "Acciones Parada",
    "name": "por",
    "group": "Acciones",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/accionesporparada/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_parada",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Acciones"
  },
  {
    "type": "post",
    "url": "/setdatoscarga",
    "title": "Crear Datos Carga",
    "name": "DatosCarga",
    "group": "Datos_Carga",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/setdatoscarga/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "id_viaje",
            "description": "<p>Id del viaje donde se guardaran los datos.</p>"
          },
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "id_parada",
            "description": "<p>Id de la parada donde se guardaran los datos.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nombre_campo",
            "description": "<p>Nombre del campo a guardar.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "valor_campo",
            "description": "<p>Valor del campo a guardar.</p>"
          },
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "id_conductor",
            "description": "<p>Id del conductor que subio el documento.</p>"
          },
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "id_dato_carga",
            "description": "<p>Id del dato carga a modificar.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Datos_Carga"
  },
  {
    "type": "post",
    "url": "/errordatoscarga",
    "title": "Error Datos Carga",
    "name": "ErrorDatosCarga",
    "group": "Datos_Carga",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/errordatoscarga/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "id_dato_carga",
            "description": "<p>Id del dato que tiene un error.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "error_campo",
            "description": "<p>Error en el campo.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nuevo_valor",
            "description": "<p>Nuevo valor del campo.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "observaciones",
            "description": "<p>Observaciones o comentarios de dato erroneo.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Datos_Carga"
  },
  {
    "type": "get",
    "url": "/getdatoscarga",
    "title": "Traer Datos Carga",
    "name": "GetDatosCarga",
    "group": "Datos_Carga",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/getdatoscarga/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "id_parada",
            "description": "<p>Id de la parada a consultar.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Datos_Carga"
  },
  {
    "type": "post",
    "url": "/login",
    "title": "Login app movil",
    "name": "Login",
    "group": "Login",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/login/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "usuario",
            "description": "<p>usuario o teléfono de conductor.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "clave",
            "description": "<p>clave de conductor.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Login"
  },
  {
    "type": "post",
    "url": "/crearparadas",
    "title": "Crear paradas",
    "name": "CrearParadas",
    "group": "Paradas",
    "version": "2.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/crearparadas/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nro_viaje",
            "description": "<p>Nro asignado al viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "poligono_parada",
            "description": "<p>ID de polígono la parada nueva del viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_entrada",
            "description": "<p>Fecha y hora de entrada a esa parada. (Formato: YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_salida",
            "description": "<p>Fecha y hora de salida a esa parada. (Formato: YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "poligono_poligono_parada_anterior",
            "description": "<p>ID de polígono de parada anterior, parada del viaje. <code>La parada nueva se inserta despues de esta dirección</code></p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Paradas"
  },
  {
    "type": "get",
    "url": "/detalleparada",
    "title": "Detalle parada",
    "name": "DetalleParada",
    "group": "Paradas",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/detalleparada/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_parada",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Paradas"
  },
  {
    "type": "post",
    "url": "/editarparada",
    "title": "Editar parada",
    "name": "EditarParada",
    "group": "Paradas",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/editarparada/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nro_viaje",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "poligono_parada",
            "description": "<p>Id del poligono de parada del viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "poligono_parada_nueva",
            "description": "<p>Id del poligono de parada que se inserta en el viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_entrada",
            "description": "<p>Fecha y hora de entrada a esa parada. (Formato: YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_salida",
            "description": "<p>Fecha y hora de salida a esa parada. (Formato: YYYY-MM-DD HH:MM:SS)</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Paradas"
  },
  {
    "type": "post",
    "url": "/eliminarparada",
    "title": "Eliminar parada",
    "name": "EliminarParada",
    "group": "Paradas",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/eliminarparada/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "viaje_id",
            "description": "<p>Id del viaje que contiene la parada que se eliminara</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "zona_id",
            "description": "<p>Id de la zona del viaje que e eliminara</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Paradas"
  },
  {
    "type": "post",
    "url": "/llegadaparada",
    "title": "Llegada a Parada",
    "name": "LlegadaAParada",
    "group": "Paradas",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/llegadaparada/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_parada",
            "description": "<p>id de la parada a consultar.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_conductor",
            "description": "<p>id del conductor que inicio sesion en la aplicación.</p>"
          },
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "id_accion",
            "description": "<p>Id de la accion a enviar. <br><ul><li>1 - Presentación</li> <li>2 - Documentos</li> <li>3 - En espera</li> <li>4 - Aculatado</li> </ul>.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "fecha_presentacion",
            "description": "<p>Fecha y hora de entrada a esa parada. (Formato: YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "latitud",
            "description": "<p>latitud del gps.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "longitud",
            "description": "<p>longitud del gps.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Paradas"
  },
  {
    "type": "get",
    "url": "/enviodatosprefactura",
    "title": "Envio Datos Prefactura",
    "name": "EnvioDatosPrefactura",
    "group": "Prefactura",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/enviodatosprefactura"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     respuesta = descripción del estado;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Prefactura"
  },
  {
    "type": "get",
    "url": "/envioncprefactura",
    "title": "Envio NC Prefactura",
    "name": "EnvioNCPrefactura",
    "group": "Prefactura",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/envioncprefactura"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     respuesta = descripción del estado;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Prefactura"
  },
  {
    "type": "post",
    "url": "/recibodatosprefactura",
    "title": "Recibo Datos Prefactura",
    "name": "ReciboDatosPrefactura",
    "group": "Prefactura",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/recibodatosprefactura/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "prefactura_id",
            "description": "<p>Id de la prefactura.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nro_factura",
            "description": "<p>Número de la factura realizada por el ERP. Si el estado es ok, enviar nro_factura.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "descripcion",
            "description": "<p>Si el estado es error, enviar descripción del error.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "estado",
            "description": "<p>Se devuelve un estado ok | error en minúsculas.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     respuesta = descripción del estado;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Prefactura"
  },
  {
    "type": "post",
    "url": "/reciboncprefactura",
    "title": "Recibo NC Prefactura",
    "name": "ReciboNCPrefactura",
    "group": "Prefactura",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/reciboncprefactura/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "prefactura_id",
            "description": "<p>Id de la prefactura.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nro_nc",
            "description": "<p>Número de la factura realizada por el ERP. Si el estado es ok, enviar nro_factura.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "descripcion",
            "description": "<p>Si el estado es error, enviar descripción del error.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "estado",
            "description": "<p>Se devuelve un estado ok | error en minúsculas.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     respuesta = descripción del estado;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Prefactura"
  },
  {
    "type": "get",
    "url": "/rendicionviaje",
    "title": "Rendicion por viaje",
    "name": "RendicionViaje",
    "group": "Rendicion",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/rendicionviaje/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "viaje_id",
            "description": "<p>Id del Viaje.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     respuesta = descripción del estado;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Rendicion"
  },
  {
    "type": "get",
    "url": "/rendiciones",
    "title": "Rendiciones",
    "name": "Rendiciones",
    "group": "Rendicion",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/rendiciones/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     respuesta = descripción del estado;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Rendicion"
  },
  {
    "type": "post",
    "url": "/rendicionescentralizar",
    "title": "Centralizar Rendiciones",
    "name": "RendicionesCentralizar",
    "group": "Rendicion",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/rendicionescentralizar/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "viaje_id",
            "description": "<p>Id del Viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "rendicion_id",
            "description": "<p>Id de la rendicion.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     respuesta = descripción del estado;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Rendicion"
  },
  {
    "type": "get",
    "url": "/accesosistema",
    "title": "Detalle Acceso al sistema",
    "name": "AccesoSistema",
    "group": "Reportes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/accesosistema/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_inicio",
            "description": "<p>Fecha y hora de consulta. (Formato:YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_fin",
            "description": "<p>Fecha y hora de consulta. (Formato:YYYY-MM-DD HH:MM:SS)</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Reportes"
  },
  {
    "type": "get",
    "url": "/vehiculostms",
    "title": "Vehiculos TMS",
    "name": "VehiculosTms",
    "group": "Reportes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/vehiculostms/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Reportes"
  },
  {
    "type": "get",
    "url": "/visitas",
    "title": "Detalle visitas a vistas",
    "name": "Visitas",
    "group": "Reportes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/visitas/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_inicio",
            "description": "<p>Fecha y hora de consulta. (Formato:YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_fin",
            "description": "<p>Fecha y hora de consulta. (Formato:YYYY-MM-DD HH:MM:SS)</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Reportes"
  },
  {
    "type": "get",
    "url": "/enviovale",
    "title": "Envio Vales a ERP",
    "name": "EnvioVale",
    "group": "Vales",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/enviovale"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     respuesta = descripción del estado;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Vales"
  },
  {
    "type": "post",
    "url": "/recibovale",
    "title": "Recibo OK Vale",
    "name": "ReciboVale",
    "group": "Vales",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/recibovale/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "vale_id",
            "description": "<p>Id del Vale.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "descripcion",
            "description": "<p>Si el estado es error, enviar descripción del error.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "estado",
            "description": "<p>Se devuelve un estado ok | error en minúsculas.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     respuesta = descripción del estado;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Vales"
  },
  {
    "type": "post",
    "url": "/validarpatentesubdominio",
    "title": "Valida la patente en el subdominio ingresado",
    "name": "Validarpatentesubdominio",
    "group": "Validarpatentesubdominio",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/Validarpatentesubdominio/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "patente",
            "description": "<p>Patente ingreasda para inicio de sesión manual.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "subdominio",
            "description": "<p>subdominio ingresado para inicio de sesión manual.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Validarpatentesubdominio"
  },
  {
    "type": "post",
    "url": "/anularviaje",
    "title": "Anular viaje",
    "name": "AnularViaje",
    "group": "Viajes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/anularviaje/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nro_viaje",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Viajes"
  },
  {
    "type": "post",
    "url": "/crearviajes",
    "title": "Crear Viaje",
    "name": "CrearViaje",
    "group": "Viajes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/crearviaje/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nro_viaje",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "unidad_negocio",
            "description": "<p>Unidad de negocio del viaje. <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "tipo_operacion",
            "description": "<p>tipo de operacion del viaje o Categoría de carga.  <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "tipo_servicio",
            "description": "<p>tipo de servicio del viaje o subcategoría de carga.  <code>Si no existe, se creará y se asignara al tipo de operacion especificada</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "rut",
            "description": "<p>rut de cliente asignado al viaje. Ejemplo: 00000000-0 <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "cliente",
            "description": "<p>nombre cliente asignado al viaje. <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "tipo_carga_nombre",
            "description": "<p>Tipo de carga del viaje. <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "tipo_carga_codigo",
            "description": "<p>Codigo del Tipo de carga del viaje. <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "transportista_rut",
            "description": "<p>Rut del Transportista asignado al viaje. <code>Si no existe, se creará y se asignaran conductores y vehículos enviados</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "transportista_nombre",
            "description": "<p>Transportista asignado al viaje. <code>Si no existe, se creará y se asignaran conductores y vehículos enviados</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "conductor_uno_rut",
            "description": "<p>Rut del Conductor principal asignado al viaje. <code>Si no existe, se creará y se asignara al transportista enviado</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "conductor_uno_nombre",
            "description": "<p>Nombre y Apellido del conductor principal asignado al viaje. <code>Si no existe, se creará y se asignara al transportista enviado</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "conductor_dos_rut",
            "description": "<p>Rut del Conductor secundario asignado al viaje. <code>Si no existe, se creará y se asignara al transportista enviado</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "conductor_dos_nombre",
            "description": "<p>Nombre y Apellido conductor secundario asignado al viaje. <code>Si no existe, se creará y se asignara al transportista enviado</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "vehiculo_uno",
            "description": "<p>Vehículo principal asignado al viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "vehiculo_dos",
            "description": "<p>Vehículo secundario asignado al viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "poligono_origen",
            "description": "<p>ID del polígono de origen o inicio del viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_entrada_origen",
            "description": "<p>Fecha y hora de entrada al origen del viaje. (Formato:YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_salida_origen",
            "description": "<p>Fecha y hora de salida del origen del viaje. Formato:(YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "poligono_destino",
            "description": "<p>ID del polígono destino o fin de viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_entrada_destino",
            "description": "<p>Fecha y hora de entrada al destino del viaje. (Formato:YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_salida_destino",
            "description": "<p>Fecha y hora de salida del destino del viaje. Formato:(YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "rut_facturador",
            "description": "<p>rut de cliente facturador asignado al viaje. Ejemplo: 00.000.000-0 <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "cliente_facturador",
            "description": "<p>nombre cliente facturador asignado al viaje. <code>Si no existe, se creará</code></p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n     viaje_id = valor entero con el id del viaje agregado;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Viajes"
  },
  {
    "type": "get",
    "url": "/documentosviaje",
    "title": "Documentos Viaje",
    "name": "DocumentosViaje",
    "group": "Viajes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/documentosviaje/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id_viaje",
            "description": "<p>Id del viaje a consultar.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Viajes"
  },
  {
    "type": "post",
    "url": "/editarviaje",
    "title": "Editar Viaje",
    "name": "EditarViaje",
    "group": "Viajes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/editarviaje/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "nro_viaje",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "unidad_negocio",
            "description": "<p>Unidad de negocio del viaje. <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "tipo_operacion",
            "description": "<p>tipo de operacion del viaje o Categoría de carga.  <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "tipo_servicio",
            "description": "<p>tipo de servicio del viaje o subcategoría de carga.  <code>Si no existe, se creará y se asignara al tipo de operacion especificada</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "rut",
            "description": "<p>rut de cliente asignado al viaje. Ejemplo: 00000000-0 <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "cliente",
            "description": "<p>nombre cliente asignado al viaje. <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "tipo_carga_nombre",
            "description": "<p>Tipo de carga del viaje. <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "tipo_carga_codigo",
            "description": "<p>Codigo del Tipo de carga del viaje. <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "tranportista_rut",
            "description": "<p>Rut del Transportista asignado al viaje. <code>Si no existe, se creará y se asignaran conductores y vehículos enviados</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "tranportista_nombre",
            "description": "<p>Transportista asignado al viaje. <code>Si no existe, se creará y se asignaran conductores y vehículos enviados</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "conductor_uno_rut",
            "description": "<p>Rut del Conductor principal asignado al viaje. <code>Si no existe, se creará y se asignara al transportista enviado</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "conductor_uno_nombre",
            "description": "<p>Nombre y Apellido del conductor principal asignado al viaje. <code>Si no existe, se creará y se asignara al transportista enviado</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "conductor_dos_rut",
            "description": "<p>Rut del Conductor secundario asignado al viaje. <code>Si no existe, se creará y se asignara al transportista enviado</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "conductor_dos_nombre",
            "description": "<p>Nombre y Apellido conductor secundario asignado al viaje. <code>Si no existe, se creará y se asignara al transportista enviado</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "vehiculo_uno",
            "description": "<p>Vehículo principal asignado al viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "vehiculo_dos",
            "description": "<p>Vehículo secundario asignado al viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "poligono_origen",
            "description": "<p>ID del polígono de origen o inicio del viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_entrada_origen",
            "description": "<p>Fecha y hora de entrada al origen del viaje. (Formato:YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_salida_origen",
            "description": "<p>Fecha y hora de salida del origen del viaje. Formato:(YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "poligono_destino",
            "description": "<p>ID del polígono destino o fin de viaje.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_entrada_destino",
            "description": "<p>Fecha y hora de entrada al destino del viaje. (Formato:YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "fecha_salida_destino",
            "description": "<p>Fecha y hora de salida del destino del viaje. Formato:(YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "rut_facturador",
            "description": "<p>rut de cliente facturador asignado al viaje. Ejemplo: 00.000.000-0 <code>Si no existe, se creará</code></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "cliente_facturador",
            "description": "<p>nombre cliente facturador asignado al viaje. <code>Si no existe, se creará</code></p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n     viaje_id = valor entero con el id del viaje editado;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Viajes"
  },
  {
    "type": "post",
    "url": "/guardardocumentosviaje",
    "title": "Guardar Documentos Viaje",
    "name": "GuardarDocumetosViaje",
    "group": "Viajes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/guardardocumentosviaje/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "viaje_id",
            "description": "<p>Id del viaje donde se guardara el documento.</p>"
          },
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "parada_id",
            "description": "<p>Id de la parada donde se guardara el documento.</p>"
          },
          {
            "group": "Parameter",
            "type": "Int",
            "optional": true,
            "field": "id_documento",
            "description": "<p>Id del documento a guardar, si es opcional enviar en nulo.</p>"
          },
          {
            "group": "Parameter",
            "type": "base64",
            "optional": false,
            "field": "documento",
            "description": "<p>Archivo enviado.</p>"
          },
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "id_conductor",
            "description": "<p>Id del conductor que subio el documento.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "observaciones",
            "description": "<p>Observacion del documento subido.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Viajes"
  },
  {
    "type": "get",
    "url": "/listadoviajes",
    "title": "Listado de viajes por vehiculo",
    "name": "ListadoViajeVehiculo",
    "group": "Viajes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/listadoviajesvehiculo/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "patente",
            "description": "<p>Patente del vehículo</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "conductor_id",
            "description": "<p>Id del conductor que inicio sesión</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "accion_operacion",
            "description": "<p>Numero para consultar el servicio <ul><li>0 viajes activos del dia actual</li><li>1 viajes activos del dia de mañana</li><li>2 viajes completados dia actual</li></ul></p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Viajes"
  },
  {
    "type": "post",
    "url": "/listadoviajeshistorico",
    "title": "Listado de viajes historico",
    "name": "ListadoViajeVehiculoHistorico",
    "group": "Viajes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/listadoviajeshistorico/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Autorizacion",
            "description": "<p>API_KEY de usuario desarrollador.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "patente",
            "description": "<p>Patente del vehículo</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "fecha_desde",
            "description": "<p>Fecha inicio para buscar viajes, solo si accion_operacion es igual a 6. (Formato:YYYY-MM-DD HH:MM:SS)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "fecha_hasta",
            "description": "<p>Fecha inicio para buscar viajes, solo si accion_operacion es igual a 6. (Formato:YYYY-MM-DD HH:MM:SS)</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Viajes"
  },
  {
    "type": "post",
    "url": "/numeroplanilla",
    "title": "Registrar Numero de planilla",
    "name": "Numeroplanilla",
    "group": "Viajes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/numeroplanilla/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "viaje_id",
            "description": "<p>Id del viaje para agregar el numero de planilla.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "numero_planilla",
            "description": "<p>Numero de planilla para registrar.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Viajes"
  },
  {
    "type": "post",
    "url": "/viajesitemrendicion",
    "title": "Agregar item de rendicion",
    "name": "ViajesItemRendicion",
    "group": "Viajes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/viajesitemrendicion/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "viaje_id",
            "description": "<p>Id del viaje a rendir.</p>"
          },
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "categoria_id",
            "description": "<p>Id de la categoria de rendición.</p>"
          },
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "motivo_id",
            "description": "<p>Id del motivo de rendición.</p>"
          },
          {
            "group": "Parameter",
            "type": "float",
            "optional": false,
            "field": "monto",
            "description": "<p>Monto de la rendicion, en formato (999.999,99).</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "tipo_documento_id",
            "description": "<p>Id del tipo de documento.</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "nro_documento",
            "description": "<p>Nro de document, solo si el tipo de documento es factura.</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "razon_social",
            "description": "<p>Razon Social, solo si el tipo de documento es factura.</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "rut_empresa",
            "description": "<p>Rut de la empresa, solo si el tipo de documento es factura.</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "observacion",
            "description": "<p>Observación al agregar item de rendicion.</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "fecha_boleta",
            "description": "<p>Fecha de la boleta en formato DD/MM/AAAA HH:MM.</p>"
          },
          {
            "group": "Parameter",
            "type": "base64",
            "optional": false,
            "field": "imagen",
            "description": "<p>Imagen del item de rendición.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Viajes"
  },
  {
    "type": "get",
    "url": "/viajesrendidosconductor",
    "title": "Viajes rendidos por conductor",
    "name": "Viajesrendidosconductor",
    "group": "Viajes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/viajesrendidosconductor/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "conductor_id",
            "description": "<p>Id del conductor que inicio sesión.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Viajes"
  },
  {
    "type": "get",
    "url": "/viajessinrendirconductor",
    "title": "Viajes sin rendir por conductor",
    "name": "Viajessinrendirconductor",
    "group": "Viajes",
    "version": "1.0.0",
    "sampleRequest": [
      {
        "url": "http://localhost/integracion/viajessinrendirconductor/"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token obtenido en el login.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Int",
            "optional": false,
            "field": "conductor_id",
            "description": "<p>Id del conductor que inicio sesion.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Repuesta",
          "content": "{\n     estado = estatus del mensaje: ok | error;\n     mensaje = respuesta del endpoint;\n}",
          "type": "json"
        }
      ]
    },
    "filename": "controllers/IntegracionController.php",
    "groupTitle": "Viajes"
  }
] });
