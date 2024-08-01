<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.payments.pos-recipt-head')

</head>

<body>
  

    <div class="wrapper">
      {{-- <img src="{{ asset('images/logo23.png') }}" class="watermark" alt="Watermark"> --}}
        <div class="d-flex row">
            <div class="col-3">
                <img class="t-log" style="max-width:140%;height: auto;" src="{{ asset('images/logo23.png') }}"
                    alt="logo 1" />
            </div>
            <div class="col-6">
                <div class="h4">MUNICIPAL RATE MANAGEMENT SYSTEM</div>
                <div class="h5">PROPERTY RATE PAYMENT TRANSACTION RECEIPT</div>
                <div class="h6">{{ $property->district  }}</div>
            </div>
            <div class="col-3">
                {{--  <img class="t-logo" src="{{ asset('images/logo23.png') }}" alt="Image" />  --}}
                <img class="t-logo" src="{{ $district->getPrimaryLogoUrl() }}" alt="Image" />
                {{-- <img class="t-logo" src="./logo1.png" alt="logo 1" /> --}}
            </div>
        </div>

        <table>
            <tbody>
                <tr>
                    <td>
                        <div class="blue-bg">RECEIPT NUMBER:</div>
                    </td>
                    <td class="text-grey">{{ sprintf('%007d', $payment->id) }} </td>
                    <td>
                        <div class="blue-bg  bb-date">DATE:</div>
                    </td>
                    <td class="text-grey">{{ @$payment->created_at->toDayDateTimeString() }}</td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td>Property ID:</td>
                    <td colspan="3">{{ @$property->getPrintableId() }}</td>
                </tr>
                <tr>
                    <td>Landlord Owner Name:</td>
                    <td colspan="3">
                        {{ $property->is_organization ? $property->organization_name : $property->landlord->first_name . ' ' . $property->landlord->middle_name . ' ' . $property->landlord->surname }}
                    </td>
                </tr>
                <tr>
                    <td>Payment Category:</td>
                    <td colspan="3">PROPERTY RATE</td>
                </tr>
                <tr>
                    <td>Transaction Status:</td>
                    <td colspan="3">{{ $payment->is_complete ? 'success' : 'pending' }}</td>
                </tr>
                <tr>
                    <td>Rate Payable 2024:</td>
                    <td colspan="3">{{ number_format((float) $property->assessment->getPropertyTaxPayable(), 2, '.', ',') }}</td>

                </tr>

                @php
                    $disability_discount = 0;
                    $pensioner_discount = 0;

                    $property_tax_payable = (float) $property->assessment->getPropertyTaxPayable();
                    if (@$property->assessment->pensioner_discount == 1) {
                        $pensioner_discount = $property_tax_payable * (10 / 100);
                    }
                    if (@$property->assessment->disability_discount == 1) {
                        $disability_discount = $property_tax_payable * (10 / 100);
                    }

                    // dd($pensioner_discount, $disability_discount);

                    $discounted_rate_payable =
                        $property->assessment->getPropertyTaxPayable() - $pensioner_discount - $disability_discount;
                    if ($property->assessment->pensioner_discount && $property->assessment->disability_discount) {
                        $discounted_value = $property_tax_payable * (20 / 100);
                        $pensioner_discount = $property_tax_payable * (10 / 100);
                        $disability_discount = $property_tax_payable * (10 / 100);
                    } elseif (
                        $property->assessment->pensioner_discount &&
                        $property->assessment->disability_discount != 1
                    ) {
                        $discounted_value = $property_tax_payable * (10 / 100);
                        $pensioner_discount = $property_tax_payable * (10 / 100);
                    } elseif (
                        $property->assessment->pensioner_discount != 1 &&
                        $property->assessment->disability_discount
                    ) {
                        $discounted_value = $property_tax_payable * (10 / 100);
                        $disability_discount = $property_tax_payable * (10 / 100);
                    } else {
                        $discounted_value = 0;
                    }
                @endphp

                <tr>
                    <td>Discount(s) Applicable:</td>
                    <td colspan="3">NLe {{ number_format((float) $discounted_value , 2, '.', ',') }}</td>
                </tr>
                <tr>

                    <td class="wide-column">Discounted Rate Payable:</td>
                    <td colspan="3">{{ number_format((float) $discounted_rate_payable , 2, '.', ',') }}</td>
                </tr>
                <tr>

                    <td>Arrears:</td>
                    <td colspan="3">{{ number_format((float) $property->assessment->getArrearDueAttribute(), 2, '.', ',') }}</td>
                </tr>
                <tr>

                    <td>Penalty:</td>
                    <td colspan="3">{{ number_format((float) $property->assessment->getPenaltyAttribute(), 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td>Amount Due 2024:</td>
                    <td colspan="3">NLe {{ $payment->totalAssessment() }}</td>
                </tr>
                <tr>
                    <td>Amount Paid:</td>
                    <td colspan="3">NLe {{ $payment->amountPaid() }}</td>
                </tr>
                <tr>
                    <td>Balance Due:</td>
                    <td colspan="3">NLe {{ number_format((float) $property->assessment->getCurrentYearTotalDue(), 2, '.', ',') }}
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</body>

</html>
