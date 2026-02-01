# Nakama Instagram Feed Plugin

Plugin de WordPress desarrollado a medida para **Nakama Bordados**. Conecta tu cuenta de Instagram y muestra un feed elegante, optimizado y totalmente integrado con el diseño de tu sitio web.

## Características

*   **Integración con API de Instagram**: Muestra tus últimas publicaciones automáticamente.
*   **Optimización de Rendimiento**: Uso de Transients de WordPress para cachear la respuesta de la API (15 minutos) y mejorar la velocidad de carga.
*   **Diseño Personalizado**: Estilos CSS basados en Tailwind y adaptados a la identidad visual de Nakama (soporte para modo oscuro).
*   **Personalización**:
    *   Campos de configuración para el Token de Acceso.
    *   Texto del encabezado modificable.
    *   Subtítulo (descripción corta) opcional.
    *   Alineación del encabezado (Izquierda, Centro, Derecha).
*   **Shortcode**: Integración sencilla en cualquier parte de la web mediante `[nakama_instagram_feed]`.

## Instalación

1.  Descarga o clona este repositorio en tu carpeta de plugins de WordPress:
    ```bash
    wp-content/plugins/nakama-instagram-feed
    ```
2.  Accede al panel de administración de WordPress.
3.  Ve a **Plugins** y activa "Nakama Instagram Feed".
4.  Aparecerá un nuevo menú llamado **Nakama Instagram** en la barra lateral.

## Configuración

1.  **Instagram Access Token**: Necesitas generar un "User Token" desde la API de Instagram Basic Display. Pégalo en el campo correspondiente.
2.  **Texto del Encabezado**: Define el título que aparecerá sobre el feed (ej. "SÍGUENOS EN INSTAGRAM").
3.  **Subtítulo**: (Opcional) Texto más pequeño debajo del título (ej. "@usuario").
4.  **Alineación**: Selecciona la alineación del texto (Izquierda, Centro, Derecha).
5.  Guarda los cambios.

## Uso

Para mostrar el feed, simplemente añade el siguiente shortcode en cualquier página, entrada o widget de WordPress:

```shortcode
[nakama_instagram_feed]
```

## Estructura del Proyecto

*   `nakama-instagram-feed.php`: Archivo principal con toda la lógica del plugin.
*   `test-preview.html`: Archivo HTML para previsualizar el diseño sin necesidad de instalar el plugin en WordPress.

---
Desarrollado por **ImperioDev**
