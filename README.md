# Cookify
Aplicación Web de Entrega de Comida a Domicilio - Proyecto Académico.

# Requerimientos
- PHP >= 7.1
- MySQL >= 5.7

## Instalacion

Migrar base de datos:

    mysql -u root -p < db_scripts/db_cookify.sql
    
 Posteriormente deberá introducir su contraseña de MySQL.
 
**NOTA:** Generalmente **root** es el nombre de usuario por defecto en MySQL, en caso de tener un nombre de usuario diferente reemplazarlo. 

Finalmente, copiar el archivo de variables de entorno:

    cp env.json.example env.json

Y colocar dentro las variables requeridas para la configuracion de entorno y la conexion a la base de datos. Aquí un ejemplo:

```json
{
  "APP_ENV": "local",
  "DB_HOST": "localhost",
  "DB_USER": "your_username",
  "DB_PASSWORD": "your_password",
  "DB_DATABASE": "db_cookify"
}
```

## Notas finales

El presente software no es ningun framework oficial de PHP. Cada una de las funciones y/o librerías en este proyecto fueron programados desde cero unicamente con fines academicos y de práctica.

Todos los archivos finales fueron publicados en el siguiente repositorio de GitHub:

[https://github.com/mikebsg01/Cookify](https://github.com/mikebsg01/Cookify)

---
Copyright (c) 2018 Michael Serrato & Perla Aguilar
