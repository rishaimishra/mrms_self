@extends('admin.layout.main')

@section('content')

    <div class="block-header">
        <h2>Tax Payers</h2>
    </div>

    {!! $grid->generate() !!}
@endsection
