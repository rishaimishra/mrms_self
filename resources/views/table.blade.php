<!DOCTYPE html>
<html>
<head>
    <title>Products List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Products List</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>propertyID</th>
                <th>url</th>
               
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
            @if ($product->getImageAnyUrl() !== null && !str_contains($product->getImageAnyUrl(), 'council_logo-image(100x100-crop)'))

                <tr>
                    <td>{{ $product->property_id }}</td>
                        
                    {{-- <td>{{ $product->getImageAnyUrl() }}</td> --}}
                    <td align="right" style="width: 18%;">
                        {{--<img style="padding: 0 15px;" src="{{ asset('images/logo2.jpg') }}" alt="">--}}
                        <img style="padding: 0 15px;" src="{{ $product->getImageAnyUrl(85,85,true) }}" alt="">
                    </td>
                </tr>
                @endif

            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {!! $products->links() !!}
    </div>
</div>
</body>
</html>
