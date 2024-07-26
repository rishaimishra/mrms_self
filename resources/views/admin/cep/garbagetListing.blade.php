@extends('admin.layout.main')
@push('stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/leantony/grid/css/grid.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/properties_grid.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/dhtmlxcombo.css') }}"/>
@endpush
@section('title')
    {{$title}}
@stop

@section('page_title') {{$title}} @stop

@section('content')
<style type="text/css">
    /* Your existing styles */
    #search-property-grid {
        display: none;
    }
    div.laravel-grid {
        margin-top: 10px !important;
    }
    .p_2 {
        padding: 20px;
    }
    .img_pre {
        width: 100px;
        height: 100px;
        border: 1px dashed gray;
        border-radius: 10px;
        margin-top: 10px;
        text-align: center;
    }
    .border_cus {
        border: 1px solid rgb(210, 210, 210);
        padding: 10px;
    }
    #imgPreview img {
        height: 95px;
        width: 95px;
        border-radius: 10px;
    }
    .ecp_submit {
        background: #0070c0 !important;
        border-radius: 5px !important;
        font-size: 16px !important;
        color: white !important;
        font-weight: bold !important;
        padding: 7px 40px !important;
        margin-right: 20px;
    }
    .ecp_publish {
        background: #15e12d !important;
        border-radius: 5px !important;
        font-size: 16px !important;
        color: white !important;
        font-weight: bold !important;
        padding: 7px 40px !important;
    }
    table {
        border-collapse: collapse;
        width: 80%;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    td {
        text-align: center;
    }
    th {
        background-color: #f2f2f2;
    }
    .time-slot {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    :root {
        --white: #fff;
        --main: #eaedf0;
        --accent: #0099cb;
        --accent-2: #0099cb;
    }
    header {
        margin: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
    }
    .header-display {
        display: flex;
        align-items: center;
    }
    .header-display p {
        color: var(--accent);
        margin: 5px;
        font-size: 1.2rem;
        word-spacing: 0.5rem;
    }
    pre {
        padding: 10px;
        margin: 0;
        cursor: pointer;
        font-size: 1.2rem;
        color: var(--accent);
    }
    .days, .week {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        margin: auto;
        padding: 0 20px;
        justify-content: space-between;
    }
    .week div, .days div {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 3rem;
        width: 3em;
        border-radius: 100%;
    }
    .days div:hover {
        background: var(--accent-2);
        color: rgb(25, 25, 201);
        cursor: pointer;
    }
    .week div {
        opacity: 0.5;
    }
    .current-date {
        background-color: var(--accent);
        color: var(--white);
    }
    .display-selected {
        margin-bottom: 10px;
        padding: 20px 20px;
        text-align: center;
    }
    .calendar {
        border: 1px solid rgb(217, 217, 217);
    }
    .slot_checkbox {
        opacity: 1 !important;
        position: relative !important;
        left: 0px !important;
    }
    .disabled {
        color: #ccc;
        pointer-events: none;
    }
    .selected-dates-times {
        margin-top: 20px;
    }
    .selected-date {
        margin-bottom: 10px;
    }
</style>
<div class="card p_2">
    <form action="{{ route('admin.change-garbage-collection') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="border_cus">
            <div class="row">
                <div class="col-md-6">
                    <h5>Select Availability Pickup Dates</h5>
                    <div class="calendar">
                        <header>
                            <pre class="left">◀</pre>
                            <div class="header-display">
                                <p class="display">""</p>
                            </div>
                            <pre class="right">▶</pre>
                        </header>
                        <div class="week">
                            <div>Su</div>
                            <div>Mo</div>
                            <div>Tu</div>
                            <div>We</div>
                            <div>Th</div>
                            <div>Fr</div>
                            <div>Sa</div>
                        </div>
                        <div class="days"></div>
                    </div>
                    <div class="display-selected">
                        <p class="selected"></p>
                    </div>
                    <div class="selected-dates-times">
                        <h5>Selected Dates and Times</h5>
                        <div id="selectedDatesTimes"></div>
                        <button type="button" class="btn btn-primary ecp_submit">Submit Availability</button>
                    </div>
                </div>
                <div class="col-md-6" style="text-align: end;">
                    <h5>Select Availability Pickup Times</h5>
                    <table>
                        <tr>
                            <th colspan="2">Indicate availability here</th>
                            <th>
                                <input class="slot_checkbox" type="checkbox" onclick="toggleAll(this)" id="selectAll"> All
                            </th>
                        </tr>
                        <tr>
                            <td>8:00 AM</td>
                            <td>9:00 AM</td>
                            <td class="time-slot"><input class="slot_checkbox" type="checkbox" name="timeSlot" value="8:00 AM - 9:00 AM"></td>
                        </tr>
                        <tr>
                            <td>9:00 AM</td>
                            <td>10:00 AM</td>
                            <td class="time-slot"><input class="slot_checkbox" type="checkbox" name="timeSlot" value="9:00 AM - 10:00 AM"></td>
                        </tr>
                        <tr>
                            <td>10:00 AM</td>
                            <td>11:00 AM</td>
                            <td class="time-slot"><input class="slot_checkbox" type="checkbox" name="timeSlot" value="10:00 AM - 11:00 AM"></td>
                        </tr>
                        <tr>
                            <td>11:00 AM</td>
                            <td>12:00 PM</td>
                            <td class="time-slot"><input class="slot_checkbox" type="checkbox" name="timeSlot" value="11:00 AM - 12:00 PM"></td>
                        </tr>
                        <tr>
                            <td>12:00 PM</td>
                            <td>1:00 PM</td>
                            <td class="time-slot"><input class="slot_checkbox" type="checkbox" name="timeSlot" value="12:00 PM - 1:00 PM"></td>
                        </tr>
                        <tr>
                            <td>1:00 PM</td>
                            <td>2:00 PM</td>
                            <td class="time-slot"><input class="slot_checkbox" type="checkbox" name="timeSlot" value="1:00 PM - 2:00 PM"></td>
                        </tr>
                        <tr>
                            <td>2:00 PM</td>
                            <td>3:00 PM</td>
                            <td class="time-slot"><input class="slot_checkbox" type="checkbox" name="timeSlot" value="2:00 PM - 3:00 PM"></td>
                        </tr>
                        <tr>
                            <td>3:00 PM</td>
                            <td>4:00 PM</td>
                            <td class="time-slot"><input class="slot_checkbox" type="checkbox" name="timeSlot" value="3:00 PM - 4:00 PM"></td>
                        </tr>
                        <tr>
                            <td>4:00 PM</td>
                            <td>5:00 PM</td>
                            <td class="time-slot"><input class="slot_checkbox" type="checkbox" name="timeSlot" value="4:00 PM - 5:00 PM"></td>
                        </tr>
                        <tr>
                            <td>5:00 PM</td>
                            <td>6:00 PM</td>
                            <td class="time-slot"><input class="slot_checkbox" type="checkbox" name="timeSlot" value="5:00 PM - 6:00 PM"></td>
                        </tr>
                    </table>
                    <button type="button" style="margin-top: 30px;" onclick="addSelectedDate()" class="btn btn-primary ecp_publish">Confirm (Pickup)</button>
                    {{--  <button type="button" onclick="addSelectedTimes()" class="btn btn-primary ecp_submit">Add Selected Times</button>  --}}
                </div>
            </div>
           
        </div>
       
    </form>
</div>
{!! $grid !!}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarElement = document.querySelector('.calendar');
    const daysElement = calendarElement.querySelector('.days');
    const displayElement = calendarElement.querySelector('.display');
    const leftButton = calendarElement.querySelector('.left');
    const rightButton = calendarElement.querySelector('.right');
    const selectedElement = document.querySelector('.selected');

    let currentDate = new Date();

    function updateCalendar() {
        daysElement.innerHTML = '';
        displayElement.textContent = currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

        const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

        const firstDayOfWeek = firstDayOfMonth.getDay();
        const lastDayOfWeek = lastDayOfMonth.getDay();

        for (let i = 0; i < firstDayOfWeek; i++) {
            const emptyCell = document.createElement('div');
            daysElement.appendChild(emptyCell);
        }

        for (let day = 1; day <= lastDayOfMonth.getDate(); day++) {
            const dayCell = document.createElement('div');
            dayCell.textContent = day;
            dayCell.onclick = () => {
                selectedElement.textContent = new Date(currentDate.getFullYear(), currentDate.getMonth(), day).toLocaleDateString('en-US');
                document.querySelectorAll('.days div').forEach(div => div.classList.remove('current-date'));
                dayCell.classList.add('current-date');
            };
            daysElement.appendChild(dayCell);
        }

        for (let i = lastDayOfWeek + 1; i < 7; i++) {
            const emptyCell = document.createElement('div');
            daysElement.appendChild(emptyCell);
        }
    }

    leftButton.onclick = () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateCalendar();
    };

    rightButton.onclick = () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateCalendar();
    };

    updateCalendar();
});

