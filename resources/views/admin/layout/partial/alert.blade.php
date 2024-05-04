@if(session()->has('alert'))

    @php $alert = session('alert') @endphp


    @switch($alert['type'])
        @case(\App\Http\Controllers\Admin\AdminController::MESSAGE_SUCCESS)
        <div class="alert alert-success">
            {{ $alert['msg'] }}
        </div>
        @break

        @case(\App\Http\Controllers\Admin\AdminController::MESSAGE_ERROR)
        <div class="alert alert-danger">
            {{ $alert['msg'] }}
        </div>
        @break
    @endswitch
@endif