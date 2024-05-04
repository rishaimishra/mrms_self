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
