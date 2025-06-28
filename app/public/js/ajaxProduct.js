
document.querySelector('#btn-add-edition').addEventListener('click', function () {
    const ajaxAddEdition = this.dataset.ajaxaddedition;

    fetch(ajaxAddEdition)
        .then(response => response.text())
        .then(html => {
            // Inyecta el HTML en el body del modal
            document.querySelector('#modal-edition-body').innerHTML = html;

            // Abre el modal
            const modalElement = document.getElementById('modalAddEdiction');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        });
});

// Solo inicializa Select2 cuando el modal está completamente visible
$(document).on('shown.bs.modal', '#modalAddEdiction', function () {
    console.log('Modal abierto: inicializando select2');

    $(this).find('select.select2, select.select2label').each(function () {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                width: '100%',
                placeholder: 'Selecciona una opción',
                allowClear: true,
                minimumResultsForSearch: 0,
                matcher: function (params, data) {
                    if ($.trim(params.term) === '') return data;
                    if (typeof data.text === 'undefined') return null;
                    if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) return data;
                    return null;
                }
            });
        }
    });
});