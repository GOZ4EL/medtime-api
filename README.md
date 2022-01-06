
# MEDTIME API

start server:
`$ php -S 127.0.0.1:8000 -t public`

DB at:
`$ /db/database.sql`
and
`$ /db/cities_and_states.sql`

### A REST-API that serves as backend of an app for making medical appointments

## Endpoints

- GET /address
	> Devuelve como respuesta todas las ciudades y estados, relacionados y 
	> con sus respectivos id cada uno.
- GET /address/[city_id]
	> Devuelve como respuesta la ciudad cuyo id es pasado como parametro 
	> por la url, relacionada con su respectivo estado.
- POST /user/login
	> Espera en el cuerpo de la peticion los datos "email" y "password" del usuario
	> y devuelve como respuesta el resto de datos del mismo, incluido
	> el token de sesion.
- GET /doctor
	> Devuelve como respuesta todos los doctores.
- GET /doctor/[ci]
	> Devuelve como respuesta los datos del doctor
	> cuya ci es pasada como parametro por la url.
- POST /doctor
	> Espera en el cuerpo de la peticion los datos "email", "firstname",
	> "lastname", "ci" (campo numerico que debe tener 6 o mas caracteres),
	> "password" (campo alfanumerico que debe tener 8 o mas caracteres),
	> "role" (cuyo valor debe ser "doctor"), "starts_at" (campo con formato
	> tipo DATE de SQL), "ends_at" (campo con formato tipo DATE de SQL) y
	> "cost" (un numero real); y devuelve como respuesta un mensaje bajo
	> el nombre de: "message" si la peticion fue realizada satisfactoriamente o
	> "error" si hubo alguna especie de error.
- PUT /doctor/[ci]
	> Espera los mismos datos que *POST /doctor* excepto "email",
	> "password", "ci" y "role"; adicionalmente debe ser pasada 
	> como parametro por la url la ci del doctor cuyos datos desean
	> ser actualizados. Devuelve lo mismo.
- DELETE /doctor/[ci]
	> Borra el registro del doctor cuya ci es sido pasada
	> como parametro por la url. Devuelve un mensaje de error
	> bajo el nombre de "error" si hay algun fallo, sino devuelve
	> un mensaje de exito bajo el nombre de "message".
- GET /patient
	> Devuelve como respuesta todos los pacientes.
- GET /patient/[ci]
	> Devuelve como respuesta los datos del paciente
	> cuya ci es pasada como parametro por la url.
- POST /patient
	> Espera en el cuerpo de la peticion los datos "email", "firstname", 
	> "lastname", "ci" (campo numerico que debe tener 6 o mas caracteres),
	> "password" (campo alfanumerico que debe tener 8 o mas caracteres),
	> "role" (cuyo valor debe ser "patient") y "city_id". Devuelve un mensaje
	> de error bajo el nombre de "error" si hay algun fallo, sino
	> devuelve un mensaje de exito bajo el nombre de "message".
- PUT /patient/[ci]
	> Espera los mismos datos que *POST /patient* excepto "email",
	> "password", "ci" y "role"; adicionalmente debe ser pasada
	> como parametro por la url la ci del paciente cuyos datos 
	> desean ser actualizados. Devuelve lo mismo.
- DELETE /patient/[ci]
	> Borra el registro del paciente cuya ci es pasada como
	> parametro por la url. Devuelve un mensaje de error bajo 
	> el nombre de "error" si hay algun fallo, sino devuelve
	> un mensaje de exito bajo el nombre de "message".
- GET /speciality 
	> Devuelve como respuesta todas las especialidades.
- POST /speciality
	> Espera en el cuerpo de la peticion el dato "name" y devuelve 
	> un mensaje de error bajo el nombre de "error" si hay algun fallo,
	> sino devuelve un mensaje de exito bajo el nombre de "message".
- DELETE /speciality/[id]
	> Borra el registro de la especialidad cuyo id es pasado
	> como parametro por la url. Devuelve un mensaje de error
	> bajo el nombre de "error" si hay algun fallo, sino 
	> devuelve un mensaje de exito bajo el nombre de "message".
