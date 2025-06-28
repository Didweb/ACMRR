//   $(document).ready(function() {
//     $('.select2').select2({
//       width: '100%',
//       theme: 'bootstrap-5', 
//       placeholder: 'Selecciona una opción',
//       allowClear: true
//     });
//   });

//   $('.select2').select2({
//     width: '100%',
//     placeholder: 'Selecciona una opción',
//     allowClear: true,
//     matcher: function(params, data) {
//         if ($.trim(params.term) === '') {
//             return data;
//         }

//         if (typeof data.text === 'undefined') {
//             return null;
//         }

//         // Forzar búsqueda básica sin acentos, etc.
//         if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
//             return data;
//         }

//         return null;
//     }
// });

// $('.select2label').select2({
//                     width: '100%',
//                     placeholder: 'Selecciona una opción',
//                     allowClear: true,
//                     matcher: function(params, data) {
//                         if ($.trim(params.term) === '') {
//                             return data;
//                         }

//                         if (typeof data.text === 'undefined') {
//                             return null;
//                         }

//                         if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
//                             return data;
//                         }

//                         return null;
//                     }
//                 });

