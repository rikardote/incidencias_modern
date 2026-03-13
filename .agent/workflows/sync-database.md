---
description: Sincronizar Base de Datos desde SQL Dump
---

Este flujo de trabajo permite actualizar la base de datos local con un dump nuevo (como el de producción), asegurando que se mantengan las optimizaciones y compatibilidades del sistema moderno.

### Pasos a seguir:

1. **Ubicar el archivo SQL**: Asegúrate de que el dump (`.sql`) esté en la raíz del proyecto.
   
2. **Ejecutar el script**: Abre una terminal en la raíz del proyecto y corre el siguiente comando (cambia el nombre del archivo por el tuyo):

```bash
./scripts/sync_db.sh 20260313_sistemas.sql
```

### ¿Qué hace este proceso automáticamente?
- **Resetea** las bases de datos `sistemas` y `sistemas_chats`.
- **Importa** los datos del archivo que le indiques.
- **Normaliza** los IDs: Convierte los `int` antiguos a `bigint unsigned` para que sean compatibles con las funciones nuevas (Chat, Logs, Excepciones).
- **Optimiza**: Crea índices en las columnas de `name`, `active`, `token` y `created_at` para que el sistema siga volando.
- **Migra**: Aplica cualquier cambio de tabla que hayamos programado recientemente.
- **Limpia**: Borra todos los cachés antiguos para que veas los datos frescos.

> [!WARNING]
> Este proceso borrará cualquier dato local que no esté en el dump. Asegúrate de haber guardado cambios importantes antes de ejecutarlo.