- GET /specialization
	> Devuelve como respuesta todas las especializaciones.
	> *NOTA:* Una especializacion es cada una de las relaciones
	> que existe entre una especializacion y un doctor. Es decir, 
	> para obtener todas las especialidades de un doctor deben
	> buscarse todas las especializaciones que tengan como dato
	> la ci de ese doctor en especifico. Abajo se muestra un endpoint
	> que da la posibilidad de hacer esto.
- GET /specialization/doctor/[doctor_ci]
	> Devuelve como respuesta todas las especializaciones que tengan
	> como "doctor_ci" la ci que es pasada como parametro por la url.
- GET /specialization/speciality/[speciality_name]
	> Devuelve como respuesta todas las especializaciones que tengan
	> como "speciality_name" el nombre de especialidad que es pasado
	> como parametro por la url.
- POST /specialization
	> Espera en el cuerpo de la peticion los datos "doctor_ci" y
	> "speciality_name". Devuelve un mensaje de error bajo el nombre
	> de "error" si hay algun fallo, sino devuelve un mensaje
	> de exito bajo el nombre de "message".
- DELETE /specialization/[id]
	> Borra el registro de la especializacion cuyo id es pasado
	> como parametro por la url. Devuelve un mensaje de error
	> bajo el nombre de "error" si hay algun fallo, sino
	> devuelve un mensaje de exito bajo el nombre de "message".
- GET /appointment 
	> Devuelve como respuesta todas las citas.
- GET /appointment/doctor/[doctor_ci]
	> Devuelve como respuesta todas las citas que se hayan hecho al
	> doctor cuya ci es pasada como parametro por la url; es decir, 
	> todos los registros de la tabla appointment cuyo campo 
	> "doctor_ci" coincida con lo correspondiente a la 
	> ultima seccion de la url.
- GET /appointment/patient/[patient_ci]
	> Devuelve como respuesta todas las citas que hayan sido hechas
	> por el paciente cuya ci es pasada como parametro por la url; 
	> es decir, todos los registros de la tabla appointment
	> cuyo campo "patient_ci" coincida con lo correspondiente a
	> la ultima seccion de la url.
- POST /appointment
	> Espera en el cuerpo de la peticion los datos "doctor_ci",
	> "patient_ci", "day" (el dia en formato 'yyyy-mm-dd'),
	> "hour" (una hora especifica en formato 'hh:mm:ss') y
	> "status" (campo alfabetico que solo acepta los valores
	> 'active', 'done' y 'cancelled'). Devuelve un mensaje de
	> error bajo el nombre de "error" si hay algun fallo, sino
	> devuelve un mensaje de exito bajo el nombre de "message".
- PUT /appointment/[id]
	> Espera en el cuerpo de la peticion los datos "doctor_ci",
	> "patient_ci", "day" (el dia en formato 'yyyy-mm-dd'),
	> "hour" (una hora especifica en formato 'hh:mm:ss') y
	> "status" (campo alfabetico que solo acepta los valores
	> 'active', 'done' y 'cancelled'), ademas, debe ser pasado 
	> como parametro por la url el id de la cita que se desea actualizar. 
	> Devuelve un mensaje de error bajo el nombre de "error" si hay algun fallo,
	> sino devuelve un mensaje de exito bajo el nombre de "message".
	> *NOTA IMPORTANTE:* Uno podria verse tentado al querer actualizar
	> el campo "status" pasar en el cuerpo de la peticion solamente
	> este dato. SIN EMBARGO, para que la peticion sea recibiida
	> y procesada satisfactoriamente, tanto en este caso como en 
	> todos los casos en los que se hace una peticion PUT, es 
	> estrictamente necesario pasar en el cuerpo de la peticion 
	> ABSOLUTAMETE TODOS los datos que se indican.
- DELETE /appointment/[id]
	> Borra el registro de la cita (tabla Appointment en la db) cuyo
	> id es pasado como parametro por la url. *NOTA:* Por lo general
	> no es necesario ni deseado borrar un registro de la tabla 
	> Appointment, ya que posee el campo status, el cual indica cuando
	> una cita ha sido suspendida, cancelada o todavia esta a la espera.
	> Sin embargo, se habilito esta opcion para los casos en los que sea
	> necesario. Tomar con precaucion.
