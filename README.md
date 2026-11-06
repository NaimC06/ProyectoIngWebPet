
# 🐾 Pet Concierge Core

### Plataforma de administración MVC para paseos de mascotas

**Proyecto académico de Ingeniería Web | Symfony 7.4 | PHP | Doctrine ORM**

---

## 🌐 APLICACIÓN WEB PÚBLICA

### 🚀 [¡ABRIR PET CONCIERGE CORE!](https://proyectoingwebpet-production.up.railway.app)

**Enlace directo:** https://proyectoingwebpet-production.up.railway.app

**Repositorio GitHub:** https://github.com/NaimC06/ProyectoIngWebPet

| Información | Detalle |
|---|---|
| Proyecto | Pet Concierge Core |
| Framework | Symfony 7.4 |
| Arquitectura | MVC |
| Base de datos | SQLite |
| Hosting | Railway |
| Control de versiones | Git y GitHub |
| Estado | Aplicación desplegada públicamente |

> La aplicación está alojada en Railway y puede consultarse desde internet sin ejecutar un servidor local.

---

## 📖 1. Descripción del proyecto

**Pet Concierge Core** es una aplicación web desarrollada para administrar un servicio de paseos de mascotas.

El sistema permite gestionar la información de clientes, paseadores, perros, planes prepagados, suscripciones y solicitudes de paseos.

Esta administración constituye la fuente de información del **Core estadístico**, encargado de analizar los registros y generar indicadores sobre los usuarios y la utilización de los servicios.

### Objetivo general

Desarrollar una administración web basada en el patrón Modelo-Vista-Controlador (MVC), utilizando Symfony, que permita almacenar, validar, relacionar y consultar los datos necesarios para alimentar el Core de Pet Concierge.

### Objetivos específicos

- Administrar usuarios, perros y planes.
- Aplicar validaciones sensibles desde el Back-End.
- Gestionar relaciones entre tablas mediante formularios con dropdowns.
- Almacenar información utilizando Doctrine ORM y SQLite.
- Proteger el acceso administrativo mediante autenticación y roles.
- Versionar el proyecto utilizando Git y GitHub.
- Publicar la aplicación en un entorno web accesible mediante Railway.

---

## 🛠️ 2. Tecnologías utilizadas

| Tecnología | Utilización |
|---|---|
| PHP 8.2+ | Lenguaje de programación del Back-End |
| Symfony 7.4 | Framework principal |
| MVC | Arquitectura del sistema |
| Doctrine ORM | Persistencia y relaciones entre entidades |
| SQLite | Base de datos |
| Twig | Plantillas y vistas |
| Symfony Forms | Construcción de formularios |
| Symfony Validator | Validación de datos |
| Symfony Security | Autenticación y autorización |
| HTML5 | Estructura de las páginas |
| SCSS / Sass | Estilos y diseño responsive |
| JavaScript | Interacciones de la interfaz |
| Composer | Gestión de dependencias PHP |
| Node.js y npm | Herramientas de desarrollo frontend |
| Git y GitHub | Versionamiento del código |
| Railway | Despliegue público de la aplicación |

---

## 🏗️ 3. Arquitectura MVC

El proyecto está desarrollado mediante el patrón **Modelo-Vista-Controlador**, que permite separar las responsabilidades de cada componente.

### 3.1. Modelo (Model)

Representa los datos y las relaciones del sistema.

Las entidades están ubicadas en:

`src/Entity/`

Ejemplos:

- `Usuario.php`
- `Perro.php`
- `Plan.php`
- `Suscripcion.php`
- `Paseo.php`

Doctrine ORM relaciona estas entidades con las tablas de SQLite.

### 3.2. Vista (View)

Presenta la información y permite la interacción con el usuario.

Las vistas se encuentran en:

`templates/`

Se utiliza Twig para construir formularios, tablas, páginas de registro y paneles administrativos.

### 3.3. Controlador (Controller)

Recibe las solicitudes HTTP, procesa formularios, coordina las validaciones y gestiona las operaciones sobre las entidades.

Los controladores están ubicados en:

`src/Controller/`

Por ejemplo:

`AdminPerroController.php`

Este controlador administra el listado, creación, edición, consulta y eliminación de perros.

### 3.4. Ejemplo del flujo MVC

Cuando un administrador registra una mascota:

1. La vista Twig muestra el formulario.
2. El administrador introduce los datos.
3. El controlador recibe la solicitud.
4. Symfony valida la información.
5. Doctrine ORM almacena el perro y su relación con el propietario.
6. El sistema muestra el registro actualizado.

```text
ADMINISTRADOR
     |
     v
VISTA (Twig)
Formulario de perros
     |
     v
CONTROLADOR (Symfony)
Procesa y valida los datos
     |
     v
MODELO (Doctrine ORM)
Entidad Perro y relación Usuario
     |
     v
BASE DE DATOS (SQLite)
Guarda la información
     |
     v
CORE ESTADÍSTICO
Consulta los registros almacenados
```

---

## 📋 4. Administración del sistema

