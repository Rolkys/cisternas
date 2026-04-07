/*
 DOC: Proyecto Cisternas
 Archivo personalizado para comportamiento/estilos especificos de la app.
*/
/* ============================================================
Control de Cisternas - JavaScript personalizado
Archivo: public/js/app-custom.js
============================================================ */

/* Modal de consumo (cisterna/index) */
/**
 * Abre el modal de consumo y prepara sus campos para una cisterna concreta.
 * @param {number|string} id Identificador de la cisterna.
 * @param {string|null} hecL1 Hora estimada de consumo linea 1.
 * @param {string|null} hrcL1 Hora real de consumo linea 1.
 * @param {string|null} hecL2 Hora estimada de consumo linea 2.
 * @param {string|null} hrcL2 Hora real de consumo linea 2.
 */
function abrirModal(id, hecL1, hrcL1, hecL2, hrcL2) {
    const form = document.getElementById('form-consumo');
    const l1 = document.getElementById('hrc-l1');
    const l2 = document.getElementById('hrc-l2');

    if (!form) return;

    form.action = '/cisterna/' + id + '/consumo';

    const infoL1 = document.getElementById('info-hec-l1');
    const infoL2 = document.getElementById('info-hec-l2');
    if (infoL1) infoL1.value = hecL1 || '';
    if (infoL2) infoL2.value = hecL2 || '';

    if (l1) l1.value = hrcL1 || '';
    if (l2) l2.value = hrcL2 || '';

    if (l1) l1.disabled = false;
    if (l2) l2.disabled = false;

    if (hrcL1 && l2) {
        l2.disabled = true;
    } else if (hrcL2 && l1) {
        l1.disabled = true;
    }

    const modalRoot = document.getElementById('modalConsumo');
    if (!modalRoot) return;

    const modal = new bootstrap.Modal(modalRoot);
    modal.show();
}

/**
 * Inicializa eventos globales al cargar el DOM.
 * Configura mutex de campos de hora y utilidades de interfaz.
 */
document.addEventListener('DOMContentLoaded', function () {
    /* Tema claro/oscuro */
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-toggle-icon');
    const storageKey = 'app-theme';

    function applyTheme(theme) {
        const isDark = theme === 'dark';
        document.body.classList.toggle('dark-mode', isDark);

        if (themeIcon) {
            themeIcon.classList.remove('bi-moon-stars-fill', 'bi-sun-fill');
            themeIcon.classList.add(isDark ? 'bi-sun-fill' : 'bi-moon-stars-fill');
        }

        if (themeToggle) {
            themeToggle.setAttribute(
                'aria-label',
                isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'
            );
            themeToggle.setAttribute(
                'title',
                isDark ? 'Modo oscuro activo' : 'Modo claro activo'
            );
        }
    }

    const savedTheme = localStorage.getItem(storageKey);
    applyTheme(savedTheme === 'dark' ? 'dark' : 'light');

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const isDark = document.body.classList.contains('dark-mode');
            const nextTheme = isDark ? 'light' : 'dark';
            localStorage.setItem(storageKey, nextTheme);
            applyTheme(nextTheme);
        });
    }

    const l1 = document.getElementById('hrc-l1');
    const l2 = document.getElementById('hrc-l2');

    if (l1 && l2) {
        l1.addEventListener('input', function () {
            if (this.value) {
                l2.value = '';
                l2.disabled = true;
            } else {
                l2.disabled = false;
            }
        });

        l2.addEventListener('input', function () {
            if (this.value) {
                l1.value = '';
                l1.disabled = true;
            } else {
                l1.disabled = false;
            }
        });
    }

    /* Toggle visibilidad contrasenas (login) */
    window.togglePassword = function () {
        const input = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            if (icon) icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    };

    /* Generador de contrasena (admin/users) */
    window.generarPassword = function () {
        const emailEl = document.getElementById('email');
        const passEl = document.getElementById('password_generada');
        const btnEl = document.getElementById('btn-crear');
        if (!emailEl || !passEl) return;

        const parte = emailEl.value.split('@')[0].toUpperCase();
        if (!parte) {
            alert('Introduce primero el email.');
            return;
        }

        const first = parte.charCodeAt(0);
        const last = parte.charCodeAt(parte.length - 1);
        const password = parte + first + last;

        passEl.value = password;
        if (btnEl) btnEl.removeAttribute('disabled');
    };

    /* Mutex H.E.C en bulk_confirm */
    document.querySelectorAll('.hec-l1').forEach(function (l1c) {
        const idx = l1c.dataset.index;
        const l2c = document.querySelector('.hec-l2[data-index="' + idx + '"]');
        if (!l2c) return;

        l1c.addEventListener('input', function () {
            if (this.value) {
                l2c.value = '';
                l2c.disabled = true;
            } else {
                l2c.disabled = false;
            }
        });

        l2c.addEventListener('input', function () {
            if (this.value) {
                l1c.value = '';
                l1c.disabled = true;
            } else {
                l1c.disabled = false;
            }
        });

        if (l1c.value) {
            l2c.disabled = true;
        } else if (l2c.value) {
            l1c.disabled = true;
        }
    });

    /* Seleccionar / deseleccionar todos (bulk_confirm) */
    window.toggleTodos = function (estado) {
        document.querySelectorAll('.check-fila').forEach(function (cb) {
            cb.checked = !!estado;
        });
    };

    /* Mutex H.E.C / H.R.C en edit.blade.php */
    function setupMutex(idA, idB) {
        const a = document.getElementById(idA);
        const b = document.getElementById(idB);
        if (!a || !b) return;

        if (a.value) {
            b.disabled = true;
        } else if (b.value) {
            a.disabled = true;
        }

        a.addEventListener('input', function () {
            if (this.value) {
                b.value = '';
                b.disabled = true;
            } else {
                b.disabled = false;
            }
        });

        b.addEventListener('input', function () {
            if (this.value) {
                a.value = '';
                a.disabled = true;
            } else {
                a.disabled = false;
            }
        });
    }

    setupMutex('HoraEstimadaConsumoL1', 'HoraEstimadaConsumoL2');
    setupMutex('HoraRealConsumoL1', 'HoraRealConsumoL2');
});
