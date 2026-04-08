# Despliegue a Produccion

Este proyecto ya esta configurado para usar MySQL en la base de datos `CISTERNAS`.

## 1. Configuracion de entorno

El archivo `.env` ya quedo preparado en modo produccion:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `DB_CONNECTION=mysql`
- `DB_HOST=192.168.1.253`
- `DB_PORT=3306`
- `DB_DATABASE=CISTERNAS`
- `DB_USERNAME=cisternas`

Antes de arrancar en servidor, verifica que PHP tenga activo `pdo_mysql`.

## 2. Subir archivos

Sube el proyecto al servidor (manteniendo la estructura completa).

Importante:
- El document root del sitio debe apuntar a la carpeta `public`.
- No uses comandos destructivos como `migrate:fresh` sobre produccion.

## 3. Comandos de arranque en servidor

Ejecuta en la raiz del proyecto:


```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

Si usas colas en segundo plano:

```bash
php artisan queue:restart
```

## 4. Assets de frontend (Vite)

El proyecto necesita `public/build/manifest.json` para funcionar en produccion.

Opcion A (recomendada): compilar en el servidor

```bash
npm ci
npm run build
```

Opcion B: compilar en otra maquina y subir la carpeta `public/build` al servidor.

## 5. Verificacion final

- Abre la web y comprueba login/listados.
- Revisa logs en `storage/logs/laravel.log`.
- Confirma conectividad al servidor MySQL `192.168.1.253:3306`.
