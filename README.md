# Proyecto: Colaboradores y Perfil del Colaborador (iTECH Contrataciones)

## 1. Instalación en WampServer

1. Copia la carpeta `Parcial_PracticoSO7/` completa dentro de `C:\wamp64\www\`
2. Inicia WampServer (Apache + MySQL en verde).
3. Ve a `http://localhost/phpmyadmin5.2.3/` y usa (o crea) la base de datos:
   - Nombre: `tiposangre`
4. Importa los scripts SQL **en este orden** (pestaña "Importar" en phpMyAdmin, dentro de la BD `tiposangre`):
   1. `database/01_tiposangre_original.sql` (el que te dio el profesor)
   2. `database/02_schema_complementario.sql` (tablas nuevas: colaboradores, perfiles_laborales, cat_tipos_planilla + Foreign Keys)
5. Ajusta `app/Config/database.php` si tu usuario/contraseña de MySQL son distintos al default de Wamp (`root` / sin contraseña). El nombre de la BD ya está configurado como `tiposangre`.
6. Abre en el navegador: `http://localhost/Parcial_PracticoSO7/public/index.php?ruta=colaborador/crear`

## 2. Habilitar mod_rewrite (para usar .htaccess)

En WampServer, verifica que el módulo `rewrite_module` esté activo:
- Click izquierdo en el ícono de Wamp (bandeja del sistema) → Apache → Módulos Apache → marca `rewrite_module`
- Reinicia todos los servicios.

Si no lo activas, igual funciona usando siempre `index.php?ruta=...` explícitamente en la URL (como se muestra arriba).

## 3. Estado actual del proyecto (lo que ya está construido)

✅ Estructura MVC completa (`app/Models`, `app/Controllers`, `app/Views`, `app/Services`)
✅ Autoloader PSR-4 simple (sin Composer)
✅ Router con whitelist de rutas
✅ `DatabaseConnection` (Singleton + PDO + prepared statements)
✅ `ValidationService` (clase estática, Punto 28)
✅ `SanitizerService` (clase estática, Punto 29-30, incluye formato título)
✅ `FirmaDigitalService` — **OpenSSL real** (RSA 2048 bits + `openssl_sign`/`openssl_verify)
✅ Modelos: `Colaborador`, `PerfilLaboral` (con lógica de promoción), catálogos
✅ Controladores: `ColaboradorController`, `PerfilLaboralController`, `ReporteController`
✅ Vistas con CSS a color, responsive, footer con copyright 2026
✅ Reporte con indicadores verde/rojo de integridad
✅ Exportación a Excel (.xls) del reporte
✅ Script SQL con tablas nuevas + Foreign Keys (ON DELETE RESTRICT, ON UPDATE CASCADE)
✅ Modal "Dar de Baja" con campo Motivo (habilitado solo en ese contexto)

