import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start();

// Global SweetAlert config
window.confirmDelete = function(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#9333ea',
        cancelButtonColor: '#6b7280',
        borderRadius: '1rem',
        customClass: {
            popup: '!rounded-2xl',
            confirmButton: '!rounded-lg !font-medium !text-sm',
            cancelButton: '!rounded-lg !font-medium !text-sm',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
};