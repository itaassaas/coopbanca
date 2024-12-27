@extends('layouts.admin')
<!-- Primero, agregar SweetAlert2 CDN en el head o antes de cerrar body -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


@section('content')


    <div class="card">
        <div class="d-sm-flex align-items-center justify-content-between py-3">
        <h5 class=" mb-0 text-gray-800 pl-3">{{ __('Withdraw Request') }}</h5>
        <ol class="breadcrumb py-0 m-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>

            <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}">{{ __('Withdraw Request') }}</a></li>
        </ol>
        </div>
    </div>


    <!-- Row -->
    <div class="row mt-3">
      <!-- Datatables -->
      <div class="col-lg-12">

        @include('includes.admin.form-success')

        <div class="card mb-4">
          <div class="table-responsive p-3">
            <table id="geniustable" class="table table-hover dt-responsive" cellspacing="0" width="100%">
              <thead class="thead-light">
                <tr>
                    <th>{{ __("Email") }}</th>
                    <th>{{ __("Phone") }}</th>
                    <th>{{ __("Amount") }}</th>
                    <th>{{ __("Method") }}</th>
                    <th>{{ __("Withdraw Date") }}</th>
                    <th>{{ __("Status") }}</th>
                    <th>{{ __("Actions") }}</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
      <!-- DataTable with Hover -->

    </div>
    <!--Row-->

<div class="modal fade confirm-modal" id="details" tabindex="-1" role="dialog" aria-labelledby="statusModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __("Withdraw Request Details") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
            <a href="javascript:;" class="btn btn-secondary" data-dismiss="modal">{{ __("Back") }}</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade confirm-modal" id="status-modal" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-block text-center">
                <h4 class="modal-title d-inline-block">{{ __("Accpet Withdraw") }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-center">{{ __("You are about to accept this Withdraw.") }}</p>
                <p class="text-center">{{ __("Do you want to proceed?") }}</p>

                

            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __("Cancel") }}</button>
                <a class="btn btn-success btn-ok">{{ __("Accept") }}</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade status-modal" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header d-block text-center">
                <h4 class="modal-title d-inline-block">{{ __("Reject Withdraw") }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-center">{{ __("You are about to reject this Withdraw.") }}</p>
                <p class="text-center">{{ __("Do you want to proceed?") }}</p>

                <div class="form-group mt-3">
                    <label for="motivo_rechazo" class="form-label required">{{ __("Motivo del Rechazo") }}</label>
                    <textarea 
                        name="motivo_rechazo" 
                        id="motivo_rechazo" 
                        class="form-control" 
                        rows="3" 
                        required
                        placeholder="Explique el motivo del rechazo..."
                    ></textarea>
                    <small class="text-muted">{{ __("Este mensaje será visible para el usuario") }}</small>
                </div>

                <style>
                .required:after {
                    content: ' *';
                    color: red;
                }
                </style>
            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __("Cancel") }}</button>
                <a class="btn btn-danger btn-ok">{{ __("Reject") }}</a>
            </div>

        </div>
    </div>
</div>

@endsection


@section('scripts')

<script type="text/javascript">
"use strict";
var table = $('#geniustable').DataTable({
			   ordering: false,
               processing: true,
               serverSide: true,
               searching: true,
               ajax: '{{ route('admin.withdraw.datatables') }}',
               columns: [
                        { data: 'email', name: 'email' },
                        {data:'phone',name: 'phone'},
                        {data:'amount',name:'amount'},
                        {data:'method',name:'method'},
                        {data: 'created_at',name:'created_at'},
                        { data: 'status',searchable: false, orderable: false},
            			{ data: 'action', searchable: false, orderable: false }
                     ],
                language : {
                    processing: '<img src="{{asset('assets/images/'.$gs->admin_loader)}}">'
                }
            });

            $(document).on('click', '#applicationDetails', function () {
      let detailsUrl = $(this).data('href');
      $.get(detailsUrl, function( data ) {
        $( "#details .modal-body" ).html( data );
      });
    })


abstract
</script>



<!-- Remove or comment out the 'abstract' line -->
// Replace AJAX call with:
<script>
$(document).ready(function() {
    $('#confirm-delete').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('data-href', $(e.relatedTarget).data('href'));
    });

    $('.btn-ok').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var url = $this.data('href');
        var motivo = $('#motivo_rechazo').val();

        if (!motivo) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe especificar un motivo de rechazo'
            });
            return;
        }

        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                motivo_rechazo: motivo
            }),
            success: function(response) {
                $('#confirm-delete').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Retiro rechazado exitosamente'
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                let message = 'Error del servidor';
                try {
                    const response = JSON.parse(xhr.responseText);
                    message = response.message || message;
                } catch(e) {}
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            }
        });
    });
});
</script>
@endsection

