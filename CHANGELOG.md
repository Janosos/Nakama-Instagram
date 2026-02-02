# Changelog

## [1.6.1] - 2026-02-01
### Corregido
- Solucionado error crítico en la página de configuración debido a callbacks faltantes para App ID/Secret.
- Actualizada la sección de "Ayuda / Instrucciones" con guía detallada para Graph API y Auto-Refresh.

## [1.6.0] - 2026-02-01
### Añadido
- **Fallo Silencioso**: El frontend ya no muestra errores de PHP/API si algo falla, evitando exponer rutas o tokens.
- **Logging del Servidor**: Los errores críticos se escriben en el `error_log` del servidor para depuración segura.
- **Notificaciones de Admin**: Se mantiene el aviso rojo solo para administradores en el Backend.

## [1.5.0] - 2026-02-01
### Añadido
- **Notificaciones de Admin**: Alertas visuales en el panel de WordPress si el token expira o falla el auto-refresh.
- Lógica reforzada para evitar refrescos prematuros (mínimo 45 días) y manejo de errores de API.

## [1.4.0] - 2026-02-01
### Añadido
- **Auto-Refresh de Tokens**: Implementación de lógica interna para renovar automáticamente el token de acceso cuando tiene más de 45 días de antigüedad.
- Nuevos campos opcionales **App ID** y **App Secret** en la configuración (requeridos para el auto-refresh de tokens Graph API `EAA`).
- Visualización de la fecha de generación y antigüedad del token en el panel de administración.

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
