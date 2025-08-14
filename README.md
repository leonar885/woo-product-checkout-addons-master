# Woo Product & Checkout Addons Master

Plugin open-source para añadir campos y addons avanzados a productos y checkout en WooCommerce.

Créditos: Leonardo Mendoza │ Wooqui Agencia Creativa — https://www.wooqui.com

Licencia: MIT

Textdomain: `wpcam`

## Instalación
1. Subir la carpeta `woo-product-checkout-addons-master` a `wp-content/plugins`.
2. Activar el plugin desde el panel de administración.

## Quick start
- Ir a Woo Product & Checkout Addons (Menú WPCAM) para crear formularios (CPT).
- Asignar formularios a productos o categorías.
- Los formularios aparecerán en la página del producto antes del botón "Añadir al carrito".

## Desarrollo
 Código organizado en:
 - `includes/` clases principales
 - `assets/` scripts y estilos
 - `templates/` templates front

## License
MIT

## Tests (desarrollo)

Este repositorio incluye una suite mínima de pruebas para la lógica de precios.

Local (requiere PHP y Composer):

1. Instalar dependencias:

```powershell
cd 'D:\Clientes\Wooqui\Plugin\woo-product-checkout-addons-master'
composer install
```

2. Ejecutar tests:

```powershell
composer test
# o
.\vendor\bin\phpunit -c phpunit.xml
```

CI: hay un workflow de GitHub Actions en `.github/workflows/phpunit.yml` que ejecuta `composer install` y `vendor/bin/phpunit` en PHP 8.1 en cada push/pull request.

Notas:
- Las pruebas actuales cubren la clase `Pricing` (funciones puras). Para pruebas que dependen de WordPress/WooCommerce necesitaríamos añadir mocks o usar un entorno de pruebas específico de WP.
