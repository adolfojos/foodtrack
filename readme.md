# FoodTrack - Sistema de seguimiento de los alimentos

![PHP](https://img.shields.io/badge/PHP-8-blue)
![License](https://img.shields.io/badge/license-MIT-green)

**FoodTrack** es una aplicación web desarrollada en **PHP 8** para realizar el seguimiento de los alimentos.  
Es una plataforma digital pensada para dar **trazabilidad, control y transparencia** a todo el proceso de distribución de víveres en el ámbito escolar.

En términos sencillos, significa que cada bolsa de alimentos que llega al **Centro de Distribución Local (CDL)** puede ser registrada, entregada a las instituciones educativas y finalmente reportada en su consumo diario, todo dentro de un mismo sistema.

---

##  Objetivo

El objetivo de **FoodTrack** es **garantizar la trazabilidad, transparencia y eficiencia** en la gestión de los alimentos destinados a las instituciones educativas, desde su recepción en el CDL hasta su consumo en los comedores escolares.

---

##  Funcionalidades principales

1. **Gestión de recepciones**
   - Registro de cada recepción mensual desde MERCAL.  
   - Validación con doble firma digital (inspectora y vocero parroquial).  
   - Generación automática de actas de recepción en PDF.  

2. **Control de entregas a instituciones**
   - Registro de entregas por institución educativa.  
   - Notas de entrega con cantidades por categoría (víveres, proteínas, frutas, verduras).  
   - Firma digital o código de validación del receptor (director o vocero).  
   - Historial de entregas por institución.  

3. **Reportes diarios de consumo**
   - Registro del menú preparado en cada escuela.  
   - Cantidades utilizadas por categoría de alimento.  
   - Número de estudiantes atendidos.  
   - Consolidación automática de reportes para el CDL.  

4. **Panel administrativo y dashboards**
   - Visualización de métricas clave:  
     - Total de víveres recibidos, entregados y consumidos.  
     - Comparativa entre instituciones.  
     - Gráficas de consumo por categoría de alimento.  
   - Alertas de inconsistencias (ej. consumo mayor a lo entregado).  

5. **Gestión de users y roles**
   - Roles diferenciados: Inspectora, Vocero parroquial, Director, Vocera escolar, Administrador CDL.  
   - Control de accesos según responsabilidades.  
   - Registro de auditoría (quién hizo qué y cuándo).  

6. **Documentación y trazabilidad**
   - Generación de actas, notas de entrega y reportes en formatos descargables.  
   - Historial completo de cada lote de alimentos.  
   - Evidencia digital para auditorías y rendición de cuentas.  

---

## Tecnologías utilizadas

- PHP 8  
- MySQL  
- Composer (autocarga y dependencias)  
- JavaScript (jQuery, plugins)  
- Materialize CSS  
- Git para control de versiones  

---

## Estructura del proyecto

FOODTRACK/
├── config/
│   ├── config.php
│   └── database.php
├── public/
│   ├── css/
│   ├── fonts/
│   ├── img/
│   ├── js/
│   ├── vendors/
│   ├── .htaccess
│   └── index.php
├── src/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   └── UsersController.php
│   ├── Core/
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   └── Model.php
│   ├── Models/
│   │   └── User.php
│   └── Views/
│       ├── auth/
│       │   ├── login.php
│       └── dashboard/
│           ├── index.php
│       ├── layout/
│       │   ├── footer.php
│       │   ├── header.php
│       │   ├── left-side-menu.php
│       │   └── right-side-menu.php
│       └── users/
│           ├── form.php
│           └── list.php
├── vendor/
│   ├── composer/
│   │   ├── autoload_classmap.php
│   │   ├── autoload_namespaces.php
│   │   ├── autoload_psr4.php
│   │   ├── autoload_real.php
│   │   ├── autoload_static.php
│   │   ├── ClassLoader.php
│   │   ├── installed.json
│   │   └── installed.php
│   └── autoload.php
├── .gitignore
├── composer.json
├── composer.lock
├── crear_admin.php
├── readme.md
└── schema.sql

##  Instalación

1. Clona el repositorio:
   
   git clone https://github.com/adolfojos/foodtrack.git
   
2. Configura tu entorno local (XAMPP, Laragon, etc.).

3. Crea una base de datos y ajusta las credenciales en config/database.php.

4. Asegúrate de que el servidor apunte a la carpeta /public.

5. Accede desde: http://localhost/foodtrack/public

## Roles del sistema

1. Administrador: Supervisa métricas, asigna roles y gestiona el sistema.

2. Inspector: Supervisa y valida que el proceso de distribución de alimentos cumpla con las normas y la transparencia.

3. Parish Spokesperson: Representa a la comunidad parroquial, recibe y certifica la entrega de víveres en el CDL.

3. School Spokesperson: Registra y reporta el consumo diario en la institución, asegurando que los alimentos lleguen a los estudiantes.

## Seguridad y buenas prácticas

1. Separación clara entre lógica, vistas y acceso público.

2. Uso de funciones reutilizables para blindar rutas y redirecciones.

3. Validación de sesiones y roles en cada controlador.

4. Integridad referencial en la base de datos.

## Créditos 

Desarrollado por Adolfo Suárez, técnico. Con enfoque en funcionalidad, empatía institucional y escalabilidad técnica.