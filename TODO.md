# TODO: Fix Bug Minutos :04 en Horas Consumo

Estado: En progreso ✅ Plan aprobado

## Pasos a completar:

### 1. [x] Crear este TODO.md
### 2. [✅] Editar resources/views/cisterna/index.blade.php
   - Cambiar **4 data-attributes** de `format('HH:MM')` → `format('H:i')`:
     * data-hec-l1
     * data-hrc-l1  
     * data-hec-l2
     * data-hrc-l2
### 3. [ ] Probar funcionalidad:
   - Abrir tabla cisternas → click botón consumo en fila.
   - Verificar modal inputs muestran hora **correcta** (no :04).
   - Cambiar hora a 11:45 → guardar.
   - Refresh página → verificar tabla muestra 11:45.
### 4. [ ] Verificar otros views (edit.blade.php, bulk):
   - Ya usan 'H:i' correcto.
### 5. [ ] Opcional: Limpiar datos corruptos DB (si existen con min=4):
   ```sql
   UPDATE cisternas SET HoraRealConsumoL1 = NULL WHERE MINUTE(HoraRealConsumoL1) = 4;
   ```
### 6. [ ] Completar task

**Notas**:
- Bug causado por Carbon format('HH:MM') inválido en data-* → input time corrupto.
- Fix asegura 'H:i' consistente ('11:40') para browser & display.
- No afecta controller (parsea string válido).
