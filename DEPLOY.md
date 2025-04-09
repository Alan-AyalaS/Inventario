# Instrucciones para el Deploy del Sistema de Inventario

## Requisitos del Servidor

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache o Nginx)
- Extensiones PHP necesarias:
  - mysqli
  - pdo
  - pdo_mysql
  - gd (para manejo de imágenes)
  - zip (para exportaciones)
  - dom (para generación de PDF)

## Pasos para el Deploy

### 1. Preparación del Entorno

1. Crear una base de datos MySQL:
   ```sql
   CREATE DATABASE inventario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Importar el esquema de la base de datos:
   ```bash
   mysql -u usuario -p inventario < schema.sql
   ```

### 2. Configuración del Proyecto

1. Copiar todos los archivos del proyecto al directorio del servidor web.

2. Configurar los permisos:
   ```bash
   chmod 755 -R .
   chmod 777 -R storage/
   chmod 777 -R assets/
   ```

3. Configurar el archivo de conexión a la base de datos:
   - Editar el archivo de configuración de la base de datos
   - Asegurarse de que las credenciales sean seguras

### 3. Configuración del Servidor Web

#### Para Apache:
```apache
<VirtualHost *:80>
    ServerName tu-dominio.com
    DocumentRoot /ruta/a/tu/proyecto
    
    <Directory /ruta/a/tu/proyecto>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

#### Para Nginx:
```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /ruta/a/tu/proyecto;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 4. Configuración de Seguridad

1. Asegurarse de que el archivo `.htaccess` esté configurado correctamente:
   ```apache
   Options -Indexes
   <FilesMatch "^\.">
       Order allow,deny
       Deny from all
   </FilesMatch>
   ```

2. Configurar HTTPS:
   - Obtener un certificado SSL (Let's Encrypt es una buena opción gratuita)
   - Configurar el servidor web para usar HTTPS

### 5. Verificación Final

1. Acceder a la URL del sistema
2. Verificar que:
   - La conexión a la base de datos funcione
   - Los archivos se puedan subir correctamente
   - Las exportaciones funcionen
   - Los permisos sean los correctos

## Mantenimiento

### Backup
- Configurar backup automático de la base de datos
- Mantener copias de seguridad de los archivos importantes

### Actualizaciones
- Mantener PHP y MySQL actualizados
- Revisar periódicamente los logs de error
- Implementar actualizaciones de seguridad cuando estén disponibles

## Solución de Problemas Comunes

1. **Error de conexión a la base de datos**:
   - Verificar credenciales
   - Comprobar que el servidor MySQL esté en ejecución
   - Verificar que el usuario tenga los permisos necesarios

2. **Problemas de permisos**:
   - Verificar que los directorios tengan los permisos correctos
   - Asegurarse de que el usuario del servidor web tenga acceso

3. **Errores 500**:
   - Revisar los logs de error
   - Verificar la configuración de PHP
   - Comprobar que todas las dependencias estén instaladas

## Contacto

Para soporte técnico o preguntas sobre el deploy, contactar al administrador del sistema. 