Pet Concierge cuenta con una interfaz administrativa independiente de la página pública.

El acceso está protegido mediante el rol `ROLE_ADMIN`.

### Módulos administrativos

| Módulo | Ruta | Función |
|---|---|---|
| Dashboard | `/admin` | Visualización de indicadores |
| Usuarios | `/admin/usuarios` | Administración de usuarios |
| Perros | `/admin/perros` | Administración de mascotas y propietarios |
| Planes | `/admin/planes` | Gestión de planes prepagados |
| Suscripciones | `/admin/suscripciones` | Gestión de suscripciones |
| Paseos | `/admin/paseos` | Gestión de los servicios de paseo |
| Reportes | `/admin/reportes` | Consulta de información estadística |
| Riesgos | `/admin/riesgos` | Indicadores de riesgos operativos |

La implementación principal demostrada en esta actividad se centra en usuarios, perros, planes, validaciones y relaciones entre tablas.

---

## ✅ 5. Cumplimiento de los requisitos de Ingeniería Web

La actividad solicita tres requisitos obligatorios.

### 🚨 Requisito 1: Validación Back-End de un dato sensible

Se implementó una validación de **cédula ecuatoriana** mediante PHP y Symfony Validator.

Archivos relacionados:

- `src/Validator/CedulaEcuatoriana.php`
- `src/Form/RegistroType.php`
- `src/Form/UsuarioType.php`
- `src/Controller/RegistroController.php`

La validación comprueba:

1. Que la cédula contenga exactamente diez dígitos.
2. Que el código de provincia sea válido.
3. Que el tercer dígito corresponda al tipo permitido.
4. Que el dígito verificador sea correcto.

Además, el registro comprueba si el correo electrónico o la cédula ya están registrados.

**La validación se realiza desde el servidor, antes de guardar la información en SQLite.**

Esto impide depender únicamente de JavaScript para validar datos sensibles.

#### Demostración

1. Abrir el formulario de registro.
2. Completar los campos obligatorios.
3. Introducir una cédula inválida, por ejemplo `0000000000`.
4. Enviar el formulario.
5. Comprobar que Symfony muestra el error y rechaza el registro.

El sistema verifica la estructura matemática de la cédula; no realiza una consulta de identidad al Registro Civil.

### 🔗 Requisito 2: Relaciones entre tablas mediante dropdowns

Se implementó una relación entre las entidades `Usuario` y `Perro`.

Cada perro pertenece a un usuario registrado como cliente.

En el formulario de creación de perros, el propietario no se introduce mediante un ID escrito manualmente.

En su lugar se utiliza **EntityType de Symfony**, que permite seleccionar un cliente existente mediante un dropdown.

Archivos relacionados:

- `src/Entity/Usuario.php`
- `src/Entity/Perro.php`
- `src/Form/PerroType.php`
- `src/Controller/AdminPerroController.php`

#### Ejemplo de implementación

```php
->add('propietario', EntityType::class, [
    'label' => 'Propietario',
    'class' => Usuario::class,
    'placeholder' => 'Selecciona un propietario',
])
```

El formulario real también incorpora una consulta para mostrar únicamente usuarios con rol `ROLE_CLIENTE`.

#### Demostración

1. Iniciar sesión como administrador.
2. Entrar en `Perros`.
3. Seleccionar `Nuevo perro`.
4. Completar el nombre, raza, edad y tamaño.
5. Seleccionar un propietario desde el dropdown.
6. Guardar el perro.
7. Comprobar que aparece asociado al cliente seleccionado.

Esta implementación evita que el administrador escriba manualmente las claves foráneas.

### 🌍 Requisito 3: Git, README y despliegue

El proyecto utiliza Git para gestionar las versiones del código fuente.

Se encuentra publicado en GitHub:

