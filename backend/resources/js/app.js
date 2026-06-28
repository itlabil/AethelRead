import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

// Image Upload Component
window.imageUpload = function () {
    return {
        preview: null,
        fileName: '',
        isDragging: false,

        handleFile(event) {
            const file = event.target.files[0];
            if (file) this.processFile(file);
        },

        handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            if (file) {
                // Set file ke input
                const input = this.$el.querySelector('input[type="file"]');
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                this.processFile(file);
            }
        },

        processFile(file) {
            // Validate size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Image size must not exceed 2MB.',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: { popup: '!rounded-2xl !text-sm' },
                });
                return;
            }

            // Validate type
            const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!allowed.includes(file.type)) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Invalid file type. Use JPEG, PNG, or WEBP.',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: { popup: '!rounded-2xl !text-sm' },
                });
                return;
            }

            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.preview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        clearPreview() {
            this.preview = null;
            this.fileName = '';
            const input = this.$el.querySelector('input[type="file"]');
            if (input) input.value = '';
        },
    };
};

// Global SweetAlert Delete Confirmation
window.confirmDelete = function (form) {
    // Simpan referensi form sebelum masuk callback async
    const targetForm = form;

    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#9333ea',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: '!rounded-2xl',
            confirmButton: '!rounded-lg !font-medium !text-sm',
            cancelButton: '!rounded-lg !font-medium !text-sm',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            targetForm.submit();
        }
    });
};

Alpine.start();