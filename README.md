# Pet Concierge Core 🐾

Proyecto académico de Ingeniería Web desarrollado con Symfony 7.4.

## Descripción

Pet Concierge es una plataforma MVC para administrar
un servicio de paseos de mascotas.

La administración registra la información de usuarios,
perros, planes, suscripciones y paseos que alimentará
el Core de análisis estadístico del sistema.

## Tecnologías

- PHP 8.4
- Symfony 7.4
- Arquitectura MVC
- Doctrine ORM
- SQLite (entorno local verificado)
- Twig
- Symfony Forms y Validator
- Symfony Security
- SCSS / Sass
- JavaScript
- Git

## Arquitectura MVC

### Model
Las entidades se encuentran en `src/Entity/` y utilizan
Doctrine ORM para gestionar los datos y sus relaciones.

### View
Las plantillas Twig se encuentran en `templates/`.

### Controller
Los controladores en `src/Controller/` reciben las
peticiones, procesan formularios y devuelven respuestas.

### Servicios
`src/Service/AnalisisService.php` concentra los cálculos
estadísticos implementados.

## Módulos administrativos

- Dashboard: `/admin`
- Usuarios: `/admin/usuarios`
- Perros: `/admin/perros`
- Planes: `/admin/planes`
- Suscripciones: `/admin/suscripciones`
- Paseos: `/admin/paseos`
- Reportes: `/admin/reportes`
- Riesgos: `/admin/riesgos`

Nota: la existencia de rutas no implica que todas
las funcionalidades hayan sido verificadas.

## Validación Back-End

El proyecto utiliza Symfony Validator y un validador
de cédula ecuatoriana en:

`src/Validator/CedulaEcuatoriana.php`

Se comprueba la estructura de la cédula, el código de
provincia, el tercer dígito y el dígito verificador.

La validación se realiza desde PHP antes de persistir
los datos.

Prueba demostrativa:
ingresar una cédula inválida en el formulario de registro
y comprobar que Symfony rechaza la operación.

## Relaciones entre tablas

Un perro está asociado a un usuario propietario.

En la administración de perros, el propietario se
selecciona mediante un dropdown alimentado desde
la base de datos, sin escribir manualmente el ID.

Los formularios utilizan Doctrine y Symfony Forms.

## Seguridad

- Autenticación con Symfony Security
- Contraseñas almacenadas mediante hash
- Protección de rutas administrativas por ROLE_ADMIN
- Formularios protegidos mediante CSRF
- Roles: ROLE_ADMIN, ROLE_CLIENTE y ROLE_PASEADOR

## Instalación local

Requisitos:
- PHP 8.2 o superior
- Composer
- Node.js y npm
- Symfony CLI recomendado
- Extensión SQLite de PHP

Clonar el repositorio:

```bash
git clone URL_DEL_REPOSITORIO
cd pet-concierge-core
```

Instalar dependencias:

```bash
composer install
npm install
```

Configurar una copia local de `.env.local` con
`APP_ENV=dev` y una conexión `DATABASE_URL`
adecuada al entorno.

No publicar credenciales ni secretos.

Crear el directorio `var/` si es necesario,
preparar la base de datos y ejecutar:

```bash
php bin/console doctrine:migrations:migrate
```

Compilar estilos:

```bash
npm run styles:build
```

Iniciar el servidor de desarrollo:

```bash
symfony server:start
```

Abrir:

`http://127.0.0.1:8000`

## Comprobaciones

```bash
php bin/console doctrine:schema:validate
php bin/console lint:twig templates/
php bin/console debug:router
npm run styles:build
```

## Demostración para Ingeniería Web

1. Iniciar sesión como administrador.
2. Mostrar el dashboard MVC.
3. Demostrar una validación de cédula inválida.
4. Registrar un perro seleccionando su propietario
   mediante un dropdown de la base de datos.
5. Mostrar el resultado almacenado.
6. Mostrar la administración de planes.
7. Mostrar GitHub y el despliegue público.

## Despliegue

Pendiente de configurar y verificar una URL pública.
El servidor de producción debe soportar PHP,
Symfony y almacenamiento persistente de datos.

## Alcance

Esta entrega se centra en la administración MVC
que alimentará el Core. Las funcionalidades
analíticas avanzadas se desarrollarán posteriormente.