**[Repositorio ProyectoIngWebPet](https://github.com/NaimC06/ProyectoIngWebPet)**

El repositorio contiene:

- Código fuente de Symfony.
- Entidades y controladores.
- Formularios y validadores.
- Plantillas Twig.
- Estilos SCSS.
- Migraciones de Doctrine.
- Configuración del proyecto.
- Documentación en `README.md`.

#### Despliegue público

Se utilizó **Railway** para alojar la aplicación.

**[Abrir aplicación en Railway](https://proyectoingwebpet-production.up.railway.app)**

El entorno utiliza Symfony, PHP y SQLite con almacenamiento persistente mediante un volumen.

De esta manera, la aplicación puede ejecutarse desde internet sin depender del servidor local del desarrollador.

---

## 🔐 6. Seguridad y autenticación

El proyecto incorpora Symfony Security para controlar el acceso a las diferentes áreas.

### Roles

| Rol | Descripción |
|---|---|
| `ROLE_ADMIN` | Administración del sistema |
| `ROLE_CLIENTE` | Usuarios propietarios de mascotas |
| `ROLE_PASEADOR` | Usuarios que ofrecen servicios de paseo |

Medidas implementadas:

- Contraseñas almacenadas mediante hashes seguros.
- Autenticación con Symfony Security.
- Protección de rutas administrativas.
- Tokens CSRF en los formularios.
- Validación de datos desde PHP.
- Restricción de roles durante el registro público.

Las credenciales privadas no están incluidas en este repositorio.

---

## 📂 7. Estructura del proyecto

```text
pet-concierge-core/
|
|-- assets/
|   |-- styles/          Estilos SCSS y CSS
|   |-- app.js           JavaScript principal
|
|-- bin/
|   |-- console          Consola Symfony
|
|-- config/              Configuración del sistema
|
|-- public/
|   |-- resources/
|   |   |-- images/      Recursos gráficos
|   |-- index.php        Punto de entrada web
|
|-- src/
|   |-- Controller/      Controladores MVC
|   |-- Entity/          Entidades Doctrine
|   |-- Form/            Formularios Symfony
|   |-- Repository/      Acceso a datos
|   |-- Service/         Lógica del Core
|   |-- Validator/       Validaciones Back-End
|   |-- Command/         Comandos de consola
|   |-- Migrations/      Migraciones
|
|-- templates/           Plantillas Twig
|-- tests/               Pruebas
|-- translations/        Traducciones
|
|-- composer.json
|-- composer.lock
|-- package.json
|-- package-lock.json
|-- .gitignore
|-- README.md
```

---

## 💻 8. Instalación y ejecución local

### Requisitos previos

- PHP 8.2 o superior.
- Composer.
- Node.js y npm.
- Extensión SQLite para PHP.
- Symfony CLI (recomendado).

### Paso 1. Clonar el repositorio

```bash
git clone https://github.com/NaimC06/ProyectoIngWebPet.git
cd ProyectoIngWebPet
```

### Paso 2. Instalar dependencias

```bash
composer install
npm install
```

### Paso 3. Configurar el entorno local

Crear el archivo `.env.local` en la raíz del proyecto.

Ejemplo:

```dotenv
APP_ENV=dev
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

Las contraseñas y los secretos deben configurarse mediante variables locales y no deben publicarse en GitHub.

### Paso 4. Ejecutar migraciones

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

### Paso 5. Compilar los estilos

```bash
npm run styles:build
```

### Paso 6. Iniciar el servidor Symfony

```bash
symfony server:start
```

Abrir en el navegador:

http://127.0.0.1:8000

Esta dirección funciona únicamente en el equipo local.

Para acceder desde internet, utilizar la URL pública de Railway indicada al inicio del documento.

---

## 🧪 9. Verificación técnica

Comandos utilizados para comprobar la estructura de la aplicación.

### Validación de Doctrine

```bash
php bin/console doctrine:schema:validate
```

### Validación de Twig

```bash
php bin/console lint:twig templates/
```

### Consulta de rutas

```bash
php bin/console debug:router
```

### Compilación de Sass

```bash
npm run styles:build
```

### Migraciones

```bash
php bin/console doctrine:migrations:status
```

---

## 📊 10. Relación con el Core estadístico

El módulo administrativo es responsable de registrar y mantener la información necesaria para el análisis del servicio.

Los datos pueden utilizarse para calcular:

- Cantidad de usuarios.
- Cantidad de clientes y paseadores.
- Número de perros registrados.
- Planes disponibles.
- Suscripciones registradas.
- Paseos programados y completados.
- Indicadores de utilización de servicios.

El proyecto incluye `AnalisisService.php`, encargado de obtener y procesar información para los indicadores implementados.

El alcance de los análisis depende de los datos efectivamente registrados. Los valores de demostración no representan necesariamente pagos reales ni actividad comercial.

---

## 🎬 11. Demostración de la actividad

Para la presentación de Ingeniería Web se propone el siguiente recorrido:

| Tiempo aproximado | Actividad |
|---|---|
| 0:00–0:30 | Presentar Pet Concierge y el objetivo del Core |
| 0:30–1:15 | Mostrar la administración MVC y el dashboard |
| 1:15–2:15 | Demostrar la validación Back-End de la cédula |
| 2:15–3:15 | Crear un perro utilizando el dropdown de propietarios |
| 3:15–4:00 | Mostrar GitHub, README y Railway |

Durante la demostración se explican los tres requisitos obligatorios:

**1. Validación sensible en Back-End.**

**2. Relaciones entre tablas mediante dropdowns.**

**3. Versionamiento Git y aplicación desplegada.**

---

## 📌 12. Alcance del proyecto

Esta entrega corresponde a la administración MVC que alimenta el Core aprobado de Pet Concierge.

El proyecto se concentra en la gestión y validación de información. Funcionalidades como pagos electrónicos reales, geolocalización en vivo y asignación automatizada de paseadores quedan fuera del alcance de esta entrega.

---

### 🐾 Pet Concierge Core

**Proyecto de Ingeniería Web — Administración MVC con Symfony 7.4**

🌐 **Web pública:** https://proyectoingwebpet-production.up.railway.app

💻 **Repositorio:** https://github.com/NaimC06/ProyectoIngWebPet
