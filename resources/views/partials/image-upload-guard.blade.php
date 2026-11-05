@php
    $postMaxBytes = (function (string $value): int {
        $number = (float) $value;

        return (int) match (strtolower(substr(trim($value), -1))) {
            'g' => $number * 1024 ** 3,
            'm' => $number * 1024 ** 2,
            'k' => $number * 1024,
            default => $number,
        };
    })((string) ini_get('post_max_size'));
@endphp
<script>
    // Image uploads: show the limits under each image field and stop oversized
    // files before they are sent (the server validates again on save).
    (() => {
        const maxBytes = {{ \App\Support\ImageUpload::MAX_KB * 1024 }};
        const formBytes = {{ $postMaxBytes ?: 0 }};
        const maxMb = maxBytes / 1048576;
        const types = /^image\/(jpeg|png|webp|gif|bmp|avif)$/;
        const isImageInput = el => el instanceof HTMLInputElement && el.type === 'file' && (el.accept || '').includes('image');
        const mb = bytes => (bytes / 1048576).toFixed(1);

        const note = input => {
            let el = input.nextElementSibling;
            if (!el || !el.classList.contains('upload-note')) {
                el = document.createElement('small');
                el.className = 'upload-note';
                el.style.cssText = 'display:block;margin-top:.35rem;font-size:.74rem;line-height:1.4;opacity:.75';
                input.after(el);
            }
            return el;
        };

        const setNote = (input, text, isError) => {
            const el = note(input);
            el.textContent = text;
            el.style.color = isError ? '#c0392b' : '';
            el.style.opacity = isError ? '1' : '.75';
        };

        const defaultText = `JPG, PNG, WebP, GIF, BMP or AVIF · up to ${maxMb} MB · saved as WebP automatically`;

        document.querySelectorAll('input[type="file"]').forEach(input => {
            if (isImageInput(input)) setNote(input, defaultText, false);
        });

        document.addEventListener('change', e => {
            const input = e.target;
            if (!isImageInput(input)) return;

            const file = input.files && input.files[0];
            if (!file) return setNote(input, defaultText, false);

            if (file.type && !types.test(file.type)) {
                input.value = '';
                return setNote(input, `“${file.name}” is not a supported image. Use JPG, PNG, WebP, GIF, BMP or AVIF.`, true);
            }

            if (file.size > maxBytes) {
                input.value = '';
                return setNote(input, `“${file.name}” is ${mb(file.size)} MB. Images must be ${maxMb} MB or smaller — please compress or resize it.`, true);
            }

            setNote(input, `${file.name} · ${mb(file.size)} MB · will be saved as WebP`, false);
        });

        document.addEventListener('submit', e => {
            if (!formBytes) return;
            const total = [...e.target.querySelectorAll('input[type="file"]')]
                .reduce((sum, input) => sum + [...(input.files || [])].reduce((s, f) => s + f.size, 0), 0);

            if (total > formBytes * 0.95) {
                e.preventDefault();
                alert(`The selected files add up to ${mb(total)} MB, but at most ${mb(formBytes)} MB can be uploaded at once. Save fewer images at a time.`);
            }
        });
    })();
</script>
