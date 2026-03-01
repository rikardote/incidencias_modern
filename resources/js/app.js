import './bootstrap';

import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es.js';
import TomSelect from 'tom-select';
import Swal from 'sweetalert2';

window.flatpickr = flatpickr;
window.TomSelect = TomSelect;
window.Swal = Swal;

// Toast mixin config
window.Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

flatpickr.localize(Spanish);
