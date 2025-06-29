document.addEventListener('click', (e) => {
  if (e.target.classList.contains('deleteImage')) {
    const btn = e.target;
    const imageId = btn.dataset.imageid;
    const ajaxDeleteImage = btn.dataset.ajaxdeleteimage;

    if (!imageId) {
      console.warn('No se encontró ID de la imagen');
      return;
    }

    if (!confirm('¿Quieres eliminar esta imagen?')) {
      return;
    }

    fetch(ajaxDeleteImage, {
      method: 'DELETE',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    })
    .then(res => res.json())
    .then(data => {

      if (data.success) {
        const container = document.getElementById(imageId);
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
