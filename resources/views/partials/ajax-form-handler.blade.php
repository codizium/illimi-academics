<script>
    (function($) {
        if (!$ || window.bindAcademicAjaxForm) {
            return;
        }

        const toArray = (value) => Array.isArray(value) ? value : [value];

        const serializeForm = (form) => {
            const payload = {};

            form.serializeArray().forEach(({
                name,
                value
            }) => {
                if (Object.prototype.hasOwnProperty.call(payload, name)) {
                    payload[name] = [].concat(payload[name], value);
                    return;
                }

                payload[name] = value;
            });

            return payload;
        };

        const renderErrors = (errors) => {
            const items = toArray(errors)
                .filter(Boolean)
                .map((error) => `<li>${$('<div>').text(error).html()}</li>`)
                .join('');

            return `<ul class="text-start mb-0 ps-16">${items}</ul>`;
        };

        const extractErrorHtml = (xhr) => {
            const response = xhr?.responseJSON || {};

            if (xhr?.status === 422 && response.errors) {
                const errors = Object.values(response.errors).flat();
                if (errors.length) {
                    return renderErrors(errors);
                }
            }

            return $('<div>').text(response.message || 'Unable to process your request. Please try again.')
                .html();
        };

        window.bindAcademicAjaxForm = function(config) {
            const form = $(config.formSelector);

            if (!form.length) {
                return;
            }

            const submitButton = form.find('[type="submit"]').first();

            form.on('submit', function(event) {
                event.preventDefault();

                let payload;
                let url;
                let method;

                try {
                    payload = typeof config.buildPayload === 'function' ?
                        config.buildPayload(form, serializeForm) :
                        serializeForm(form);

                    url = typeof config.url === 'function' ? config.url(form, payload) : (config.url ||
                        form.attr('action'));
                    method = (typeof config.method === 'function' ? config.method(form, payload) :
                        config.method || form.attr('method') || 'POST').toUpperCase();
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: config.validationTitle || 'Check the form',
                        html: $('<div>').text(error.message ||
                            'Please review the form and try again.').html()
                    });
                    return;
                }

                if (!url) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing endpoint',
                        text: 'Unable to determine where to submit this form.'
                    });
                    return;
                }

                const isFormData = window.FormData && payload instanceof FormData;

                submitButton.prop('disabled', true);

                Swal.fire({
                    title: config.loadingText || 'Saving...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                        url,
                        method,
                        data: payload,
                        processData: isFormData ? false : true,
                        contentType: isFormData ? false :
                            'application/x-www-form-urlencoded; charset=UTF-8',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .done((response) => {
                        const message = response?.message || config.successMessage ||
                            'Saved successfully.';

                        Swal.fire({
                            icon: 'success',
                            title: config.successTitle || 'Success',
                            text: message
                        }).then(() => {
                            if (typeof config.onSuccess === 'function') {
                                const callbackResult = config.onSuccess(response, form);
                                if (callbackResult === false) {
                                    return;
                                }
                            }

                            if (config.redirectUrl) {
                                window.location.href = config.redirectUrl;
                                return;
                            }

                            if (config.reloadOnSuccess) {
                                window.location.reload();
                                return;
                            }

                            if (config.resetOnSuccess) {
                                form[0].reset();
                            }
                        });
                    })
                    .fail((xhr) => {
                        Swal.fire({
                            icon: 'error',
                            title: config.errorTitle || 'Request failed',
                            html: extractErrorHtml(xhr)
                        });
                    })
                    .always(() => {
                        submitButton.prop('disabled', false);
                    });
            });
        };
    })(window.jQuery);
</script>
