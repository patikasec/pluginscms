# 📄 Documentación Completa: Cajita de Pagos Payphone

> **Fuente:** https://docs.payphone.app/cajita-de-pagos-payphone  
> **Formato:** Markdown (.md)  
> **Nota:** Todos los códigos están completos y sin recortar

---

## Tabla de Contenidos

1. [¿Qué es la Cajita de Pagos Payphone?](#-qué-es-la-cajita-de-pagos-payphone)
2. [Flujo de Cajita de Pagos](#-flujo-de-cajita-de-pagos-)
3. [Consideraciones Previas](#-consideraciones-previas)
4. [Configuración del Ambiente y Obtención de Credenciales](#-configuración-del-ambiente-y-obtención-de-credenciales)
5. [Insertar Cajita de Pagos Payphone](#-insertar-cajita-de-pagos-payphone)
6. [Descripción de Parámetros](#-descripción-de-parámetros-en-la-petición)
7. [Estructura Final del Código](#-estructura-final-del-código)
8. [Consultar Respuesta de la Transacción](#-consultar-respuesta-de-la-transacción)
9. [Ejemplos de Implementación API Confirm](#-ejemplos-de-implementación-api-confirm)
10. [Pruebas y Paso a Producción](#-pruebas-y-paso-a-producción)
11. [Reverso de Transacciones](#-reverso-de-transacciones)
12. [Funcionalidades Adicionales](#-funcionalidades-adicionales)
13. [Referencia Rápida](#-referencia-rápida)

---

## 📎 ¿Qué es la Cajita de Pagos Payphone?

La **Cajita de Pagos de Payphone** es una solución de pago digital diseñada para facilitar transacciones seguras y ágiles entre negocios y clientes. Esta herramienta permite aceptar pagos con:

- 💳 Tarjetas de crédito y débito: **Visa, MasterCard, Diners Club y Discover**
- 📱 Saldo Payphone

Brindando una experiencia de pago versátil y práctica para cualquier tipo de negocio o proyecto en línea.

### Casos de uso:
- 🛒 E-commerce
- 👨‍💻 Freelancers
- 🏪 Pequeños emprendimientos
- 🎓 Servicios educativos
- 🚚 Delivery y transporte

Payphone transforma los cobros en un proceso fácil de implementar utilizando tecnologías como **HTML**, **JavaScript** y solicitudes **POST**, ideales para integrar en páginas web de forma rápida y sin complicaciones.

---

## 🚀 Flujo de Cajita de Pagos 💳 Payphone

La **Cajita de Pagos** de Payphone es una solución ágil y segura para integrar pagos en línea directamente en tu sitio web. Con esta herramienta, tus clientes podrán pagar sin salir de tu página.

### ¿Cómo funciona? La integración se realiza en **dos fases clave**:

#### 1️⃣ 🧩 Preparación

En esta etapa se incorpora el botón de pago a tu sitio utilizando los recursos proporcionados por Payphone:

1. **Script JS**, fuente **CSS** y **etiqueta HTML**
2. Permite que controles totalmente las acciones de pago, registrando los montos, impuestos y demás detalles de la transacción
3. Acepta pagos con tarjetas **Visa, MasterCard, Diners Club, Discover** o saldo Payphone

#### 2️⃣ ✅ Confirmación

Una vez que el usuario completa el pago, Payphone redirige a tu sitio con parámetros en la URL que representan el resultado de la operación.

Tu sistema debe:

1. **Capturar los parámetros de respuesta**
2. Realizar una solicitud **POST** a la **API de Confirmación** de Payphone
3. Obtener el **detalle completo de la transacción** (estado, monto, autorización, etc.)

### 🔙 Importante: Reverso Automático

> ⚠️ **Si tu sistema NO ejecuta la fase de confirmación dentro de los primeros 5 minutos después del pago, Payphone REVERSARÁ automáticamente la transacción.**

Esto se hace para proteger tanto al comercio como al cliente, evitando:
- ❌ Cobros indebidos
- ❌ Procesos incompletos por falta de datos
- ❌ Conflictos o reclamos por parte del cliente

> 📖 **En resumen:** Si no confirmas el pago, **Payphone lo cancela automáticamente**, ya que no puede garantizar que el comercio haya registrado correctamente la transacción.

---

## ⚠️ Consideraciones Previas

Antes de comenzar con la implementación de la Cajita de Pagos de Payphone, asegúrate de cumplir con los siguientes requisitos:

### 🌐 Plataforma
- La **Cajita de Pagos** está diseñada exclusivamente para entornos **WEB**. No está disponible para aplicaciones móviles directamente.

### 🔒 Dominio y entorno seguro
- Es indispensable contar con un dominio que tenga un **certificado SSL (https://)** válido para producción
- Para fines de **prueba o desarrollo local**, puedes utilizar `http://localhost` sin necesidad de certificado SSL
- **Importante:** La **Cajita de Pagos está vinculada directamente al dominio configurado** en tu cuenta Payphone
- Si se intenta ejecutar desde otro dominio no autorizado, **se mostrará un error de autorización** y el proceso de pago no podrá completarse

### 🏢 Cuenta en Payphone Business
- Debes tener una **cuenta activa en Payphone Business**
- Registro: [Payphone Business](https://business.payphonetodoesposible.com)

### 👨‍💻 Usuario con rol de Desarrollador
- Dentro de tu cuenta Payphone Business, deberás **crear un usuario** con el rol de **Desarrollador**
- Este usuario tendrá acceso a las configuraciones técnicas y a la generación de credenciales para integración

### 💻 Entorno de desarrollo
Asegúrate de contar con:
- Editor de código (VSCode, WebStorm, etc.)
- Navegador actualizado
- Herramientas de prueba como Postman, Insomnia o cURL
- Conocimientos básicos de **HTML, JavaScript** y consumo de APIs REST

### 🔑 Obtención de credenciales
Desde la plataforma para desarrolladores de Payphone, deberás:
1. Crear una nueva configuración de aplicación
2. Obtener las **credenciales necesarias para la integración**:
   - **`TOKEN`** de autenticación (Bearer Token)
   - **`STOREID`** asociado a tu comercio

---

## 🔑 Configuración del Ambiente y Obtención de Credenciales

¡Prepara tu plataforma para recibir pagos de forma **segura, fácil y eficiente** con Payphone! 🛒💳

### 🚀 Lo primero es configurar tu ambiente

Para que Payphone funcione correctamente, necesitas establecer una conexión segura entre tu sistema y nuestra plataforma.

### Credenciales clave: Token y StoreID

### 🛠️ ¿Cómo hacerlo?

1. **Configura tu API.** Desde tu cuenta de Payphone Developer asegúrate de haber creado una aplicación de **tipo: "WEB"**
2. Al elegir aplicación de tipo **"WEB"**, se requerirá completar dos campos nuevos:
   - **Dominio Web**
   - **URL de Respuesta**
3. **Obtén tus credenciales.** Estos datos son esenciales para autenticarte con Payphone. Encuéntralos al configurar tu aplicación
4. **Establece tu entorno de desarrollo y pruebas.** Esto te permitirá realizar simulaciones antes de pasar a la producción

### 🎯 ¿Por qué es importante?

1. **Seguridad:** Tus transacciones estarán encriptadas y protegidas contra accesos no autorizados
2. **Personalización:** Adapta los métodos de pago según las necesidades de tu negocio
3. **Funcionalidad:** Garantiza que los pagos se procesen correctamente

> ⚠️ **NOTA:** Sin esta configuración, no podrás procesar pagos a través de nuestra plataforma.

---

## 🛠️ Insertar Cajita de Pagos Payphone

La integración de la **Cajita de Pagos** en tu sitio web es rápida y sencilla. Solo necesitas agregar **dos scripts**, una **fuente CSS**, un **fragmento de JavaScript de configuración**, y un contenedor HTML donde aparecerá el botón de pago.

### 🧷 Agrega las dependencias en la cabecera de tu página

```html
<head>
    <!-- Hoja de estilos CSS: aplica el diseño visual del botón de pago -->
    <link rel="stylesheet" href="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.css">
    
    <!-- SDK JavaScript: habilita el control y la lógica de la Cajita -->
    <script type="module" src="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.js"></script>
</head>
```

### ⚙️ Inserta el script de configuración

Este script es el núcleo de la integración. Define los datos de la transacción, credenciales, impuestos, y comportamiento del formulario.

```javascript
<script>
    // Cobro mixto: 1 USD con impuesto del 15% y 2 USD sin impuesto
    window.addEventListener('DOMContentLoaded', () => {
        ppb = new PPaymentButtonBox({
            // Credenciales de acceso (OBLIGATORIO)
            token: 'ACA TU TOKEN',
            
            // Identificador único por transacción (OBLIGATORIO - Máx 50 caracteres)
            clientTransactionId: 'ID_UNICO_X_TRANSACCION-001',
            
            // Valor total en centavos (OBLIGATORIO)
            amount: 315,
            
            // Monto que no está sujeto a impuestos
            amountWithoutTax: 200,
            
            // Monto que incluye el valor sujeto a impuestos (excluyendo el impuesto)
            amountWithTax: 100,
            
            // Monto del impuesto aplicado
            tax: 15,
            
            // Monto asociado al servicio proporcionado
            service: 0,
            
            // Monto de la propina otorgada por el cliente
            tip: 0,
            
            // Código de moneda ISO 4217
            currency: "USD",
            
            // Identificador de la sucursal (OBLIGATORIO si se configura en Payphone Developer)
            storeId: "TU_STOREID",
            
            // Motivo o referencia específica del pago (Máx 100 caracteres)
            reference: "Pago por venta Fact#001",
            
            // Idioma del formulario: "es" (español) o "en" (inglés)
            lang: "es",
            
            // Método por defecto: "card" (Tarjeta) o "payphone" (Saldo Payphone)
            defaultMethod: "card",
            
            // Zona horaria (ej: -5 para Ecuador)
            timeZone: -5,
            
            // Coordenadas geográficas (opcional)
            lat: "-1.831239",
            lng: "-78.183406",
            
            // Información adicional para la transacción
            optionalParameter: "Parametro opcional",
            
            // Datos del titular de la tarjeta (OPCIONALES pero recomendados)
            phoneNumber: "+593999999999",
            email: "aloy@mail.com",
            documentId: "1234567890",
            
            // Tipo de identificación: 1=Cédula, 2=RUC, 3=Pasaporte (Por defecto: 1)
            identificationType: 1
        }).render('pp-button');
    })
</script>
```

### 📦 Inserta el contenedor del botón

Debes agregar un contenedor `<div id="pp-button">` en la ubicación donde desees que aparezca el botón de pago:

```html
<div id="pp-button"></div>
```

---

## 📌 Consideraciones Importantes

### 📟 Cálculo del monto total (`amount`)

El campo `amount` debe ser la **suma de todos los valores monetarios**:

```
amount = amountWithoutTax + amountWithTax + tax + service + tip
```

> ⚠️ Aunque los campos individuales son opcionales, **debe haber al menos uno presente** que respalde el valor total `amount`.

### 💵 Valores monetarios en centavos

Todos los montos deben expresarse como **enteros**. Multiplica el valor en dólares por 100:

| 💵 Valor en USD | 🪙 Valor en centavos |
|----------------|---------------------|
| $ 1.00 | 100 |
| $ 1.50 | 150 |
| $ 10.00 | 1000 |
| $ 12.68 | 1268 |
| $ 0.99 | 99 |

### 📈 Ejemplos básicos de configuración

#### Ejemplo 1: Montos Con Impuestos

```javascript
<script>
    // Ejemplo de cobro de 1 USD con impuesto del 15%
    window.addEventListener('DOMContentLoaded', () => {
        ppb = new PPaymentButtonBox({
            token: 'ACA TU TOKEN',
            clientTransactionId: 'ID_UNICO_X_TRANSACCION-001',
            amount: 115,
            amountWithTax: 100,
            tax: 15,
            currency: "USD",
            storeId: "TU_STOREID",
            reference: "Pago por venta Fact#001"
        }).render('pp-button');
    })
</script>
```

#### Ejemplo 2: Montos Sin Impuestos

```javascript
<script>
    // Ejemplo de cobro de 2 USD sin impuesto
    window.addEventListener('DOMContentLoaded', () => {
        ppb = new PPaymentButtonBox({
            token: 'ACA TU TOKEN',
            clientTransactionId: 'ID_UNICO_X_TRANSACCION-001',
            amount: 200,
            amountWithoutTax: 200,
            currency: "USD",
            storeId: "TU_STOREID",
            reference: "Pago por venta Fact#001"
        }).render('pp-button');
    })
</script>
```

#### Ejemplo 3: Montos Mixtos

```javascript
<script>
    // Ejemplo de un cobro mixto: 1 USD con impuesto del 15% y 2 USD sin impuesto
    window.addEventListener('DOMContentLoaded', () => {
        ppb = new PPaymentButtonBox({
            token: 'ACA TU TOKEN',
            clientTransactionId: 'ID_UNICO_X_TRANSACCION-001',
            amount: 315,
            amountWithoutTax: 200,
            amountWithTax: 100,
            tax: 15,
            currency: "USD",
            storeId: "TU_STOREID",
            reference: "Pago por venta Fact#001"
        }).render('pp-button');
    })
</script>
```

---

## 🧾 Descripción de Parámetros en la Petición

| Nombre | Descripción | Tipo de Dato | Opcional |
|--------|-------------|--------------|----------|
| **`token`** | Credencial que se genera en la configuración de Payphone Developer | String | ❌ No |
| **`amount`** | Valor total de la factura a cobrar. Es la suma de `amountWithTax`, `amountWithoutTax`, `tax`, `service` y `tip` | Int | ❌ No |
| **`amountWithoutTax`** | Monto que no está sujeto a impuestos | Int | ❌ No* |
| **`amountWithTax`** | Monto que incluye el valor sujeto a impuestos, excluyendo el propio impuesto | Int | ✅ Sí |
| **`tax`** | Monto del impuesto aplicado a la transacción | Int | ✅ Sí |
| **`service`** | Monto asociado al servicio proporcionado | Int | ✅ Sí |
| **`tip`** | Monto de la propina otorgada por el cliente | Int | ✅ Sí |
| **`currency`** | Código de moneda ISO 4217 (ej: USD) | String | ✅ Sí |
| **`clientTransactionId`** | Identificador único asignado por el comercio a cada transacción para su seguimiento. Máximo 50 caracteres | String | ❌ No |
| **`storeId`** | Identificador de la sucursal que efectúa el cobro (se obtiene en Payphone Developer) | String | ✅ Sí |
| **`reference`** | Motivo o referencia específica del pago. Máximo 100 caracteres | String | ✅ Sí |
| **`phoneNumber`** | Número de teléfono del titular. Formato: `+` + Código País + número. Ej: `+593984111222` | String | ✅ Sí |
| **`email`** | Correo electrónico del titular | String | ✅ Sí |
| **`documentId`** | Número de identificación del titular | String | ✅ Sí |
| **`identificationType`** | Tipo de identificación: `1`=Cédula, `2`=RUC, `3`=Pasaporte. Por defecto: `1` | Int | ✅ Sí |
| **`lang`** | Idioma del Formulario: `en` (inglés), `es` (español). Por defecto: `es` | String | ✅ Sí |
| **`defaultMethod`** | Método por defecto a mostrar: `"card"` (Tarjeta), `"payphone"` (Saldo Payphone) | String | ✅ Sí |
| **`timeZone`** | Zona horaria (ej: -5) | Int | ✅ Sí |
| **`lat`** | Latitud en formato decimal (ej: `-1.831239`) | String | ✅ Sí |
| **`lng`** | Longitud en formato decimal (ej: `-78.183406`) | String | ✅ Sí |
| **`optionalParameter`** | Información adicional para la transacción | String | ✅ Sí |

> ⚠️ **Advertencia sobre datos del titular:**
> 
> Al utilizar los campos `phoneNumber`, `email` y `documentId` en las solicitudes a los servicios de Payphone, **es crucial que se ingresen los datos del titular de la tarjeta para cada transacción individual**.
> 
> ❌ No se permite el uso de datos "quemados" o estáticos, ya que esto puede resultar en:
> - Rechazo de transacciones
> - Bloqueo de usuarios
> - Sospechas de fraude por datos falsos o repetitivos
> 
> Payphone se compromete a proteger la seguridad y privacidad de los datos. Cumplir con esta política es fundamental para garantizar un proceso de pago seguro y confiable.

---

## 🧱 Estructura Final del Código

### Opción 1: Cajita con DOM (incrustada en página)

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago con Payphone</title>
    
    <!-- Dependencias de Payphone -->
    <script src="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.js" type="module"></script>
    <link href="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.css" rel="stylesheet">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .payment-container {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Realizar Pago</h1>
    
    <div class="payment-container">
        <h3>Detalles del Pedido</h3>
        <p><strong>Producto:</strong> Ejemplo de Producto</p>
        <p><strong>Total:</strong> $3.15 USD</p>
        
        <!-- Contenedor donde se renderizará el botón de Payphone -->
        <div id="pp-button"></div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            ppb = new PPaymentButtonBox({
                // Credenciales (REEMPLAZAR con valores reales)
                token: 'ACA TU TOKEN',
                storeId: 'TU_STOREID',
                
                // Identificador único de transacción
                clientTransactionId: 'ID_UNICO_X_TRANSACCION-001',
                
                // Montos en centavos
                amount: 315,
                amountWithoutTax: 200,
                amountWithTax: 100,
                tax: 15,
                service: 0,
                tip: 0,
                
                // Configuración general
                currency: "USD",
                reference: "Pago por venta Fact#001",
                lang: "es",
                defaultMethod: "card",
                timeZone: -5,
                
                // Datos opcionales del cliente
                phoneNumber: "+593999999999",
                email: "aloy@mail.com",
                documentId: "1234567890",
                identificationType: 1,
                
                // Parámetros adicionales
                optionalParameter: "Parametro opcional"
            }).render('pp-button');
        })
    </script>
</body>
</html>
```

### Opción 2: Cajita con Modal (popup)

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popup con Cajita de Pagos Payphone</title>
    
    <!-- Dependencias de Payphone -->
    <link rel="stylesheet" href="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.css">
    <script type="module" src="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.js"></script>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        
        /* Overlay para el modal */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
        }
        
        /* Modal */
        #popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            z-index: 999;
            min-width: 320px;
            max-width: 90%;
        }
        
        #btn-cerrar {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #f44336;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-weight: bold;
        }
        
        #btn-cerrar:hover {
            background: #d32f2f;
        }
        
        .btn-pago {
            background: #6610f2;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-pago:hover {
            background: #520dc2;
        }
    </style>
</head>
<body>
    <h1>Integración de Cajita de Pagos Payphone</h1>
    
    <button class="btn-pago" onclick="mostrarPopup()">Realizar Pago</button>
    
    <!-- Overlay -->
    <div id="overlay" onclick="cerrarPopup()"></div>
    
    <!-- Modal -->
    <div id="popup">
        <button id="btn-cerrar" onclick="cerrarPopup()">×</button>
        <h3>Completar Pago</h3>
        <div id="pp-button"></div>
    </div>

    <script>
        // Ocultar botón de cerrar inicialmente
        document.getElementById('btn-cerrar').style.display = 'none';
        
        /**
         * Muestra el popup con la cajita de pagos
         */
        function mostrarPopup() {
            // Mostrar el popup y overlay
            document.getElementById('popup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('btn-cerrar').style.display = 'block';

            // Ejecutar la cajita de pagos
            ejecutarCajitaPagos();
        }

        /**
         * Cierra el popup
         */
        function cerrarPopup() {
            document.getElementById('popup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        /**
         * Inicializa y renderiza la Cajita de Pagos
         */
        function ejecutarCajitaPagos() {
            // Generar ID único para esta transacción
            const clientTransactionID = "ID-UNICO-X-TRANSACCION-" + Date.now();
            
            const ppb = new PPaymentButtonBox({
                // Token obtenido desde la consola de Payphone Developer
                token: 'YOUR_TOKEN',
                
                // Montos en centavos
                amount: 315,
                amountWithoutTax: 200,
                amountWithTax: 100,
                tax: 15,
                service: 0,
                tip: 0,
                
                // Configuración
                storeId: "YOUR_STOREID",
                reference: "Motivo de Pago",
                currency: 'USD',
                clientTransactionId: clientTransactionID,
                
                // Personalización visual
                backgroundColor: "#6610f2",
                
                // Idioma y método por defecto
                lang: "es",
                defaultMethod: "card"
            }).render('pp-button');
        }
    </script>
</body>
</html>
```

---

## 📌 Consideraciones Adicionales

### ⏱️ Tiempo de vida del formulario
- Cada formulario de la Cajita de Pagos tiene una vigencia de **10 minutos** desde su carga
- Si el usuario no completa el pago en ese tiempo, el formulario expirará y se deberá generar uno nuevo

### 🚫 Error de "Acceso denegado" en Cajita de Pagos

#### ¿Por qué ocurre este error?

El mensaje **"Acceso denegado o dominio no permitido"** puede deberse a:

#### 1. 🌐 Dominio no autorizado
- La **Cajita de Pagos solo funciona en el dominio registrado** en tu configuración de desarrollador en Payphone Developer
- Si intentas ejecutarla desde otro dominio o mediante una redirección no autorizada, se bloqueará por motivos de seguridad

> ✅ **Solución:** Asegúrate de que la redirección hacia el formulario de pago se realice **desde el mismo dominio** que configuraste en tu cuenta de Payphone.

#### 2. 🛡️ Falta de identidad del sitio (Referrer-Policy)

En algunos frameworks o servidores, existe una configuración por defecto que **no comparte la identidad del sitio web** desde donde se hace la redirección al formulario de pago.

Esto impide que Payphone verifique el origen de la solicitud y provoca el error de acceso denegado.

##### Configuraciones que pueden causar el error:
- **Referrer-Policy:** en plataformas como **C# / ASP.NET**, **WordPress**, entre otras
- **SECURE_REFERRER_POLICY:** Desde **Django 3.1** en adelante

#### 📘 ¿Qué es la Referrer-Policy?

La **Referrer-Policy** es una política de seguridad del navegador que define qué información del origen (referencia) se envía al hacer peticiones a otros recursos.

##### Opciones más comunes:

| Política | Descripción |
|----------|-------------|
| `no-referrer` | No se envía ninguna referencia. Máxima privacidad, pero puede romper funcionalidades |
| `no-referrer-when-downgrade` | Envía la referencia **solo si** ambos sitios usan HTTPS |
| `origin` | Envía solo el esquema y host del sitio (sin ruta). Nivel medio de privacidad |
| `origin-when-cross-origin` | Como `origin`, pero solo en solicitudes entre sitios distintos |
| `strict-origin` | Similar a `origin`, pero restringido a HTTPS |
| `strict-origin-when-cross-origin` | Mayor control sobre envíos entre orígenes cruzados |

> ✅ **Recomendación de Payphone:**
> 
> Para garantizar el correcto funcionamiento de la Cajita de Pagos, recomendamos usar:
> - `origin`
> - `origin-when-cross-origin`
> 
> Esto proporciona el equilibrio ideal entre **funcionalidad y privacidad**, y permite a Payphone validar correctamente el origen de la solicitud.

##### Ejemplo de configuración en diferentes entornos:

**Apache (.htaccess):**
```apache
<IfModule mod_headers.c>
    Header set Referrer-Policy "origin-when-cross-origin"
</IfModule>
```

**Nginx:**
```nginx
add_header Referrer-Policy "origin-when-cross-origin";
```

**PHP:**
```php
header("Referrer-Policy: origin-when-cross-origin");
```

**HTML meta tag:**
```html
<meta name="referrer" content="origin-when-cross-origin">
```

---

## 🧾 Consultar Respuesta de la Transacción

Una vez que el usuario completa el pago, será redirigido automáticamente a la **URL de respuesta** que hayas configurado previamente en la plataforma de Payphone.

### Parámetros en la URL de respuesta

Esta URL incluirá dos parámetros esenciales en la cadena de consulta:

| Parámetro | Descripción | Tipo |
|-----------|-------------|------|
| `id` | Número entero que representa el identificador único de la transacción generado por **Payphone** | Integer |
| `clientTransactionId` | Cadena de texto definida como identificador único por **tu plataforma** al iniciar el pago | String |

### 🔸 Ejemplo en PHP para obtener los parámetros de la URL:

```php
<?php
// Obtener parámetros enviados por Payphone desde la URL de respuesta
$id = isset($_GET["id"]) ? $_GET["id"] : 0;
$clientTransactionId = isset($_GET["clientTransactionId"]) ? $_GET["clientTransactionId"] : "";

// Validar que los parámetros existan
if ($id == 0 || empty($clientTransactionId)) {
    echo "Error: Parámetros de transacción no válidos";
    exit;
}
?>
```

---

### ✔️ Confirmar el Estado de la Transacción: API Button/Confirm

Para verificar si una transacción fue **aprobada, cancelada o fallida**, debes realizar una solicitud al **endpoint de confirmación**.

Esto te permitirá mostrar un mensaje claro al usuario sobre el resultado.

#### 🔗 Endpoint del API `Confirm`

```
POST https://pay.payphonetodoesposible.com/api/button/V2/Confirm
```

#### 📦 Cuerpo de la solicitud (JSON)

El cuerpo de la solicitud debe ser un objeto JSON que contenga los siguientes parámetros:

```json
{
  "id": 0,
  "clientTxId": "string"
}
```

#### 🔐 Cabeceras requeridas

Es fundamental incluir las siguientes cabeceras en la solicitud:

| Cabecera | Valor | Descripción |
|----------|-------|-------------|
| `Authorization` | `Bearer TU_TOKEN` | Token de autenticación de tu aplicación (el mismo usado al preparar la transacción) |
| `Content-Type` | `application/json` | Indica que el formato de los datos enviados es JSON |

---

### 📬 Respuesta satisfactoria de solicitud POST

Si la solicitud es correcta, recibirás un objeto JSON con el detalle de la transacción:

```json
{
    "email": "aloy@mail.com",
    "cardType": "Credit",
    "bin": "530219",
    "lastDigits": "XX17",
    "deferredCode": "00000000",
    "deferred": false,
    "cardBrandCode": "51",
    "cardBrand": "Mastercard Produbanco/Promerica",
    "amount": 315,
    "clientTransactionId": "ID_UNICO_X_TRANSACCION-001",
    "phoneNumber": "593999999999",
    "statusCode": 3,
    "transactionStatus": "Approved",
    "authorizationCode": "W23178284",
    "message": null,
    "messageCode": 0,
    "transactionId": 23178284,
    "document": "1234567890",
    "currency": "USD",
    "optionalParameter3": "Descripción Extra",
    "optionalParameter4": "ELISABETH SOBECK",
    "storeName": "Tienda Payphone",
    "date": "2023-10-10T11:57:26.367",
    "regionIso": "EC",
    "transactionType": "Classic",
    "reference": "Pago por venta Fact#001"
}
```

---

### 📝 Descripción de parámetros de respuesta

| Nombre | Descripción |
|--------|-------------|
| `statusCode` | Código de estado: `2` = Cancelado, `3` = Aprobada |
| `transactionStatus` | Estado de la transacción: `"Approved"` o `"Canceled"` |
| `clientTransactionId` | Identificador de transacción que enviaste en la petición |
| `authorizationCode` | Código de autorización bancario |
| `transactionId` | Identificador de transacción asignado por Payphone |
| `email` | Correo electrónico registrado en el formulario |
| `phoneNumber` | Número de teléfono registrado en el formulario |
| `document` | Número de cédula registrado en el formulario |
| `amount` | Monto total pagado (en centavos) |
| `cardType` | Tipo de tarjeta: `"Credit"` o `"Debit"` |
| `cardBrandCode` | Código de la marca de la tarjeta |
| `cardBrand` | Marca de la tarjeta: Visa, MasterCard, Diners Club, Discover y Banco Emisor |
| `bin` | Primeros 6 dígitos de la tarjeta utilizada |
| `lastDigits` | Últimos dígitos de la tarjeta utilizada |
| `deferredCode` | Código de diferido empleado por el usuario |
| `deferredMessage` | Mensaje del diferido |
| `deferred` | Booleano: indica si se usó un diferido |
| `message` | Mensaje de error, si corresponde |
| `messageCode` | Código de mensaje de error |
| `currency` | Moneda utilizada para el pago |
| `reference` | Motivo de la transacción |
| `optionalParameter3` | Parámetro opcional enviado |
| `optionalParameter4` | Nombre del titular si el pago es con tarjeta |
| `storeName` | Nombre de la tienda que cobró |
| `date` | Fecha de cobro en formato ISO 8601 |
| `regionIso` | Código de país en ISO 3166-1 |
| `transactionType` | Tipo de transacción |

---

### 📬 Respuesta con error de solicitud POST

Si la solicitud contiene algún error, recibirás un objeto JSON con el detalle:

```json
{
    "message": "La transacción no existe, verifique que el identificador enviado sea correcto.",
    "errorCode": 20
}
```

#### Códigos de error comunes:

| errorCode | message | Solución |
|-----------|---------|----------|
| `20` | La transacción no existe | Verificar que `id` y `clientTxId` sean correctos |
| `401` | Token inválido | Verificar el token en la cabecera Authorization |
| `400` | Parámetros requeridos faltantes | Incluir `id` y `clientTxId` en el body |
| `500` | Error interno del servidor | Reintentar más tarde o contactar soporte |

---

### 🔙 Recordatorio: Reverso Automático

> ⚠️ **Si tu sistema NO ejecuta la fase de confirmación dentro de los primeros 5 minutos después del pago, Payphone REVERSARÁ automáticamente la transacción.**

Esto protege tanto al comercio como al cliente, evitando:
- ❌ Cobros indebidos
- ❌ Procesos incompletos por falta de datos
- ❌ Conflictos o reclamos por parte del cliente

> 📖 **En resumen:** Si no confirmas el pago, **Payphone lo cancela automáticamente**, ya que no puede garantizar que el comercio haya registrado correctamente la transacción.

---

## 🧱 Ejemplos de Implementación API Button/Confirm

### Ejemplo 1: PHP con cURL

```php
<?php
/**
 * Confirmar transacción Payphone - Ejemplo en PHP
 */

// 1. Obtener parámetros enviados por Payphone desde la URL de respuesta
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$clientTxId = isset($_GET["clientTransactionId"]) ? $_GET["clientTransactionId"] : "";

// Validar parámetros
if ($id === 0 || empty($clientTxId)) {
    http_response_code(400);
    echo json_encode([
        "error" => true,
        "message" => "Parámetros de transacción no válidos"
    ]);
    exit;
}

// 2. Preparar cabeceras para la solicitud
$headers = [
    'Authorization: Bearer your_token',  // REEMPLAZAR con tu token real
    'Content-Type: application/json'
];

// 3. Preparar objeto JSON para la solicitud
$data = [
    "id" => $id,
    "clientTxId" => $clientTxId
];
$objetoJSON = json_encode($data);

// 4. Iniciar solicitud cURL: POST
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://pay.payphonetodoesposible.com/api/button/V2/Confirm",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $objetoJSON,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => true
]);

// 5. Ejecutar solicitud y obtener respuesta
$curl_response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curl_error = curl_error($curl);

curl_close($curl);

// 6. Manejar errores de conexión
if ($curl_error) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "Error de conexión: " . $curl_error
    ]);
    exit;
}

// 7. Procesar respuesta JSON
$result = json_decode($curl_response, true);

// 8. Mostrar resultado (en producción, guarda en base de datos y redirige al usuario)
header('Content-Type: application/json; charset=utf-8');

if ($http_code === 200 && isset($result['statusCode'])) {
    // Transacción confirmada exitosamente
    if ($result['statusCode'] === 3 && $result['transactionStatus'] === 'Approved') {
        // ✅ Pago aprobado - Aquí puedes:
        // - Actualizar el estado del pedido en tu base de datos
        // - Enviar correo de confirmación
        // - Redirigir a página de éxito
        
        echo json_encode([
            "success" => true,
            "message" => "Pago aprobado exitosamente",
            "data" => [
                "transactionId" => $result['transactionId'],
                "authorizationCode" => $result['authorizationCode'],
                "amount" => $result['amount'] / 100,  // Convertir a dólares
                "currency" => $result['currency'],
                "reference" => $result['reference']
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
    } elseif ($result['statusCode'] === 2 || $result['transactionStatus'] === 'Canceled') {
        // ❌ Pago cancelado
        echo json_encode([
            "success" => false,
            "message" => "El pago fue cancelado por el usuario",
            "data" => $result
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
    } else {
        // ⚠️ Otro estado
        echo json_encode([
            "success" => false,
            "message" => "Estado de transacción no reconocido",
            "data" => $result
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
} else {
    // Error en la respuesta de Payphone
    echo json_encode([
        "error" => true,
        "message" => $result['message'] ?? "Error al confirmar la transacción",
        "errorCode" => $result['errorCode'] ?? null,
        "httpCode" => $http_code
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>
```

---

### Ejemplo 2: jQuery con AJAX

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación con jQuery</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .resultado { 
            background: #f8f9fa; 
            border: 1px solid #dee2e6; 
            border-radius: 6px; 
            padding: 15px; 
            margin-top: 20px;
            white-space: pre-wrap;
            font-family: monospace;
        }
        .loading { color: #666; font-style: italic; }
        .error { color: #dc3545; }
        .success { color: #28a745; }
    </style>
</head>
<body>
    <h1>Confirmación de Transacción Payphone</h1>
    
    <div id="loading" class="loading">Procesando confirmación...</div>
    <div id="resultado"></div>

    <script>
        /**
         * Función para obtener parámetros de la URL
         * @param {string} variable - Nombre del parámetro a buscar
         * @returns {string} Valor del parámetro o false si no existe
         */
        function getQueryVariable(variable) {
            var query = window.location.search.substring(1);
            var vars = query.split("&");
            for (var i = 0; i < vars.length; i++) {
                var pair = vars[i].split("=");
                if (pair[0] === variable) {
                    return decodeURIComponent(pair[1]);
                }
            }
            return false;
        }

        // Obtener parámetros de Payphone
        var id = getQueryVariable('id');
        var clientTxId = getQueryVariable('clientTransactionId');

        $(document).ready(function() {
            // Validar parámetros
            if (!id || !clientTxId) {
                $('#loading').hide();
                $('#resultado').html('<p class="error">❌ Error: Parámetros de transacción no válidos</p>');
                return;
            }

            // Realizar solicitud AJAX a la API de Confirmación
            $.ajax({
                url: "https://pay.payphonetodoesposible.com/api/button/V2/Confirm",
                type: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer your_token"  // ⚠️ REEMPLAZAR con tu token real
                },
                data: JSON.stringify({
                    "id": parseInt(id),
                    "clientTxId": clientTxId
                }),
                timeout: 30000,  // 30 segundos de timeout
                success: function(response) {
                    $('#loading').hide();
                    
                    if (response.statusCode === 3 && response.transactionStatus === 'Approved') {
                        // ✅ Pago aprobado
                        $('#resultado').html(
                            '<p class="success">✅ Pago aprobado exitosamente</p>' +
                            '<p><strong>ID Transacción:</strong> ' + response.transactionId + '</p>' +
                            '<p><strong>Código de Autorización:</strong> ' + response.authorizationCode + '</p>' +
                            '<p><strong>Monto:</strong> $' + (response.amount / 100).toFixed(2) + ' ' + response.currency + '</p>' +
                            '<p><strong>Referencia:</strong> ' + response.reference + '</p>' +
                            '<details><summary>Ver respuesta completa</summary><pre>' + 
                            JSON.stringify(response, null, 2) + '</pre></details>'
                        );
                        
                        // Aquí puedes redirigir o actualizar la página
                        // window.location.href = '/pedido-exitoso?tx=' + response.transactionId;
                        
                    } else if (response.statusCode === 2 || response.transactionStatus === 'Canceled') {
                        // ❌ Pago cancelado
                        $('#resultado').html(
                            '<p class="error">❌ El pago fue cancelado</p>' +
                            '<pre>' + JSON.stringify(response, null, 2) + '</pre>'
                        );
                    } else {
                        // ⚠️ Otro estado
                        $('#resultado').html(
                            '<p>⚠️ Estado: ' + response.transactionStatus + '</p>' +
                            '<pre>' + JSON.stringify(response, null, 2) + '</pre>'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    $('#loading').hide();
                    
                    let errorMsg = "Error en la solicitud";
                    if (xhr.responseJSON) {
                        errorMsg = xhr.responseJSON.message || errorMsg;
                    } else if (xhr.status === 401) {
                        errorMsg = "Token de autenticación inválido";
                    } else if (xhr.status === 400) {
                        errorMsg = "Parámetros de solicitud inválidos";
                    } else if (xhr.status === 0) {
                        errorMsg = "Error de conexión - Verifica tu conexión a internet";
                    }
                    
                    $('#resultado').html(
                        '<p class="error">❌ ' + errorMsg + '</p>' +
                        '<details><summary>Detalles técnicos</summary><pre>' + 
                        JSON.stringify({
                            status: xhr.status,
                            statusText: xhr.statusText,
                            response: xhr.responseJSON || xhr.responseText,
                            error: error
                        }, null, 2) + '</pre></details>'
                    );
                }
            });
        });
    </script>
</body>
</html>
```

---

### Ejemplo 3: Fetch API (JavaScript moderno)

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación con Fetch API</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            padding: 20px; 
            max-width: 800px; 
            margin: 0 auto;
            line-height: 1.6;
        }
        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 24px;
        }
        .status {
            padding: 12px 16px;
            border-radius: 6px;
            margin: 16px 0;
            font-weight: 500;
        }
        .status.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status.warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .status.info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        pre {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 12px;
            overflow-x: auto;
            font-size: 13px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            margin-top: 10px;
        }
        .btn:hover { background: #0056b3; }
        .hidden { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Confirmación de Transacción</h1>
        <p>Verificando el estado de tu pago con Payphone...</p>
        
        <div id="loading">⏳ Procesando...</div>
        
        <div id="result" class="hidden">
            <div id="statusMessage"></div>
            <div id="details"></div>
            <details id="rawDetails" class="hidden">
                <summary>📋 Ver respuesta completa</summary>
                <pre id="rawResponse"></pre>
            </details>
        </div>
    </div>

    <script>
        /**
         * Extrae un parámetro de la URL
         * @param {string} variable - Nombre del parámetro
         * @returns {string|null} Valor del parámetro o null
         */
        function getQueryVariable(variable) {
            const query = window.location.search.substring(1);
            const vars = query.split("&");
            
            for (let i = 0; i < vars.length; i++) {
                const pair = vars[i].split("=");
                if (pair[0] === variable) {
                    return decodeURIComponent(pair[1]);
                }
            }
            return null;
        }

        /**
         * Formatea un monto en centavos a moneda legible
         * @param {number} cents - Monto en centavos
         * @param {string} currency - Código de moneda
         * @returns {string} Monto formateado
         */
        function formatAmount(cents, currency = 'USD') {
            const amount = cents / 100;
            return new Intl.NumberFormat('es-EC', {
                style: 'currency',
                currency: currency
            }).format(amount);
        }

        /**
         * Muestra el resultado de la confirmación
         * @param {Object} response - Respuesta de la API
         */
        function displayResult(response) {
            const resultDiv = document.getElementById('result');
            const statusDiv = document.getElementById('statusMessage');
            const detailsDiv = document.getElementById('details');
            const rawDiv = document.getElementById('rawDetails');
            const rawPre = document.getElementById('rawResponse');
            
            document.getElementById('loading').classList.add('hidden');
            resultDiv.classList.remove('hidden');
            
            // Determinar estado y mostrar mensaje apropiado
            if (response.statusCode === 3 && response.transactionStatus === 'Approved') {
                statusDiv.className = 'status success';
                statusDiv.innerHTML = '✅ <strong>Pago aprobado exitosamente</strong>';
                
                detailsDiv.innerHTML = `
                    <p><strong>📦 ID de Transacción:</strong> ${response.transactionId}</p>
                    <p><strong>🔐 Código de Autorización:</strong> ${response.authorizationCode}</p>
                    <p><strong>💰 Monto:</strong> ${formatAmount(response.amount, response.currency)}</p>
                    <p><strong>📝 Referencia:</strong> ${response.reference || 'N/A'}</p>
                    <p><strong>💳 Tarjeta:</strong> ${response.cardBrand} ****${response.lastDigits}</p>
                    <p><strong>📅 Fecha:</strong> ${new Date(response.date).toLocaleString('es-EC')}</p>
                    <a href="#" class="btn" onclick="window.location.href='/mis-pedidos'">Ver mis pedidos</a>
                `;
                
            } else if (response.statusCode === 2 || response.transactionStatus === 'Canceled') {
                statusDiv.className = 'status warning';
                statusDiv.innerHTML = '⚠️ <strong>El pago fue cancelado</strong>';
                
                detailsDiv.innerHTML = `
                    <p>El usuario canceló el proceso de pago o la transacción expiró.</p>
                    <p>Puedes intentar realizar el pago nuevamente.</p>
                    <a href="#" class="btn" onclick="window.history.back()">← Volver e intentar de nuevo</a>
                `;
                
            } else {
                statusDiv.className = 'status error';
                statusDiv.innerHTML = '❌ <strong>Error en la transacción</strong>';
                
                detailsDiv.innerHTML = `
                    <p><strong>Mensaje:</strong> ${response.message || 'Estado no reconocido'}</p>
                    <p><strong>Código de estado:</strong> ${response.statusCode}</p>
                    <p>Por favor, contacta a soporte si el problema persiste.</p>
                `;
            }
            
            // Mostrar respuesta completa en el detalle expandible
            rawPre.textContent = JSON.stringify(response, null, 2);
            rawDiv.classList.remove('hidden');
        }

        /**
         * Maneja errores de la solicitud
         * @param {Error} error - Error capturado
         */
        function handleError(error) {
            const resultDiv = document.getElementById('result');
            const statusDiv = document.getElementById('statusMessage');
            const detailsDiv = document.getElementById('details');
            
            document.getElementById('loading').classList.add('hidden');
            resultDiv.classList.remove('hidden');
            
            statusDiv.className = 'status error';
            statusDiv.innerHTML = '❌ <strong>Error de conexión</strong>';
            
            detailsDiv.innerHTML = `
                <p>No se pudo conectar con el servidor de Payphone.</p>
                <p><strong>Detalles:</strong> ${error.message}</p>
                <p>Verifica tu conexión a internet e intenta nuevamente.</p>
                <button class="btn" onclick="location.reload()">🔄 Reintentar</button>
            `;
        }

        // ============================================
        // EJECUCIÓN PRINCIPAL
        // ============================================
        
        (async function confirmPayment() {
            try {
                // 1. Obtener parámetros de la URL
                const id = getQueryVariable('id');
                const clientTxId = getQueryVariable('clientTransactionId');
                
                // 2. Validar parámetros
                if (!id || !clientTxId) {
                    throw new Error('Parámetros de transacción no válidos. id y clientTransactionId son requeridos.');
                }
                
                // 3. Preparar la solicitud
                const url = "https://pay.payphonetodoesposible.com/api/button/V2/Confirm";
                const headers = {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer your_token",  // ⚠️ REEMPLAZAR con tu token real
                    "Referer": document.referrer  // Importante para Referrer-Policy
                };
                
                const body = {
                    "id": parseInt(id),
                    "clientTxId": clientTxId
                };
                
                // 4. Realizar la solicitud fetch
                const response = await fetch(url, {
                    method: "POST",
                    headers: headers,
                    body: JSON.stringify(body),
                    mode: 'cors',
                    cache: 'no-cache'
                });
                
                // 5. Procesar respuesta
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Error HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                // 6. Mostrar resultado
                displayResult(data);
                
            } catch (error) {
                console.error('Error confirmando pago:', error);
                handleError(error);
            }
        })();
    </script>
</body>
</html>
```

---

## 🔰 Pruebas y Paso a Producción

En Payphone, tienes el control total de tu integración: tú decides cuándo probar y cuándo lanzar. No necesitas pasar por procesos de certificación, ni depender de terceros para poner tu aplicación en línea.

### Payphone ofrece dos entornos listos para usar:

#### 1. 🧪 Entorno de PRUEBAS (Sandbox)

Espacio seguro y controlado para el desarrollo, integración y validación de tu aplicación.

**Características:**
- ✅ Todas las transacciones se aprueban automáticamente
- ✅ No se conecta con entidades bancarias reales
- ✅ Puedes usar datos reales (sin cobro) o datos ficticios válidos
- ✅ Compatible con herramientas como Postman o curl para pruebas automatizadas
- ✅ No se realizan cargos reales a tarjetas

**Probadores en App Payphone:**
Invita usuarios personales de Payphone como "probadores" para simular pagos reales desde la app. Ideal para validar la experiencia completa del cliente.

#### 2. 🖥️ Entorno de PRODUCCIÓN

Ambiente en el que tus usuarios finales realizarán pagos reales. Todas las transacciones aquí son efectivas y se procesan a través de la red bancaria.

**Características:**
- 💰 El dinero se transfiere directamente a tu cuenta Payphone
- 📊 Todas las transacciones se reflejan en tiempo real
- 🔐 Requiere certificado SSL válido (https://)
- 🌐 Solo funciona en el dominio registrado en Payphone Developer

### 📋 Checklist antes de pasar a producción

- [ ] ✅ Pruebas exhaustivas en entorno Sandbox completadas
- [ ] ✅ Confirmación de transacciones implementada y probada
- [ ] ✅ Manejo de errores y mensajes al usuario implementado
- [ ] ✅ Dominio configurado con certificado SSL válido
- [ ] ✅ Referrer-Policy configurado correctamente
- [ ] ✅ Credenciales de producción generadas (diferentes a las de pruebas)
- [ ] ✅ URL de respuesta configurada en Payphone Developer
- [ ] ✅ Logs y monitoreo implementados para transacciones

### 📊 Monitoreo y visualización

| Entorno | Dónde consultar transacciones |
|---------|------------------------------|
| **Pruebas** | Payphone Developer → Probadores → Transacciones |
| **Producción** | Payphone Business → Ventas → Historial |

> ⚠️ **Consideraciones importantes:**
> - Realiza pruebas exhaustivas **antes de pasar a producción**
> - En entorno de producción, **usa únicamente datos reales y verificados**
> - Nuestro sistema tiene estrictos protocolos de seguridad: asegúrate de cumplir con las normas para evitar rechazos o bloqueos

---

## ↩️ Reverso de Transacciones

Este proceso permite deshacer una transacción que ya ha sido procesada, devolviendo los fondos al cliente.

### 📋 Requisitos para reverso

Para gestionar reverso o anulación de transacciones, es necesario contar con:
- `transactionID` **o**
- `clientTransactionID`

Estos identificadores son cruciales para localizar y manipular la transacción específica.

### 🗂️ Casos de uso del método de reverso

El método de reverso es útil en diferentes situaciones:

| Situación | Descripción |
|-----------|-------------|
| 🔁 Transacciones erróneas | Cuando se generó una transacción con monto o datos incorrectos |
| 💬 Solicitud de reembolso | Cuando el cliente solicita la devolución del pago |
| ❓ Estado incierto | Cuando tu plataforma no puede confirmar el estado de la transacción |
| 🔐 Seguridad | Cuando un pago necesita ser reversado por motivos de seguridad o fraude |

### ⚠️ Restricciones temporales importantes

> 🕐 **Los reversos solo pueden ejecutarse el mismo día de la transacción original.**
> 
> 🕗 **El período de reversión está limitado hasta las 20:00 (8:00 PM) del día en que se realizó la transacción.**

### 🔧 Métodos disponibles para reverso

#### Opción 1: API Reverse

Para obtener una explicación detallada sobre el proceso de reverso a través de la API, consulta la documentación oficial:

📑 [Guía de Reverso Payphone](https://docs.payphone.app/reverso)

#### Opción 2: Reverso desde Payphone Business

Puedes realizar reversos directamente desde la plataforma administrativa:

1. Ingresa a [Payphone Business](https://business.payphonetodoesposible.com)
2. Navega a **Ventas** → **Historial de transacciones**
3. Busca la transacción que deseas reversar
4. Haz clic en **"Reversar"** o **"Anular"**
5. Confirma la operación

---

## 🔧 Funcionalidades Adicionales

### 💳 Tokenización de Tarjetas con Payphone

La **tokenización** es una funcionalidad que permite a los comercios guardar de forma segura un **identificador único (cardToken)** asociado a la tarjeta de un cliente, sin almacenar directamente los datos sensibles.

#### 🔁 ¿Cómo funciona?

**Primera Transacción:**
1. El cliente realiza un pago usando el botón o cajita de Payphone
2. Si el comercio está autorizado, Payphone genera un **cardToken**
3. El token se devuelve junto a los parámetros de la transacción aprobada

**Transacciones siguientes:**
1. El comercio utiliza el **cardToken** para realizar nuevos cobros al mismo cliente
2. El cliente no necesita volver a ingresar los datos de su tarjeta
3. Payphone procesa estos pagos usando el token de manera segura

#### 💼 Casos de uso comunes

| Caso de uso | Descripción |
|-------------|-------------|
| 🔄 Plataformas de suscripción | Cobros mensuales automáticos (Netflix, Spotify, etc.) |
| 💰 Pagos recurrentes | Membresías, servicios por cuotas, gimnasios |
| 🛒 Compras frecuentes | E-commerce que ofrece "pago rápido" o "1-click checkout" |
| 🚚 Apps de delivery/transporte | Usuarios registrados con método de pago guardado |
| 🎬 Servicios bajo demanda | Streaming, educación en línea, software SaaS |

#### 📌 Consideraciones importantes

- ✅ Esta funcionalidad requiere **autorización previa** por parte de Payphone
- ⚠️ La tokenización de Payphone **NO es un sistema de pagos recurrentes**
- 🔧 El servicio entrega los tokens para que los comercios puedan **implementar sus propios sistemas de recurrencia**
- ✅ El token solo se genera si la transacción inicial es **aprobada** por la entidad emisora
- 💳 La tokenización solo funciona para **pagos directos con tarjetas** de crédito o débito
- 👥 Debes contar con un **sistema de gestión de usuarios activo** y funcional

📑 [Guía de Tokenización Payphone](https://docs.payphone.app/tokenizacion)

---

### ➗ Split de Pagos con Payphone

El **Split de Pagos** permite **dividir un cobro entre varios usuarios Payphone** al momento de realizar un pago. Ideal para plataformas que necesiten distribuir un pago entre distintos actores.

#### 🔁 ¿Cómo funciona?

1. Se configura el servicio indicando:
   - El monto total
   - Los datos de la transacción
   - Los usuarios que recibirán un porcentaje del pago
2. Se realiza la transacción a través del canal elegido (Cajita, Botón, Link)
3. Si la operación es aprobada y el comercio tiene el permiso, el monto se distribuye automáticamente

#### 💼 Casos de uso comunes

| Caso de uso | Ejemplo |
|-------------|---------|
| 🏪 Marketplaces multiactor | Restaurantes, delivery, taxis: el pago se divide entre establecimiento, repartidor y plataforma |
| 💼 Sistemas de recaudación | Comercio principal + socio que cobra comisión |
| 🎓 Plataformas educativas | Institución + instructor + plataforma |
| 🎨 Marketplaces de servicios | Freelancer + plataforma + impuestos |

#### 📌 Consideraciones importantes

- ✅ Esta funcionalidad requiere **autorización previa** por parte de Payphone
- ⚠️ El valor a dividir **no puede superar** el total cobrado (considera la comisión del 5.75% por pagos con tarjeta)
- 💰 Si el monto dividido supera el valor cobrado, se usará el **saldo en la wallet del comercio** para cubrir la diferencia
- 🔄 Al hacer una **dispersión inmediata**, el saldo se transfiere de forma definitiva
- ⚠️ Si necesitas hacer un reverso, deberás **gestionar manualmente** el reembolso con cada usuario receptor

📑 [Guía de Split de Pagos Payphone](https://docs.payphone.app/split-de-pagos)

---

## 🎞️ Video Tutorial

Aprende a integrar la Cajita de Pago de Payphone en minutos:

📺 [Cómo integrar la cajita de Pagos de Payphone? - YouTube](https://www.youtube.com/watch?v=VIDEO_ID)

*(Nota: Reemplaza VIDEO_ID con el ID real del video en YouTube)*

---

## 📚 Referencia Rápida

### 🔗 Endpoints de la API

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `https://pay.payphonetodoesposible.com/api/button/V2/Confirm` | Confirmar estado de transacción |
| `POST` | `https://pay.payphonetodoesposible.com/api/payment/V2/Reverse` | Reversar transacción *(ver guía completa)* |

### 💻 Recursos CDN

```html
<!-- CSS (estilos del botón) -->
<link rel="stylesheet" href="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.css">

<!-- JS (SDK de Payphone) -->
<script type="module" src="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.js"></script>
```

### 🧮 Conversión de montos

```javascript
// USD a centavos
function usdToCents(amount) {
    return Math.round(amount * 100);
}

// Centavos a USD
function centsToUsd(cents) {
    return cents / 100;
}

// Ejemplos
usdToCents(1.50);    // Retorna: 150
centsToUsd(1268);    // Retorna: 12.68
```

### 🌐 Configuración de Referrer-Policy

```apache
# Apache (.htaccess)
<IfModule mod_headers.c>
    Header set Referrer-Policy "origin-when-cross-origin"
</IfModule>
```

```nginx
# Nginx
add_header Referrer-Policy "origin-when-cross-origin";
```

```php
// PHP
header("Referrer-Policy: origin-when-cross-origin");
```

```html
<!-- HTML meta tag -->
<meta name="referrer" content="origin-when-cross-origin">
```

### 🔄 Estados de transacción

| statusCode | transactionStatus | Significado |
|------------|------------------|-------------|
| `2` | `Canceled` | Transacción cancelada por el usuario o expirada |
| `3` | `Approved` | Transacción aprobada y procesada exitosamente |

### 🎨 Personalización visual

```javascript
// Parámetros de personalización en PPaymentButtonBox
const config = {
    // ... otros parámetros ...
    
    // Color de fondo del botón (hex, rgb, o nombre CSS)
    backgroundColor: "#6610f2",
    
    // Idioma de la interfaz
    lang: "es",  // o "en"
    
    // Método de pago por defecto
    defaultMethod: "card",  // o "payphone"
};
```

---

## 🆘 Soporte y Recursos

| Recurso | Enlace |
|---------|--------|
| 🏢 Payphone Business | [business.payphonetodoesposible.com](https://business.payphonetodoesposible.com) |
| 👨‍💻 Payphone Developer | [developer.payphonetodoesposible.com](https://developer.payphonetodoesposible.com) |
| 📚 Documentación completa | [docs.payphone.app](https://docs.payphone.app) |
| 💬 Soporte técnico | soporte@payphonetodoesposible.com |
| 🐛 Reportar bugs | [GitHub Issues](https://github.com/payphone/support/issues) |

---
