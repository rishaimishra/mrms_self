

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
                        <h2>{{ __("Delivery Detail") }}</h2>
                    </div>
                </div>
            </div>

            <div class="body">
                <table class="table">
                    <thead>
                        <tr>
                        <th>Year</th>
                        <th>Demand Note Delivery</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($property->assessments as $assessment)
                        <tr>
                            <td>{{ $assessment->created_at->format('Y') }}</td>
                            <td>

                                    <p>{{ $assessment->isDelivered() ? 'Delivered' : 'Not Delivered' }}</p>

                                    @if($assessment->isDelivered())
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6>Recipient Name</h6>
                                                <p>{{ $assessment->demand_note_recipient_name }}</p>
                                            </div>
                                            <div class="col-sm-3">
                                                <h6>Recipient Contact Number</h6>
                                                <p>{{ $assessment->demand_note_recipient_mobile }}</p>
                                            </div>
                                        </div>

                                        <a href="{{ $assessment->getRecipientPhoto(600,600) }}" data-sub-html="">
                                            <img style="max-width: 100px" class="img-responsive thumbnail" src="{{ $assessment->getRecipientPhoto(600,600) }}">
                                        </a>
                                    @endif
                            </td>

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