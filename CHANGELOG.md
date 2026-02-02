# Changelog

## [1.3.0] - 2026-02-01
### Añadido
- Soporte para **Instagram Graph API** (Cuentas Business/Creator) con tokens `EAA...`.
- Detección automática del tipo de token (Básico vs Graph).
- Fallback automático para buscar cuentas de Instagram Business vinculadas a Páginas de Facebook si el acceso directo falla.
- Actualización de documentación y walkthrough para cumplir con los nuevos requisitos de Meta (Cuenta Profesional recomendada).

## [1.2.0] - 2026-02-01
### Añadido
- Nueva sección de "Ayuda / Instrucciones" dentro de la configuración del plugin con un walkthrough paso a paso para la activación.

## [1.1.0] - 2026-02-01
### Añadido
- Lanzamiento inicial del plugin.
- Conexión con Instagram Basic Display API.
- Sistema de caché mediante Transients de WordPress (15 minutos).
- Página de ajustes en el admin para:
    - Token de acceso.
    - Texto del encabezado.
    - Toggle para mostrar créditos ("Desarrollado por ImperioDev").
- Shortcode `[nakama_instagram_feed]` para renderizar el feed.
- Diseño responsivo (Grid 2 columnas móvil / 4 escritorio) integrado con Tailwind CSS.
- Soporte para Modo Oscuro.
- Archivo `test-preview.html` para pruebas de diseño locales.
