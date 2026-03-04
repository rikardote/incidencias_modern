# 🏝️ Guía: Creación de Widgets para Dynamic Island

Esta guía contiene la **especificación técnica exacta** para que cualquier modelo de IA (ChatGPT, Claude, etc.) te genere widgets perfectos para tu sistema sin cometer errores de PHP o dimensiones.
docker exec -t modern_app npm run build
---

## 1. 🚀 PROMPT MAESTRO (Copiar y Pegar)

Copia este texto íntegramente y pégalo en el chat de la otra IA:

> "Actúa como un desarrollador experto en Tailwind CSS y Alpine.js. Necesito generar el código HTML para un widget que vive dentro de una **'Dynamic Island'** (una barra horizontal pequeña de 36px de alto).
> 
> **CONTEXTO TÉCNICO INVIOLABLE:**
> 1. **SOLO CONTENIDO INTERIOR**: El contenedor negro redondeado YÁ EXISTE. El widget NO debe definir su propio fondo (`bg-black`), ni su propia altura (`h-9`), ni su propia redondez (`rounded-full`). 
> 2. **PROHIBIDO EL SÍMBOLO '@'**: No uses `@{{` ni `{{`. PROHIBIDO el uso de cualquier carácter de escape de Blade. Todo dato dinámico debe mostrarse exclusivamente con la directiva `x-text`.
> 3. **VARIABLE DE TEXTO**: Usa exclusivamente `x-text="islandMsg"` para el contenido.
> 4. **VARIABLES DE ESTADO (Alpine)**:
>    - `islandType`: Estados ('success', 'error', 'warning', 'info'). Úsalo con `<template x-if="...">`.
>    - `progress`: Valor (0-100). Úsalo con `x-text="progress + '%' "` para mostrar el número y con `:style="'width: ' + progress + '%' "` para barras.
> 
> **DISEÑO Y DIMENSIONES:**
> 1. **BARRA HORIZONTAL**: Usa simplemente `<div class="flex items-center gap-3 w-full min-w-0 px-4 text-white">`.
> 2. **MARCA Y TEXTO**: Usa clases `nothing-font`, `uppercase`, `font-black` y `tracking-widest`. El tamaño ideal para el mensaje es **`text-[11px]`** para máxima legibilidad.
> 3. **COLORES INSTITUCIONALES**: Usa exclusivamente `text-white`, `text-green-400`, `text-red-500` o **`text-oro`**.
> 
> **TAREA:**
> Diseña un widget con el estilo: **[ESCRIBE TU IDEA AQUÍ]**. Usa iconos de FontAwesome 5."

---

## 2. 🛠️ Pasos para la Integración (Sin ayuda)

Si la IA te da el código HTML correcto, intégralo así:

1. **Génesis**: Crea el archivo en `resources/views/components/island/styles/[nombre].blade.php`.
2. **Registro**: En `resources/views/layouts/navigation.blade.php`, busca los estilos y añade:
   ```blade
   <template x-if="$store.island.activeStyle === '[nombre]'">
       <x-island.styles.[nombre] />
   </template>
   ```
3. **Validación**: En `app/Services/System/IslandWidgetService.php`, añade `'[nombre]'` al array `$validStyles`.
4. **Dashboard**: En `resources/views/livewire/system/maintenance-toggle.blade.php`, añade `'[nombre]' => 'Descripción'` al `@foreach`.

---

## ⚠️ Solución de Errores Comunes

| Si la IA hace esto... | Respóndele esto: |
| :--- | :--- |
| Usa `{{ $islandMsg }}` | "No uses PHP. Usa `x-text="islandMsg"` de Alpine.js" |
| Usa `@if($condition)` | "No uses Blade. Usa `<template x-if="condition">`" |
| Crea un cuadro grande | "Recuerda que es una barra horizontal. Usa `flex items-center` y evita `flex-col`" |
| Texto en negro | "El fondo es oscuro, cambia el texto a `text-white` o `text-green-400`" |
