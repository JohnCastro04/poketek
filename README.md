# 🧬 PokéTek

> Proyecto de fin de ciclo superior creado con **pasión** por **John Castro**.
> Una **Pokédex interactiva fullstack** hecha con **Laravel**, alimentada por la **PokéAPI**.
>
> 🚀 **Combina bases de datos, APIs, autenticación, minijuegos y más, ofreciendo una experiencia completa para los fans de Pokémon.**

![Hecho con Laravel](https://img.shields.io/badge/Hecho%20con-Laravel-red?style=for-the-badge&logo=laravel)
![Autor: John Castro](https://img.shields.io/badge/Autor-John%20Castro-blue?style=for-the-badge&logo=github)
![Versión 1.0](https://img.shields.io/badge/Versi%C3%B3n-1.0-yellow?style=for-the-badge)

---

## 📖 Índice

- [✨ Acerca de PokéTek](#-acerca-de-poketek)
- [📦 Características Principales](#-características-principales)
  - [🔹 PokéDex (Página de Inicio)](#-pokédex-página-de-inicio)
  - [👤 Perfil de Usuario](#-perfil-de-usuario)
  - [📚 Herramientas y Utilidades](#-herramientas-y-utilidades)
- [🎮 Minijuegos](#-minijuegos)
  - [🟡 Encuentra el Shiny](#-encuentra-el-shiny)
  - [⚫ Adivina la Silueta](#-adivina-la-silueta)
- [⚙️ Tecnologías Usadas](#-tecnologías-usadas)
- [🧠 Conocimientos Aplicados](#-conocimientos-aplicados)
- [📄 Licencia](#-licencia)
- [👤 Autor](#-autor)

---

## ✨ Acerca de PokéTek

**PokéTek** no es solo una Pokédex; es una **plataforma interactiva** que explora las funcionalidades de **Laravel** combinadas con la vasta información de la **PokéAPI**. Este proyecto de fin de ciclo superior busca demostrar un dominio integral en el desarrollo web fullstack, ofreciendo una experiencia rica y entretenida para cualquier fan de Pokémon. Desde la gestión de usuarios hasta minijuegos interactivos, PokéTek lo tiene todo.

---

## 📦 Características Principales

### 🔹 PokéDex (Página de Inicio)

La columna vertebral de la aplicación. Permite una **exploración profunda y dinámica** de todos los Pokémon.

-   ✅ **Listado Dinámico**: Navega a través de Pokémon con paginación optimizada.
-   ✅ **Filtros Avanzados**: Combina filtros por:
    -   🔤 **Nombre**: Búsqueda instantánea por nombre.
    -   🔥 **Tipo**: Filtra por uno o varios tipos (ej. "Fuego" y "Volador").
    -   🥚 **Grupo Huevo**: Descubre Pokémon con grupos de cría específicos.
-   ✅ **Vista Detallada**: Cada Pokémon tiene su propia página con:
    -   📊 **Estadísticas**: Visualiza sus atributos base.
    -   ✨ **Habilidades**: Conoce sus talentos especiales.
    -   📘 **Descripción**: Lore y datos curiosos.
    -   🔄 **Cadena Evolutiva**: (¡Próximamente!) Sigue la línea de evolución completa.

### 👤 Perfil de Usuario

Un espacio personalizado donde cada usuario puede gestionar su experiencia.

-   🆔 **ID de Usuario**: Identificador único (solo lectura).
-   ✏️ **Edición de Datos**: Actualiza fácilmente:
    -   **Nombre de Usuario**
    -   **Correo Electrónico**
    -   **Contraseña**
-   🖼️ **Selección de Avatar**: Personaliza tu perfil con una galería de avatares prediseñados.
-   📈 **Estadísticas de Actividad**: Monitoriza tu progreso en los minijuegos y otras interacciones.

### 📚 Herramientas y Utilidades

Funcionalidades adicionales diseñadas para enriquecer la interacción.

-   🎲 **Generador de Pokémon Aleatorio**: Descubre un Pokémon al azar con solo un clic.
-   🧩 **Editor de Equipos Personalizados**: (Solo para usuarios registrados)
    -   ➕ **Añadir/Eliminar Pokémon**: Crea y gestiona tus equipos estratégicos.
    -   👁️ **Vista Completa del Equipo**: Revisa las estadísticas y tipos de tu equipo.
    -   🏷️ **Motear Pokémon**: Asigna un apodo a tus compañeros.
-   🌱 **Visualización de Naturalezas**: Aprende cómo las naturalezas afectan las estadísticas de tu Pokémon.

---

## 🎮 Minijuegos

Dos minijuegos interactivos para poner a prueba tu conocimiento Pokémon. ¡Las **estadísticas se guardan automáticamente** si tienes sesión iniciada!

### 🟡 Encuentra el Shiny

> 🔎 **Desafío visual**: Entre un grupo de Pokémon, ¿puedes identificar al escurridizo **Shiny** que brilla diferente? ¡La agudeza visual es clave!

### ⚫ Adivina la Silueta

> ❓ **Clásico del anime**: ¿Eres un verdadero maestro Pokémon? Demuéstralo adivinando el Pokémon oculto tras su silueta negra.

---

## ⚙️ Tecnologías Usadas

Este proyecto se construye sobre una base sólida de tecnologías modernas y eficientes:

| Herramienta        | Descripción                                                        |
| :----------------- | :----------------------------------------------------------------- |
| 🧱 **Laravel** | Framework PHP principal para el backend, enrutamiento y lógica de negocio. |
| 🔐 **Laravel Breeze** | Sistema de autenticación ligero y preconfigurado.               |
| 🌐 **PokéAPI** | Fuente externa de datos para toda la información de Pokémon.     |
| 🛢️ **MySQL** | Base de datos relacional para usuarios, estadísticas y datos específicos del proyecto. |
| 💄 **Bootstrap** | Framework CSS para un diseño responsive y componentes de UI predefinidos. |
| 🧠 **Blade** | Motor de plantillas de Laravel para la renderización de vistas dinámicas. |
| 🔁 **Axios** | Cliente HTTP basado en promesas para peticiones asíncronas desde el frontend. |
| 🎨 **CSS Personalizado** | Estilos adicionales para una identidad visual única y pulida. |

## 🧠 Conocimientos Aplicados

Este proyecto es una culminación de los conocimientos adquiridos en diversas áreas del desarrollo web:

-   **Backend Development**: Dominio de **Laravel** para la lógica de negocio, manejo de rutas, controladores, modelos y migraciones.
-   **Frontend Development**: Construcción de interfaces interactivas con **Blade**, **Bootstrap**, **CSS** y manejo de peticiones **AJAX** con **Axios**.
-   **Database Management**: Diseño y gestión de esquemas de bases de datos con **MySQL**, incluyendo relaciones y consultas eficientes.
-   **API Integration**: Consumo y procesamiento de datos de la **PokéAPI** para enriquecer la aplicación.
-   **Authentication & Authorization**: Implementación de un sistema robusto de registro, inicio de sesión y gestión de usuarios con **Laravel Breeze**.
-   **Game Development Concepts**: Desarrollo de lógica para minijuegos y gestión de puntuaciones.
-   **Version Control**: Uso de **Git** y **GitHub** para la gestión colaborativa del código.

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Para más detalles, consulta el archivo `LICENSE.md` en el repositorio.

---

## 👤 Autor

**John Castro** - [GitHub](https://github.com/JohnCastro04)

---