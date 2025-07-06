
document.querySelector('#btn-add-edition').addEventListener('click', function () {
    const ajaxAddEdition = this.dataset.ajaxaddedition;

    fetch(ajaxAddEdition)
        .then(response => response.text())
        .then(html => {
    
            document.querySelector('#modal-edition-body').innerHTML = html;

            const modalElement = document.getElementById('modalAddEdiction');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        });
});


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

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('input[type="file"][data-ajaxaddimage]').forEach(input => {
    input.addEventListener('change', (event) => {
      handleImageChange(event);
    });
  });
});


function handleImageChange(event) {
    const input = event.target;
    const file = input.files[0];
    if (!file) return;

    const entityName = input.dataset.entity;
    const entityId = input.dataset.entityid;
    const ajaxAddImage = input.dataset.ajaxaddimage;

    const formData = new FormData();
    formData.append('image', file);
    formData.append('entityName', entityName);
    formData.append('entityId', entityId);

    fetch(ajaxAddImage, {
        method: 'POST',
        body: formData,
        headers: {
        'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
        console.log('Imagen subida correctamente');
        const images = data.data.all_images;

        const container = document.querySelector(`.showImages${entityId}`);

        container.innerHTML = '';

        images.forEach(img => {
            const imgElem = document.createElement('img');
            imgElem.src = img.url;
            imgElem.alt = img.filename;
            imgElem.style.maxWidth = '150px';
            imgElem.style.margin = '5px';
            container.appendChild(imgElem);
        });
        } else {
        console.warn('Error al subir:', data.message);
        }
    })
    .catch(err => {
        console.error('Error AJAX', err);
    });
}


document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.open-edition-modal').forEach(button => {
    button.addEventListener('click', function (e) {
      e.preventDefault(); 
      const url = this.dataset.url;

      fetch(url)
        .then(res => res.text())
        .then(html => {
          document.querySelector('#modalEditionFormContent').innerHTML = html;

          const modal = new bootstrap.Modal(document.getElementById('modalEditionForm'));
          modal.show();
        })
        .catch(err => {
          console.error('Error al cargar el formulario:', err);
        });
    });
  });
});


document.addEventListener('click', (e) => {
  if (e.target.classList.contains('deleteProductEdition')) {
    const btn = e.target;
    const ajaxDeleteProductEdition = btn.dataset.ajaxdeleteproductedition;
    const productEditionId = btn.dataset.producteditionid;

    if (!confirm('¿Quieres eliminar esta edición?')) {
      return;
    }

    fetch(ajaxDeleteProductEdition, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    })
    .then(res => res.json())
    .then(data => {

      if (data.success) {
        const container = document.getElementById('block-'+data.data.id);
        if (container) {
            container.remove();
        }

      } else {
        alert('Error al eliminar imagen: ' + (data.message || 'Error desconocido'));
      }
    })
    .catch(err => {
      console.error('Error en la petición de eliminar imagen:', err);
      alert('Error en la petición al servidor.');
    });
  }
});