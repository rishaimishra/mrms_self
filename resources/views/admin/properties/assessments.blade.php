

<div id="assessment" class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        <div class="card">
            <div class="header  bg-cyan">
                <div class="row">
                    <div class="col-md-8">
                        <h2>{{ __("Assessment Details") }}</h2>
                    </div>
                </div>
            </div>

            <div class="body">
                @foreach($property->assessments as $assessment)

                    <div class="assessment-item">
                        @include('admin.properties.assessment', ['assessment' => $assessment])
                    </div>

                    @if(!$loop->last)
                        <hr/>
                    @endif

                @endforeach
            </div>
        </div>
    </div>
</div>
<div id="assessment" class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        <div class="card">
            <div class="header bg-orange">
                <div class="row">
                    <div class="col-md-8">
                        <h2>{{ __("Demand Note Details") }}</h2>
                    </div>
                </div>
            </div>

            <div class="body">
                <table class="table">
                    <thead>
                        <tr>
                        <th>Year</th>
                        <th>Delivery Status</th>
                        <th>Recipient Name</th>
                        <th>Recipient Contact Number</th>
                        <th>Proof of Delivery</th>
                        <th>Last Printed</th>
                        <th>Last Payment</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($property->assessments as $assessment)
                        <tr>
                            <td>{{ $assessment->created_at->format('Y') }}</td>
                           

                                  <td>  <p>{{ $assessment->isDelivered() ? 'Delivered' : 'Not Delivered' }}</p></td>

                                    @if($assessment->isDelivered())
                                        
                                    <td>   <p>{{ $assessment->demand_note_recipient_name }}</p> </td>
                                       
                                            <td>    <p>{{ $assessment->demand_note_recipient_mobile }}</p></td>
                                           
                                     <td>   <a href="{{ $assessment->getRecipientPhoto(600,600) }}" data-sub-html="">
                                            <img style="max-width: 100px" class="img-responsive thumbnail" src="{{ $assessment->getRecipientPhoto(600,600) }}">
                                        </a></td>
                                       
                                            <td>
                                                 {{ $assessment->isPrinted() ? $assessment->last_printed_at->toDayDateTimeString() : 'Never' }}</td>
                                            
                                                @php $lastPayment = $property->recentPayment()->whereYear('created_at', $assessment->created_at->format('Y'))->first() @endphp
                                            <td>
                                                {{ $lastPayment ? $lastPayment->created_at->toDayDateTimeString() : 'Never' }} 
                                            </td>
                                    
                                    @endif
                           

                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{--  @foreach($property->assessments as $assessment)

                    <div class="assessment-item">
                        @include('admin.properties.assessment', ['assessment' => $assessment])
                    </div>

                    @if(!$loop->last)
                        <hr/>
                    @endif

                @endforeach  --}}
            </div>
        </div>
    </div>
</div>