<!doctype html>
<html lang="en">
<head>
    @include('admin.payments.print-head')
</head>
<body>

<div class="page" style="width:100%;">
    @foreach($properties as $property)

        @php
        $property->assessment->setPrinted();
        $district = App\Models\District::where('name', $property->district)->first();
        @endphp

        @include('admin.payments.receipt-content', ['property' => $property, 'assessment' => $property->assessment, 'paymentInQuarter' => $property->getPaymentsInQuarter($year), 'year' => $year,'district'=>$district])

        @include('admin.payments.receipt-account-details', ['property' => $property,  'assessment' => $property->assessment, 'paymentInQuarter' => $property->getPaymentsInQuarter($year), 'year' => $year,'district'=>$district])

        <div class="page-break"></div>
        @include('admin.payments.receipt-policy',['property' => $property, 'assessment' => $property->assessment,'district'=>$district, 'year' => $year])

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</div>

</body>
</html>
