@extends('layout')

@section('content')
    <div class="container border p-5 mt-5 mb-5">
        <div class="row">
            <div class="col text-center fs-1">
                Accesorios con AJAX
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-9 col-12 mx-auto">
                <div class="row">
                    <div class="col fs-1 text-center">
                        Crear nuevo accesorio
                    </div>
                </div>
                <form id="formAccesorio">
                    <div class="row mt-3">
                        <div class="col-lg-6 col-md-9 col-12 mx-auto">
                            <label for="nombre">Nombre del accesorio</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre">
                        </div>
                        <div class="col-lg-6 col-md-9 col-12 mx-auto">
                            <label for="imagen">Imágen</label>
                            <input type="file" id="imagen" name="imagen" class="form-control" placeholder="Imagen">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col mx-auto">
                            <button type="submit" id="btnEnviar" class="btn btn-dark w-100">Enviar</button>
                        </div>
                    </div>
                </form>  
                <div class="row mt-5">
                    <div class="col fs-1 text-center">
                        Accesorios disponibles
                    </div>
                </div>    
                <div class="row">
                    <div class="col">
                        <select name="accesorios_select" id="accesorios_select" class="form-select">
                            @foreach ($accesorios as $ac)
                                <option value="{{ $ac->id }}" data-id="{{ $ac->id }}">{{ $ac->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>          
            </div>
            <div class="col-lg-6 col-md-9 col-12 mx-auto">
                <div class="row" style="max-height: 40rem; overflow: auto;">
                    <div class="col-9 mx-auto">
                        <div class="row" id="accessory-list">
                            @foreach ($accesorios as $a)
                                <div class="col-12 contenedor-show">
                                    <div class="row mt-4 d-flex align-items-center justify-content-center">
                                        <div class="col-lg-8 col-md-8 col-12 border">
                                            {{ $a->nombre }}
                                        </div>
                                        <div class="col-lg-4 px-0 col-md-4 col-12 border">
                                            <div class="col position-relative" style="
                                                background-color: #000000;
                                                background-image: url('{{ asset('img/accesorios/'.$a->imagen) }}');
                                                background-size: contain;
                                                background-repeat: no-repeat;
                                                background-position: center center;
                                                width: 100%;
                                                height: 10rem;
                                            ">
                                                <div class="col position-absolute top-0 start-100 translate-middle">
                                                    <button type="button" class="btn btn-danger w-100 delete-btn" data-id="{{ $a->id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    
    <script>
        $(document).ready(function(){
            $('#formAccesorio').submit(function(e){
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                // Cachamos el CSRF token del layout
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                // Insertamos el token CSRF a los datos de la solicitud AJAX
                formData.append('_token', csrfToken);

                $.ajax({
                    url: "{{ route('accesorios.store') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success:function(response) {
                        // toastr.success(response.message);
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        
                        var newAccessory = `
                            <div class="col-12 contenedor-show">
                                <div class="row mt-4 d-flex align-items-center justify-content-center">
                                    <div class="col-lg-8 col-md-8 col-12 border">
                                        ${$('#nombre').val()}
                                    </div>
                                    <div class="col-lg-4 px-0 col-md-4 col-12 border">
                                        <div class="col position-relative" style="
                                            background-color: #000000;
                                            background-image: url('${URL.createObjectURL($('#imagen')[0].files[0])}');
                                            background-size: contain;
                                            background-repeat: no-repeat;
                                            background-position: center center;
                                            width: 100%;
                                            height: 10rem;
                                        ">
                                            <div class="col position-absolute top-0 start-100 translate-middle">
                                                <button type="button" class="btn btn-danger w-100 delete-btn" data-id="${response.id}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#accessory-list').append(newAccessory);
                        
                        // Actualizar el select con los accesorios actualizados
                        var select = $('#accesorios_select');
                        select.empty(); // Vaciar el select actual

                        
                        $.each(response.accesorios, function(index, accesorio) {
                            select.append($('<option>', {
                                value: accesorio.id,
                                text: accesorio.nombre
                            }));
                        });
                        
                        $('#formAccesorio')[0].reset();
                    },
                    error:function(xhr){
                        toastr.error('Error al enviar el formulario');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#accessory-list').on('click', '.delete-btn', function() {
                var button = $(this);
                var id = button.data('id');
                // __id__ es un marcador para poder pasar el id como una cadena
                var urlTemplate = "{{ route('accesorios.delete', ['accesorio' => '__id__']) }}";
                var url = urlTemplate.replace('__id__', id);
    
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        if (response.success) {
                            // toastr.success('Accesorio eliminado');
                            Swal.fire({
                                position: "top-end",
                                icon: "info",
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            button.closest('.contenedor-show').remove();
                            $('#accesorios_select option[data-id="' + id + '"]').remove();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error al eliminar el accesorio');
                    }
                });
            });
        });
    </script>    
@endsection



