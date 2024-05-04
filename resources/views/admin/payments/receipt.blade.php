<!doctype html>
<html lang="en">
<head>
    @include('admin.payments.print-head')
</head>
<body>

{{--<div class="print_row">--}}
{{--    <a onclick="myFunction()">Print this page</a>--}}
{{--</div>--}}
<div class="page">
    @include('admin.payments.receipt-content', ['property' => $property, 'paymentInQuarter' => $paymentInQuarter,'district' => $district])
    @include('admin.payments.receipt-account-details', ['property' => $property, 'paymentInQuarter' => $paymentInQuarter,'district' => $district])
</div>

<div class="pagebreak"></div>

<div class="page">
    @include('admin.payments.receipt-policy',['district' => $district])
</div>
{{--</div>--}}


{{--<script>--}}
{{--    function myFunction() {--}}
{{--        window.print();--}}
{{--    }--}}
{{--</script>--}}

{{--<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>--}}
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>--}}
{{--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>--}}
</body>
</html>
