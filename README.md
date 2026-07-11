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
✅ `FirmaDigitalService` — **OpenSSL real** (RSA 2048 bits + `openssl_sign`/`openssl_verify`, Puntos 16 y 27)
✅ Modelos: `Colaborador`, `PerfilLaboral` (con lógica de promoción), catálogos
✅ Controladores: `ColaboradorController`, `PerfilLaboralController`, `ReporteController`
✅ Vistas con CSS a color, responsive, footer con copyright 2026
✅ Reporte con indicadores verde/rojo de integridad
✅ Exportación a Excel (.xls) del reporte
✅ Script SQL con tablas nuevas + Foreign Keys (ON DELETE RESTRICT, ON UPDATE CASCADE)
✅ Modal "Dar de Baja" con campo Motivo (habilitado solo en ese contexto)

## 4. IMPORTANTE: llaves OpenSSL

El proyecto incluye un par de llaves RSA ya generadas en `app/Config/keys/`:
- `private_key.pem` — usada para FIRMAR (nunca debería ser pública en un proyecto real, pero se incluye aquí para que el proyecto funcione de inmediato)
- `public_key.pem` — usada para VERIFICAR

**Si vas a subir el repositorio a GitHub y es público**, considera regenerar tus propias llaves y no subir la privada, o explica en tu entrega que es solo para fines académicos.

Para regenerar las llaves tú mismo (opcional):
```bash
openssl genrsa -out app/Config/keys/private_key.pem 2048
openssl rsa -in app/Config/keys/private_key.pem -pubout -out app/Config/keys/public_key.pem
```

## 5. Si ya habías importado la base de datos antes

La columna `firma_digital` cambió de `VARCHAR(255)` a `TEXT` porque las firmas RSA en base64 son más largas (~344 caracteres) que un hash simple. **Vuelve a importar `database/02_schema_complementario.sql`** (recreará las tablas `colaboradores` y `perfiles_laborales` desde cero — perderás los datos de prueba que tengas, pero es lo esperado en esta etapa de desarrollo).

## 6. Pendiente / a revisar contigo

- [ ] Probar el flujo completo end-to-end en tu Wamp (registrar colaborador → asignar perfil → promover → dar de baja)
- [ ] Revisar si el profesor exige que "Motivo de Baja" sea catálogo (`cat_motivos_terminacion`, ya implementado así) o texto libre
- [ ] Confirmar el rango de validación de "Nacionalidad" (¿selector fijo o texto libre? actualmente es texto libre sanitizado)
- [ ] Decidir cómo interpretar "el reporte con temas separados por comas" (¿tabla normal o formato de línea con comas?)
- [ ] Verificar si `cat_tipos_planilla` debe tener 4 tipos en vez de 3 (el documento menciona "Planilla (1,2,3,4)" en la sección de firma)

## 7. Notas técnicas importantes

- La tabla `` cat_sexo`` tiene un espacio en el nombre en el dump original — el modelo `CatSexo.php` ya lo maneja con backticks correctamente, no lo cambies a menos que también edites el modelo.
- La tabla `tiposangre` venía en motor MyISAM; el script `02_schema_complementario.sql` la convierte a InnoDB (necesario para que funcione como FK).
- La vinculación entre `colaboradores` y `perfiles_laborales` es por `colaborador_id` (autonumérico), nunca por `identidad`, tal como exige la rúbrica.
