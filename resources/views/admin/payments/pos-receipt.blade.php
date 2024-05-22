
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>

  <style>
    html {
}

body {
  font-family: sans-serif;
}

* {
  margin: 0;
  padding: 0;
}

.wrapper {
  width: 840px;
  height: 1188px;
  padding: 20px 20px 20px;
  position: relative;
  /* border: 2px solid red; */
}
.wrapper:before {
  content: "";
  display: block;
  background: url(./logo1.png);
  background-size: 70%;
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  background-repeat: no-repeat;
  background-position: 50% 48%;
  z-index: -1;
  opacity: 20%;
}
.d-flex {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  align-items: center;
  padding: 0 0 20px;
  margin: 0 0 40px;
  border-bottom: 1px solid #c4c4c4;
}

.col-3 {
  width: 15%;
}

.col-6 {
  width: 70%;
  text-align: center;
}

.h4 {
  font-size: 22px;
  font-weight: 900;
  margin: 0 0 10px;
}

.h5 {
  font-size: 16px;
  font-weight: 700;
  color: #777;
  margin: 0 0 6px;
}

.h6 {
  font-size: 16px;
  font-weight: 700;
  color: #777;
}

.t-logo {
  max-width: 100%;
  height: auto;
  /* width: 140px; */
}

table {
  font-size: 17px;
  font-family: serif;
  width: 90%;
  margin: auto;
}

td {
  padding: 10px 10px 7px;
  font-weight: 600;
  color: #626262;
}

.blue-bg {
  background: #116cca;
  color: #fff;
  padding: 7px 10px 7px;
}

table tr td:first-child {
  width: 220px;
}
.text-grey {
  color: grey;
  padding: 0 0 0 20px;
}
.bb-date {
  margin: 0 0 0 150px;
}


@media only screen and (max-width:600px){.wrapper{width:auto;height:auto}.d-flex{}.h4{font-size:12px}.h5{font-size:10px}.h6{font-size:10px}.bb-date{margin:0}td{font-size:8px;padding:5px 4px 5px}table tr td:first-child{width:100px}table{width:100%}}

  </style>
</head>

<body>
  <div class="wrapper">
    <div class="d-flex row">
      <div class="col-3">
        <img class="t-logo" src="./logo1.png" alt="logo 1" />
      </div>
      <div class="col-6">
        <div class="h4">MUNICIPAL RATE MANAGEMENT SYSTEM</div>
        <div class="h5">PROPERTY RATE PAYMENT TRANSACTION RECEIPT</div>
        <div class="h6">{{$property->district}}</div>
      </div>
      <div class="col-3">
        <img class="t-logo" src="./logo1.png" alt="logo 1" />
      </div>
    </div>

    <table>
      <tbody>
        <tr>
          <td>
            <div class="blue-bg">RECEIPT NUMBER:</div>
          </td>
          <td class="text-grey">{{sprintf("%007d", $payment->id)}} </td>
          <td>
            <div class="blue-bg  bb-date">DATE:</div>
          </td>
          <td class="text-grey">{{ $payment->created_at->toDayDateTimeString() }}</td>
        </tr>
        <tr><td></td></tr>
        <tr>
          <td>Property ID:</td>
          <td colspan="3">{{$property->getPrintableId()}}</td>
        </tr>
        <tr>
          <td>Landlord Owner Name:</td>
          <td colspan="3">{{$property->is_organization ?  $property->organization_name : $property->landlord->first_name.' '.$property->landlord->middle_name.' '.$property->landlord->surname}}</td>
        </tr>
        <tr>
          <td>Payment Category:</td>
          <td colspan="3">PROPERTY RATE</td>
        </tr>
        <tr>
          <td>Transaction Status:</td>
          <td colspan="3">{{$payment->is_complete ? 'success' : 'pending' }}</td>
        </tr>
        <tr>
          <td>Rate Payable 2024:</td>
          <td colspan="3">{{number_format((float)$property->assessment->getPropertyTaxPayable(),2,'.','')}}</td>
     
        </tr>

        @php
        $property_tax_payable = (float)$property->assessment->getPropertyTaxPayable();
        if ($property->assessment->pensioner_discount == 1) {
            $pensioner_discount = $property_tax_payable * (10/100);
        }
        if ($property->assessment->disability_discount == 1) {
            $disability_discount = $property_tax_payable * (10/100);
        }

       
        // dd($pensioner_discount, $disability_discount);

        $discounted_rate_payable = ($property->assessment->getPropertyTaxPayable()) - $pensioner_discount - $disability_discount;
        if($property->assessment->pensioner_discount && $property->assessment->disability_discount)
                {
                    $discounted_value = $property_tax_payable * ((20)/100);
                    $pensioner_discount = $property_tax_payable * (10/100);
                    $disability_discount = $property_tax_payable * (10/100);
                }else if( $property->assessment->pensioner_discount && $property->assessment->disability_discount != 1)
                {
                    $discounted_value = $property_tax_payable * ((10)/100);
                    $pensioner_discount = $property_tax_payable * (10/100);

                }else if ($property->assessment->pensioner_discount != 1 && $property->assessment->disability_discount)
                {
                    $discounted_value = $property_tax_payable * ((10)/100);   
                    $disability_discount = $property_tax_payable * (10/100);
                }else
                {
                    $discounted_value = 0; 
                }
        @endphp

        <tr>
          <td>Discount(s) Applicable:</td>
          <td colspan="3">NLe {{number_format((float)$discounted_value,2,'.','')}}</td>
        </tr>
        <tr>
           
          <td>Discounted Rate Payable:</td>
          <td colspan="3">{{number_format((float)$discounted_rate_payable,2,'.','')}}</td>
        </tr>
        <tr>
          <td>Amount Due 2024:</td>
          <td colspan="3">NLe {{$payment->totalAssessment()}}</td>
        </tr>
        <tr>
          <td>Amount Paid:</td>
          <td colspan="3">NLe {{($payment->amountPaid())}}</td>
        </tr>
        <tr>
          <td>Balance Due:</td>
          <td colspan="3">NLe {{ number_format(max($payment->balance, 0)) }}</td>
        </tr>

      </tbody>
    </table>
  </div>
</body>

</html>