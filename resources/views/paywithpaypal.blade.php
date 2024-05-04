<html>
<head>

     <link href="{{ url('admin/css/w3.css') }}" rel="stylesheet">
</head>
<body>
    <div class="w3-container">
        @if ($message = Session::get('success'))
        <div class="w3-panel w3-green w3-display-container">
            <span onclick="this.parentElement.style.display='none'"
    				class="w3-button w3-green w3-large w3-display-topright">&times;</span>
            <p>{!! $message !!}</p>
        </div>
        <?php Session::forget('success');?>
        @endif

        @if ($message = Session::get('error'))
        <div class="w3-panel w3-red w3-display-container">
            <span onclick="this.parentElement.style.display='none'"
    				class="w3-button w3-red w3-large w3-display-topright">&times;</span>
            <p>{!! $message !!}</p>
        </div>
        <?php Session::forget('error');?>
        @endif

    	<form class="w3-container w3-display-middle w3-card-4 w3-padding-16" method="POST" id="payment-form"
          action="{!! URL::to('paypal') !!}" >
    	  <div class="w3-container w3-teal w3-padding-16">Paywith Paypal</div>
    	  {{ csrf_field() }}
    	  <h2 class="w3-text-blue">Payment Form</h2>
    	  <p>Demo PayPal form - Integrating paypal in laravel</p>
    	  <label class="w3-text-blue"><b>Amount IN USD</b></label>
          <input class="w3-input w3-border" id="amount" type="text" name="amount" value="{{ request()->amount }}" readonly>
          <label class="w3-text-blue"><b>Amount IN Le</b></label>
          <input class="w3-input w3-border" id="amount" type="text" name="amount_in_le" value="{{  request()->amount * ($optionGroup->{\App\Logic\SystemConfig::CURRENCY_RATE}) }}" readonly>
          <label class="w3-text-blue"><b>Property Id</b></label>
          <input class="w3-input w3-border" id="amount" type="text" name="property_id" value="{{ request()->property_id }}" readonly>
          <label class="w3-text-blue"><b>Payee Name</b></label>
          <input class="w3-input w3-border" id="amount" type="text" name="payee_name" value="{{ request()->payee_name }}" readonly></p>
          <label class="w3-text-blue"><b>Mobile Number</b></label>
          <input class="w3-input w3-border" id="amount" type="text" name="mobile_number" value="{{ request()->mobile_number }}" readonly></p>
    	  <button class="w3-btn w3-blue">Pay with PayPal</button>
    	</form>
    </div>
</body>
</html>