function toggleAll(source) {
    const checkboxes = document.querySelectorAll('.slot_checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = source.checked);
}

function addSelectedDate() {
    const selectedDate = document.querySelector('.selected').textContent;
    const selectedTimes = Array.from(document.querySelectorAll('input[name="timeSlot"]:checked')).map(input => input.value);
    if (selectedDate && selectedTimes.length) {
        const selectedDatesTimesContainer = document.getElementById('selectedDatesTimes');
        const selectedDateDiv = document.createElement('div');
        selectedDateDiv.classList.add('selected-date');
        selectedDateDiv.textContent = `Date: ${selectedDate}, Times: ${selectedTimes.join(', ')}`;
        selectedDatesTimesContainer.appendChild(selectedDateDiv);
        document.querySelector('.selected').textContent = '';
        document.querySelectorAll('input[name="timeSlot"]').forEach(input => input.checked = false);
    } else {
        alert('Please select a date and at least one time slot.');
    }
}


document.querySelector('.ecp_submit').addEventListener('click', function() {
   
    const selectedDatesTimes = [];
    document.querySelectorAll('#selectedDatesTimes .selected-date').forEach(div => {
        const [datePart, timesPart] = div.textContent.split(', Times: ');
        const date = datePart.replace('Date: ', '').trim();
        const times = timesPart.split(', ').map(time => time.trim());
        selectedDatesTimes.push({ date, times });
    });

    if (selectedDatesTimes.length > 0) {
        $.ajax({
            url: '{{ route('admin.change-garbage-collection') }}', // Adjust this route to your needs
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                selectedDatesTimes: selectedDatesTimes
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('An error occurred while submitting availability.');
            }
        });
    } else {
        alert('Please select at least one date and time slot.');
    }
});
</script>
@endsection