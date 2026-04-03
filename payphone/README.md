# Payphone Payment Gateway for Botble CMS

Plugin de integración con la pasarela de pagos **Payphone** (Ecuador) para Botble CMS.

## Características

- Cajita de pagos Payphone (iframe)
- Soporte para tarjetas de crédito/débito
- Soporte para saldo Payphone
- Confirmación automática de pagos vía callback
- Reembolsos en línea
- Conversión automática a USD
- Configuración desde el panel de administración

## Requisitos

- PHP 8.2 o superior
- Botble CMS 7.0.0 o superior
- Token y Store ID de Payphone

## Instalación

1. Copia la carpeta `payphone` en `platform/plugins/` de tu instalación de Botble.
2. Activa el plugin desde el panel de administración o ejecuta:
   ```bash
   php artisan cms:plugin:activate payphone
   ```
3. Ve a **Configuración > Pagos** y configura:
   - **Token**: Tu token de API de Payphone
   - **Store ID**: Tu ID de tienda de Payphone
   - **Modo de prueba**: Actívalo para usar el entorno sandbox

## Configuración en Payphone

1. Regístrate en [Payphone](https://payphone.app/)
2. Obtén tu Token y Store ID desde el dashboard
3. Configura las URLs de callback si es necesario

## Uso

Una vez activado, los clientes podrán seleccionar "Payphone" como método de pago durante el checkout. Serán redirigidos a la cajita de pagos de Payphone para completar la transacción.

## Soporte

Para problemas o sugerencias, por favor crea un issue en el repositorio.

## Licencia

MIT
