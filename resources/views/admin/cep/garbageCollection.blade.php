@extends('admin.layout.main')
@push('stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.css" />

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/leantony/grid/css/grid.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/properties_grid.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/dhtmlxcombo.css') }}" />
@endpush


@section('content')
    <style type="text/css">
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
        .ecp_submit{
            background: #0070c0 !important;
            border-radius: 5px !important;
            font-size: 16px !important;
            color: white !important;
            font-weight: bold !important;
            padding: 7px 40px !important;
            margin-right: 20px;
        }
        .ecp_publish{
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
        td{
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
        .container {
            display: inline-block;
            background-color: var(--white);
            border-radius: 35px;
            padding: 0 1em;
            margin-top: 2em;
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
            font-size: 1.5rem;
            word-spacing: 0.5rem;
            font-weight: bold;
          }

          pre {
            padding: 10px;
            margin: 0;
            cursor: pointer;
            font-size: 1.2rem;
            color: var(--accent);
            background: none !important;
            border: none !important;
          }

          .days,
          .week {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            margin: auto;
            padding: 0 20px;
            justify-content: space-between;
          }
          .week div,
          .days div {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 4rem;
            width: 3em;
            border-radius: 100%;
          }
          .days div:hover {
            background: var(--accent-2);
            color: white;
            cursor: pointer;
          }
          .week div {
            opacity: 0.5;
          }
          .current-date {
            background-color: var(--accent);
            color: black;
          }
          .selected-date {
            background-color: var(--accent);
            color: black;
          }
          .display-selected {
            margin-bottom: 10px;
            padding: 20px 20px;
            text-align: center;
          }
          .calendar{
            border: 1px solid rgb(217, 217, 217);
          }
          [type="checkbox"]:not(:checked), [type="checkbox"]:checked{
            opacity: 1 !important;
            position: relative;
            left: 0px;
        
          }
    </style>
    <div class="card p_2" style="height: 100vh;">
        <form action="{{ route('admin.change-garbage-collection',['id' => $gc->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="border_cus">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Select Availibility Pickup Dates</h5>

                            <div class="calendar">
                              <header>
                                <pre class="left">◀</pre>

                                <div class="header-display">
                                  <p class="display"></p>
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
                            <button type="submit" onclick="send_bit(1)" class="btn btn-primary ecp_submit">Submit Availibility</button>
                    </div>
                    <div class="col-md-6">
                        <h5>Select Availibility Pickup Times</h5>
                        <table>
                            <tr>
                                <th colspan="2">Indicate availability here</th>
                                <th>
                                    <input type="checkbox" onclick="toggleAll(this)" id="selectAll"> All
                                </th>
                            </tr>
                            <tr>
                                <td>8:00 AM</td>
                                <td>9:00 AM</td>
                                <td class="time-slot"><input type="checkbox" @if ($gc->slot == '8am-9am')
                                    checked
                                @endif name="timeSlot"></td>
                            </tr>
                            <tr>
                                <td>9:00 AM</td>
                                <td>10:00 AM</td>
                                <td class="time-slot"><input type="checkbox" @if ($gc->slot == '9am-10am')
                                    checked
                                @endif name="timeSlot"></td>
                            </tr>
                            <tr>
                                <td>10:00 AM</td>
                                <td>11:00 AM</td>
                                <td class="time-slot"><input type="checkbox" @if ($gc->slot == '10am-11am')
                                    checked
                                @endif name="timeSlot"></td>
                            </tr>
                            <tr>
                                <td>11:00 AM</td>
                                <td>12:00 PM</td>
                                <td class="time-slot"><input type="checkbox" @if ($gc->slot == '11am-12pm')
                                    checked
                                @endif name="timeSlot"></td>
                            </tr>
                            <tr>
                                <td>12:00 PM</td>
                                <td>1:00 PM</td>
                                <td class="time-slot"><input type="checkbox" @if ($gc->slot == '12pm-1pm')
                                    checked
                                @endif name="timeSlot"></td>
                            </tr>
                            <tr>
                                <td>1:00 PM</td>
                                <td>2:00 PM</td>
                                <td class="time-slot"><input type="checkbox" @if ($gc->slot == '1pm-2pm')
                                    checked
                                @endif name="timeSlot"></td>
                            </tr>
                            <tr>
                                <td>2:00 PM</td>
                                <td>3:00 PM</td>
                                <td class="time-slot"><input type="checkbox" @if ($gc->slot == '2pm-3pm')
                                    checked
                                @endif name="timeSlot"></td>
                            </tr>
                            <tr>
                                <td>3:00 PM</td>
                                <td>4:00 PM</td>
                                <td class="time-slot"><input type="checkbox" @if ($gc->slot == '3pm-4pm')
                                    checked
                                @endif name="timeSlot"></td>
                            </tr>
                            <tr>
                                <td>4:00 PM</td>
                                <td>5:00 PM</td>
                                <td class="time-slot"><input type="checkbox" @if ($gc->slot == '4pm-5pm')
                                    checked
                                @endif name="timeSlot"></td>
                            </tr>
                            <tr>
                                <td>5:00 PM</td>
                                <td>6:00 PM</td>
                                <td class="time-slot"><input type="checkbox" @if ($gc->slot == '5pm-6pm')
                                    checked
                                @endif name="timeSlot"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row" style="margin-top: 30px;text-align:end;margin-right:30px;">
                   
                    <button type="submit" onclick="send_bit(2)" class="btn btn-success ecp_publish">Confirm(Pickup)</button>
                </div>
            </div>
            <input type="hidden" name="status_bit" id="setvalue" value="">
        </form>
    </div>
    <script>
        function send_bit(value){
           // console.log(value);
            document.getElementById('setvalue').value = value;
        }
    </script>
    <script>
        let display = document.querySelector(".calendar .display");
        let days = document.querySelector(".calendar .days");
        let previous = document.querySelector(".calendar .left");
        let next = document.querySelector(".calendar .right");
        let selected = document.querySelector(".calendar .selected");
        let date = new Date();
        let year = date.getFullYear();
        let month = date.getMonth();
        let selectedDates = [];
        let garbage_date = '{{ $formatted_date }}'
        function displayCalendar() {
           //console.log(garbage_date);
            days.innerHTML = ""; // Clear previous calendar
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const firstDayIndex = firstDay.getDay();
            const numberOfDays = lastDay.getDate();
            let formattedDate = date.toLocaleString("en-US", {
                month: "long",
                year: "numeric"
            });
            console.log(formattedDate)
            display.innerHTML = `${formattedDate}`;

            for (let x = 1; x <= firstDayIndex; x++) {
                const div = document.createElement("div");
                div.innerHTML = "";
                days.appendChild(div);
            }

            for (let i = 1; i <= numberOfDays; i++) {
                let div = document.createElement("div");
                let currentDate = new Date(year, month, i);
                div.dataset.date = currentDate.toDateString();
                div.innerHTML = i;

                if (currentDate.getFullYear() === new Date().getFullYear() &&
                    currentDate.getMonth() === new Date().getMonth() &&
                    currentDate.getDate() === new Date().getDate()) {
                    div.classList.add("current-date");
                }

                let garbageDate = new Date(garbage_date);
              //  console.log('currentDate',currentDate)
                //console.log('garbageDate',garbageDate)
             
                if (currentDate.getFullYear() === garbageDate.getFullYear() &&
                    currentDate.getMonth() === garbageDate.getMonth() &&
                    currentDate.getDate() === garbageDate.getDate()) {
                    div.classList.add("current-date"); // Add a custom class for the garbage date
                }
                
                
                if (selectedDates.includes(div.dataset.date)) {
                    div.classList.add("selected-date");
                }

                div.addEventListener("click", (e) => {
                    const selectedDate = e.target.dataset.date;
                    if (selectedDates.includes(selectedDate)) {
                        selectedDates = selectedDates.filter(date => date !== selectedDate);
                        e.target.classList.remove("selected-date");
                    } else {
                        selectedDates.push(selectedDate);
                        e.target.classList.add("selected-date");
                    }
                    displaySelected();
                });

                days.appendChild(div);
            }
        }

        function displaySelected() {
            selected.innerHTML = `Selected Dates: ${selectedDates.join(", ")}`;
        }

        previous.addEventListener("click", () => {
            if (month === 0) {
                month = 11;
                year -= 1;
            } else {
                month -= 1;
            }
            date.setMonth(month);
            displayCalendar();
        });

        next.addEventListener("click", () => {
            if (month === 11) {
                month = 0;
                year += 1;
            } else {
                month += 1;
            }
            date.setMonth(month);
            displayCalendar();
        });

        // Initial call to display the calendar
        displayCalendar();

    </script>
    <script>
        function toggleAll(source) {
            checkboxes = document.getElementsByName('timeSlot');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
@stop

